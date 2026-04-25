<?php
/**
 * Model Periode
 * File: models/Periode.php
 */

class Periode {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Ambil semua periode
     */
    public function getAll(int $page = 1, int $perPage = 15): array {
        $offset = ($page - 1) * $perPage;
        $sql    = "SELECT * FROM periode ORDER BY id DESC LIMIT $perPage OFFSET $offset";
        $stmt   = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Hitung total periode
     */
    public function count(): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM periode");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Ambil periode berdasarkan ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM periode WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Set periode aktif (hanya 1 yang bisa aktif)
     */
    public function setActive(int $id): bool {
        try {
            $this->db->beginTransaction();
            // Nonaktifkan semua
            $this->db->exec("UPDATE periode SET is_active = 0");
            // Aktifkan yang dipilih
            $stmt = $this->db->prepare("UPDATE periode SET is_active = 1 WHERE id = ?");
            $stmt->execute([$id]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Buat periode baru
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO periode (nama, tanggal_mulai, tanggal_selesai, is_active, created_at)
             VALUES (?, ?, ?, ?, NOW())"
        );
        $isActive = !empty($data['is_active']) ? 1 : 0;
        
        $stmt->execute([
            $data['nama'],
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $isActive
        ]);
        
        $id = (int)$this->db->lastInsertId();
        if ($isActive) {
            $this->setActive($id);
        }
        return $id;
    }

    /**
     * Update periode
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE periode SET nama = ?, tanggal_mulai = ?, tanggal_selesai = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['nama'],
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $id
        ]);
    }

    /**
     * Hapus periode
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM periode WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
