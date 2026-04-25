<?php
/**
 * Model Pembimbing Skripsi
 * File: models/Pembimbing.php
 */

class Pembimbing {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Ambil pembimbing berdasarkan pengajuan_id
     */
    public function getByPengajuanId(int $pengajuanId): array {
        $stmt = $this->db->prepare(
            "SELECT pb.*, d.nama AS nama_dosen, d.nidn, d.email AS email_dosen,
                    d.bidang_keahlian
             FROM pembimbing pb
             JOIN dosen d ON d.id = pb.dosen_id
             WHERE pb.pengajuan_id = ?
             ORDER BY pb.urutan ASC"
        );
        $stmt->execute([$pengajuanId]);
        return $stmt->fetchAll();
    }

    /**
     * Tambahkan pembimbing ke pengajuan
     */
    public function assign(int $pengajuanId, int $dosenId, int $urutan): bool {
        // Hapus dulu jika sudah ada di urutan ini
        $this->removeByUrutan($pengajuanId, $urutan);

        $stmt = $this->db->prepare(
            "INSERT INTO pembimbing (pengajuan_id, dosen_id, urutan, assigned_at)
             VALUES (?, ?, ?, NOW())"
        );
        return $stmt->execute([$pengajuanId, $dosenId, $urutan]);
    }

    /**
     * Simpan semua pembimbing sekaligus (Dewan menentukan)
     */
    public function assignAll(int $pengajuanId, int $dosen1Id, ?int $dosen2Id = null): bool {
        $this->db->beginTransaction();
        try {
            // Hapus semua pembimbing lama
            $stmt = $this->db->prepare("DELETE FROM pembimbing WHERE pengajuan_id = ?");
            $stmt->execute([$pengajuanId]);

            // Pembimbing 1 (wajib)
            $stmt = $this->db->prepare(
                "INSERT INTO pembimbing (pengajuan_id, dosen_id, urutan, assigned_at)
                 VALUES (?, ?, 1, NOW())"
            );
            $stmt->execute([$pengajuanId, $dosen1Id]);

            // Pembimbing 2 (opsional)
            if ($dosen2Id) {
                $stmt2 = $this->db->prepare(
                    "INSERT INTO pembimbing (pengajuan_id, dosen_id, urutan, assigned_at)
                     VALUES (?, ?, 2, NOW())"
                );
                $stmt2->execute([$pengajuanId, $dosen2Id]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Hapus pembimbing berdasarkan urutan
     */
    public function removeByUrutan(int $pengajuanId, int $urutan): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM pembimbing WHERE pengajuan_id = ? AND urutan = ?"
        );
        return $stmt->execute([$pengajuanId, $urutan]);
    }

    /**
     * Hapus semua pembimbing dari pengajuan
     */
    public function removeAll(int $pengajuanId): bool {
        $stmt = $this->db->prepare("DELETE FROM pembimbing WHERE pengajuan_id = ?");
        return $stmt->execute([$pengajuanId]);
    }
}
