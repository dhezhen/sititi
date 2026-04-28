<?php
/**
 * Model Catatan Bimbingan
 */
class CatatanBimbingan {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO catatan_bimbingan (pengajuan_id, dosen_id, catatan, created_at)
             VALUES (?, ?, ?, NOW())"
        );
        return $stmt->execute([
            $data['pengajuan_id'],
            $data['dosen_id'],
            $data['catatan']
        ]);
    }

    /**
     * Cek apakah kedua calon pembimbing sudah memberikan masukan
     */
    public function hasFeedbackFromBothAdvisors(int $pengajuanId): bool {
        $db = getDB();
        // Ambil ID pembimbing 1 dan 2 dari pengajuan
        $stmt = $db->prepare("SELECT pembimbing_1_id, pembimbing_2_id FROM pengajuan_judul WHERE id = ?");
        $stmt->execute([$pengajuanId]);
        $pj = $stmt->fetch();
        
        if (!$pj) return false;
        
        // Cek apakah p1 sudah memberi catatan
        $stmt1 = $db->prepare("SELECT COUNT(*) FROM catatan_bimbingan WHERE pengajuan_id = ? AND dosen_id = ?");
        $stmt1->execute([$pengajuanId, $pj['pembimbing_1_id']]);
        $hasP1 = $stmt1->fetchColumn() > 0;
        
        // Cek apakah p2 sudah memberi catatan (jika p2 ada)
        $hasP2 = true;
        if ($pj['pembimbing_2_id']) {
            $stmt2 = $db->prepare("SELECT COUNT(*) FROM catatan_bimbingan WHERE pengajuan_id = ? AND dosen_id = ?");
            $stmt2->execute([$pengajuanId, $pj['pembimbing_2_id']]);
            $hasP2 = $stmt2->fetchColumn() > 0;
        }
        
        return $hasP1 && $hasP2;
    }

    public function getByPengajuanId(int $pengajuanId): array {
        $stmt = $this->db->prepare(
            "SELECT cb.*, d.nama as nama_dosen, u.foto as foto_dosen
             FROM catatan_bimbingan cb
             JOIN dosen d ON d.id = cb.dosen_id
             LEFT JOIN users u ON u.email = d.email
             WHERE cb.pengajuan_id = ?
             ORDER BY cb.created_at DESC"
        );
        $stmt->execute([$pengajuanId]);
        return $stmt->fetchAll();
    }
}
