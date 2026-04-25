<?php
/**
 * Model User
 * File: models/User.php
 */

class User {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Cari user berdasarkan email (untuk login)
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare(
            "SELECT id, nama, email, password, role, is_active 
             FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Cari user berdasarkan ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT id, nama, email, role, is_active, created_at 
             FROM users WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Verifikasi password login
     */
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * Ambil semua user dengan filter opsional
     */
    public function getAll(?string $role = null, int $page = 1, int $perPage = 15): array {
        $offset = ($page - 1) * $perPage;
        $sql    = "SELECT id, nama, email, role, is_active, created_at FROM users";
        $params = [];

        if ($role) {
            $sql    .= " WHERE role = ?";
            $params[] = $role;
        }

        $sql .= " ORDER BY nama ASC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Hitung total user (untuk pagination)
     */
    public function count(?string $role = null): int {
        $sql    = "SELECT COUNT(*) FROM users";
        $params = [];
        if ($role) {
            $sql    .= " WHERE role = ?";
            $params[] = $role;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Buat user baru
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO users (nama, email, password, role, is_active, created_at)
             VALUES (?, ?, ?, ?, 1, NOW())"
        );
        $stmt->execute([
            $data['nama'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update data user
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];

        if (!empty($data['nama'])) {
            $fields[] = 'nama = ?';
            $params[] = $data['nama'];
        }
        if (!empty($data['email'])) {
            $fields[] = 'email = ?';
            $params[] = $data['email'];
        }
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = ?';
            $params[] = (int)$data['is_active'];
        }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql      = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt     = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Hapus user
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Cek apakah email sudah digunakan
     */
    public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql    = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];
        if ($excludeId) {
            $sql    .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
