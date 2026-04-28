<?php
/**
 * Model Notifikasi
 * File: models/Notifikasi.php
 */

class Notifikasi {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Kirim notifikasi ke user tertentu
     */
    public function send(int $userId, string $pesan, ?int $pengajuanId = null): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO notifikasi (user_id, pengajuan_id, pesan, is_read, created_at)
             VALUES (?, ?, ?, 0, NOW())"
        );
        return $stmt->execute([$userId, $pengajuanId, $pesan]);
    }

    /**
     * Kirim ke semua user dengan role tertentu
     */
    public function sendToRole(string $role, string $pesan, ?int $pengajuanId = null): bool {
        $stmt    = $this->db->prepare("SELECT id FROM users WHERE role = ? AND is_active = 1");
        $stmt->execute([$role]);
        $users   = $stmt->fetchAll();

        $insert  = $this->db->prepare(
            "INSERT INTO notifikasi (user_id, pengajuan_id, pesan, is_read, created_at)
             VALUES (?, ?, ?, 0, NOW())"
        );
        foreach ($users as $user) {
            $insert->execute([$user['id'], $pengajuanId, $pesan]);
        }
        return true;
    }

    /**
     * Ambil notifikasi user (yang belum dibaca duluan)
     */
    public function getByUserId(int $userId, int $limit = 20): array {
        if (!$userId) return [];
        $stmt = $this->db->prepare(
            "SELECT n.*, pj.judul AS judul_pengajuan
             FROM notifikasi n
             LEFT JOIN pengajuan_judul pj ON pj.id = n.pengajuan_id
             WHERE n.user_id = ?
             ORDER BY n.is_read ASC, n.created_at DESC
             LIMIT $limit"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Hitung notifikasi belum dibaca
     */
    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_read = 0"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca
     */
    public function markRead(int $id, int $userId): bool {
        $stmt = $this->db->prepare(
            "UPDATE notifikasi SET is_read = 1 WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca
     */
    public function markAllRead(int $userId): bool {
        $stmt = $this->db->prepare(
            "UPDATE notifikasi SET is_read = 1 WHERE user_id = ? AND is_read = 0"
        );
        return $stmt->execute([$userId]);
    }
}
