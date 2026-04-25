<?php
/**
 * Model Dosen
 * File: models/Dosen.php
 */

class Dosen {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Ambil semua dosen (aktif) untuk dropdown
     */
    public function getAll(bool $activeOnly = false, string $search = '', int $page = 1, int $perPage = 15): array {
        $offset = ($page - 1) * $perPage;
        $sql    = "SELECT d.*, p.nama AS nama_prodi
                   FROM dosen d
                   LEFT JOIN prodi p ON p.id = d.prodi_id
                   WHERE 1=1";
        $params = [];

        if ($activeOnly) {
            $sql .= " AND d.is_active = 1";
        }
        if ($search) {
            $sql    .= " AND (d.nama LIKE ? OR d.nidn LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY d.nama ASC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ambil dosen aktif untuk pilihan pembimbing (tanpa pagination)
     */
    public function getActiveList(): array {
        $stmt = $this->db->prepare(
            "SELECT id, nama, nidn, bidang_keahlian 
             FROM dosen WHERE is_active = 1 ORDER BY nama ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil dosen berdasarkan ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT d.*, p.nama AS nama_prodi
             FROM dosen d LEFT JOIN prodi p ON p.id = d.prodi_id
             WHERE d.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Hitung total dosen
     */
    public function count(string $search = ''): int {
        $sql    = "SELECT COUNT(*) FROM dosen WHERE 1=1";
        $params = [];
        if ($search) {
            $sql    .= " AND (nama LIKE ? OR nidn LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Buat data dosen baru
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO dosen (nama, nidn, email, prodi_id, bidang_keahlian, no_hp, is_active)
             VALUES (?, ?, ?, ?, ?, ?, 1)"
        );
        $stmt->execute([
            $data['nama'],
            $data['nidn'],
            $data['email']           ?? null,
            $data['prodi_id']        ?? null,
            $data['bidang_keahlian'] ?? null,
            $data['no_hp']           ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update data dosen
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE dosen
             SET nama=?, nidn=?, email=?, prodi_id=?, bidang_keahlian=?, no_hp=?, is_active=?
             WHERE id=?"
        );
        return $stmt->execute([
            $data['nama'],
            $data['nidn'],
            $data['email']           ?? null,
            $data['prodi_id']        ?? null,
            $data['bidang_keahlian'] ?? null,
            $data['no_hp']           ?? null,
            $data['is_active']       ?? 1,
            $id,
        ]);
    }

    /**
     * Hapus dosen
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM dosen WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
