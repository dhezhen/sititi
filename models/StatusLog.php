<?php
/**
 * Model Status Log - Riwayat perubahan status pengajuan
 * File: models/StatusLog.php
 */

class StatusLog {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Tambahkan log perubahan status
     */
    public function add(int $pengajuanId, string $statusLama, string $statusBaru,
                        int $userId, ?string $catatan = null): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO status_log
             (pengajuan_id, status_lama, status_baru, user_id, catatan, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        return $stmt->execute([$pengajuanId, $statusLama, $statusBaru, $userId, $catatan]);
    }

    /**
     * Ambil semua log berdasarkan pengajuan (untuk timeline)
     */
    public function getByPengajuanId(int $pengajuanId): array {
        $stmt = $this->db->prepare(
            "SELECT sl.*, u.nama AS nama_user, u.role
             FROM status_log sl
             JOIN users u ON u.id = sl.user_id
             WHERE sl.pengajuan_id = ?
             ORDER BY sl.created_at ASC"
        );
        $stmt->execute([$pengajuanId]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil keputusan terakhir dewan tertentu untuk pengajuan tertentu
     */
    public function getLastDecisionByDewan(int $pengajuanId, int $userId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM status_log 
             WHERE pengajuan_id = ? AND user_id = ? 
             ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute([$pengajuanId, $userId]);
        return $stmt->fetch() ?: null;
    }
}
