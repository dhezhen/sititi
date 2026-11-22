<?php

class Pengumuman {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getRoles(): array {
        return $this->db->query("SELECT kode, nama FROM roles ORDER BY nama ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecipients(array $roles = []): array {
        $sql = "SELECT u.id, u.nama, u.email
                FROM users u
                WHERE u.is_active = 1";
        $params = [];

        if (!empty($roles)) {
            $placeholders = implode(',', array_fill(0, count($roles), '?'));
            $sql .= " AND (
                EXISTS (
                    SELECT 1 FROM user_roles ur
                    WHERE ur.user_id = u.id AND ur.role_kode IN ($placeholders)
                )
                OR (
                    NOT EXISTS (SELECT 1 FROM user_roles ur2 WHERE ur2.user_id = u.id)
                    AND u.role IN ($placeholders)
                )
            )";
            $params = array_merge($roles, $roles);
        }

        $sql .= ' ORDER BY u.nama ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createAndNotify(
        string $title,
        string $message,
        array $roles,
        int $senderId,
        bool $sendEmail,
        array $recipients
    ): int {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO pengumuman
                    (judul, isi, target_roles, dibuat_oleh, penerima_count, email_diminta, email_status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $title,
                $message,
                empty($roles) ? null : json_encode(array_values($roles), JSON_UNESCAPED_UNICODE),
                $senderId,
                count($recipients),
                $sendEmail ? 1 : 0,
                $sendEmail ? 'menunggu' : 'tidak_diminta'
            ]);
            $announcementId = (int)$this->db->lastInsertId();

            $notification = $this->db->prepare(
                "INSERT INTO notifikasi (user_id, pengajuan_id, pesan, is_read, created_at)
                 VALUES (?, NULL, ?, 0, NOW())"
            );
            $notificationText = $title . "\n\n" . $message;
            foreach ($recipients as $recipient) {
                $notification->execute([(int)$recipient['id'], $notificationText]);
            }

            $this->db->commit();
            return $announcementId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function updateEmailResult(int $id, string $status, int $accepted, int $failed): void {
        $stmt = $this->db->prepare(
            "UPDATE pengumuman
             SET email_status = ?, email_diterima = ?, email_gagal = ?
             WHERE id = ?"
        );
        $stmt->execute([$status, $accepted, $failed, $id]);
    }

    public function getRecent(int $limit = 30): array {
        $limit = max(1, min($limit, 100));
        $stmt = $this->db->query(
            "SELECT p.*, u.nama AS nama_pengirim
             FROM pengumuman p
             LEFT JOIN users u ON u.id = p.dibuat_oleh
             ORDER BY p.created_at DESC
             LIMIT $limit"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
