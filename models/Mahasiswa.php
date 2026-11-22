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
    public function getAll(string $search = '', int $page = 1, int $perPage = 15, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $sql    = "SELECT m.*, u.nama, u.email, u.is_active, p.nama AS nama_prodi
                   FROM mahasiswa m
                   JOIN users u ON u.id = m.user_id
                   LEFT JOIN prodi p ON p.id = m.prodi_id";
        [$where, $params] = $this->buildListFilters($search, $filters);
        if ($where !== '') $sql .= ' WHERE ' . $where;

        $sql .= " ORDER BY u.nama ASC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Hitung total mahasiswa
     */
    public function count(string $search = '', array $filters = []): int {
        $sql    = "SELECT COUNT(*) FROM mahasiswa m JOIN users u ON u.id = m.user_id";
        [$where, $params] = $this->buildListFilters($search, $filters);
        if ($where !== '') $sql .= ' WHERE ' . $where;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function buildListFilters(string $search, array $filters): array {
        $conditions = [];
        $params = [];
        if ($search !== '') {
            $conditions[] = '(u.nama LIKE ? OR m.nim LIKE ? OR u.email LIKE ? OR m.no_hp LIKE ?)';
            $searchTerm = '%' . $search . '%';
            array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        }
        if (!empty($filters['prodi_id'])) {
            $conditions[] = 'm.prodi_id = ?';
            $params[] = (int)$filters['prodi_id'];
        }
        if (!empty($filters['angkatan'])) {
            $conditions[] = 'm.angkatan = ?';
            $params[] = (int)$filters['angkatan'];
        }
        if (isset($filters['is_active']) && $filters['is_active'] !== null) {
            $conditions[] = 'u.is_active = ?';
            $params[] = (int)$filters['is_active'];
        }
        return [implode(' AND ', $conditions), $params];
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

    /**
     * Hapus profil mahasiswa
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM mahasiswa WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
