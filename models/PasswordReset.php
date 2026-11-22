<?php

class PasswordReset {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function findActiveUserByEmail(string $email): ?array {
        $stmt = $this->db->prepare(
            'SELECT id, nama, email FROM users WHERE email = ? AND is_active = 1 LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function recordRequest(string $emailHash, string $ip, ?int $userId, ?string $tokenHash): ?int {
        $ip = substr($ip, 0, 45);
        $stmt = $this->db->prepare(
            'SELECT
                SUM(requested_ip = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)) AS ip_count,
                SUM(email_hash = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)) AS email_count
             FROM password_reset_tokens'
        );
        $stmt->execute([$ip, $emailHash]);
        $limits = $stmt->fetch();

        if ((int)$limits['ip_count'] >= 3 || (int)$limits['email_count'] >= 1) {
            return null;
        }

        $this->db->prepare(
            'DELETE FROM password_reset_tokens WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)'
        )->execute();

        $stmt = $this->db->prepare(
            'INSERT INTO password_reset_tokens
                (user_id, email_hash, token_hash, requested_ip, expires_at)
             VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))'
        );
        $stmt->execute([$userId, $emailHash, $tokenHash, $ip]);
        return (int)$this->db->lastInsertId();
    }

    public function isTokenValid(string $tokenHash): bool {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM password_reset_tokens
             WHERE token_hash = ? AND user_id IS NOT NULL AND used_at IS NULL AND expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute([$tokenHash]);
        return (bool)$stmt->fetchColumn();
    }

    public function resetPassword(string $tokenHash, string $password): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'SELECT id, user_id FROM password_reset_tokens
                 WHERE token_hash = ? AND user_id IS NOT NULL AND used_at IS NULL AND expires_at > NOW()
                 LIMIT 1 FOR UPDATE'
            );
            $stmt->execute([$tokenHash]);
            $reset = $stmt->fetch();
            if (!$reset) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ? AND is_active = 1');
            $stmt->execute([password_hash($password, PASSWORD_BCRYPT), (int)$reset['user_id']]);
            if ($stmt->rowCount() !== 1) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare(
                'UPDATE password_reset_tokens SET used_at = NOW()
                 WHERE user_id = ? AND used_at IS NULL'
            );
            $stmt->execute([(int)$reset['user_id']]);
            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }
}
