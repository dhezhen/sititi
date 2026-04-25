<?php
/**
 * Model Mahasiswa
 * File: models/Mahasiswa.php
 */

class Mahasiswa {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Ambil profil mahasiswa berdasarkan user_id
     */
    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.nama, u.email, u.is_active,
                    p.nama AS nama_prodi
             FROM mahasiswa m
             JOIN users u ON u.id = m.user_id
             LEFT JOIN prodi p ON p.id = m.prodi_id
             WHERE m.user_id = ? LIMIT 1"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Ambil mahasiswa berdasarkan ID mahasiswa
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.nama, u.email,
                    p.nama AS nama_prodi
             FROM mahasiswa m
             JOIN users u ON u.id = m.user_id
             LEFT JOIN prodi p ON p.id = m.prodi_id
             WHERE m.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Ambil semua mahasiswa dengan pagination & search
     */
    public function getAll(string $search = '', int $page = 1, int $perPage = 15): array {
        $offset = ($page - 1) * $perPage;
        $sql    = "SELECT m.*, u.nama, u.email, u.is_active, p.nama AS nama_prodi
                   FROM mahasiswa m
                   JOIN users u ON u.id = m.user_id
                   LEFT JOIN prodi p ON p.id = m.prodi_id";
        $params = [];

        if ($search) {
            $sql    .= " WHERE u.nama LIKE ? OR m.nim LIKE ?";
            $params  = ["%$search%", "%$search%"];
        }

        $sql .= " ORDER BY u.nama ASC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Hitung total mahasiswa
     */
    public function count(string $search = ''): int {
        $sql    = "SELECT COUNT(*) FROM mahasiswa m JOIN users u ON u.id = m.user_id";
        $params = [];
        if ($search) {
            $sql    .= " WHERE u.nama LIKE ? OR m.nim LIKE ?";
            $params  = ["%$search%", "%$search%"];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Buat profil mahasiswa baru
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO mahasiswa (user_id, nim, angkatan, semester, prodi_id, no_hp, alamat)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['nim'],
            $data['angkatan'],
            $data['semester']  ?? null,
            $data['prodi_id']  ?? null,
            $data['no_hp']     ?? null,
            $data['alamat']    ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update profil mahasiswa
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE mahasiswa 
             SET nim=?, angkatan=?, semester=?, prodi_id=?, no_hp=?, alamat=?
             WHERE id=?"
        );
        return $stmt->execute([
            $data['nim'],
            $data['angkatan'],
            $data['semester']  ?? null,
            $data['prodi_id']  ?? null,
            $data['no_hp']     ?? null,
            $data['alamat']    ?? null,
            $id,
        ]);
    }
}
