<?php
/**
 * Model Pengajuan Judul Skripsi
 * File: models/Pengajuan.php
 */

class Pengajuan {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Ambil pengajuan berdasarkan ID (dengan join semua tabel terkait)
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT pj.*,
                    u.nama AS nama_mahasiswa, u.email AS email_mahasiswa,
                    m.nim, m.angkatan, m.semester,
                    pr.nama AS nama_prodi,
                    per.nama AS nama_periode
             FROM pengajuan_judul pj
             JOIN users u   ON u.id = pj.user_id
             JOIN mahasiswa m ON m.user_id = pj.user_id
             LEFT JOIN prodi pr    ON pr.id = m.prodi_id
             LEFT JOIN periode per ON per.id = pj.periode_id
             WHERE pj.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Ambil semua pengajuan milik satu mahasiswa
     */
    public function getByUserId(int $userId, int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $stmt   = $this->db->prepare(
            "SELECT pj.*, per.nama AS nama_periode
             FROM pengajuan_judul pj
             LEFT JOIN periode per ON per.id = pj.periode_id
             WHERE pj.user_id = ?
             ORDER BY pj.created_at DESC
             LIMIT $perPage OFFSET $offset"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Hitung pengajuan milik mahasiswa
     */
    public function countByUserId(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM pengajuan_judul WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Ambil semua pengajuan dengan filter & pagination (untuk Staff/Kaprodi/Dewan)
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 15): array {
        $offset = ($page - 1) * $perPage;
        $sql    = "SELECT pj.*,
                          u.nama AS nama_mahasiswa,
                          m.nim, pr.nama AS nama_prodi,
                          per.nama AS nama_periode
                   FROM pengajuan_judul pj
                   JOIN users u   ON u.id = pj.user_id
                   JOIN mahasiswa m ON m.user_id = pj.user_id
                   LEFT JOIN prodi pr    ON pr.id = m.prodi_id
                   LEFT JOIN periode per ON per.id = pj.periode_id
                   WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql    .= " AND pj.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql    .= " AND (u.nama LIKE ? OR pj.judul LIKE ? OR m.nim LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (!empty($filters['periode_id'])) {
            $sql    .= " AND pj.periode_id = ?";
            $params[] = $filters['periode_id'];
        }

        $sql .= " ORDER BY pj.created_at DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Hitung total pengajuan (untuk pagination)
     */
    public function count(array $filters = []): int {
        $sql    = "SELECT COUNT(*) FROM pengajuan_judul pj
                   JOIN users u ON u.id = pj.user_id
                   JOIN mahasiswa m ON m.user_id = pj.user_id
                   WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql    .= " AND pj.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql    .= " AND (u.nama LIKE ? OR pj.judul LIKE ? OR m.nim LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Buat pengajuan baru (status awal: draft)
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO pengajuan_judul
             (user_id, periode_id, judul, deskripsi, bidang, file_proposal, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, 'draft', NOW(), NOW())"
        );
        $stmt->execute([
            $data['user_id'],
            $data['periode_id']     ?? null,
            $data['judul'],
            $data['deskripsi']      ?? null,
            $data['bidang']         ?? null,
            $data['file_proposal']  ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update pengajuan (hanya bisa jika status draft/revisi)
     */
    public function update(int $id, array $data): bool {
        $fields = ['judul=?', 'deskripsi=?', 'bidang=?', 'updated_at=NOW()'];
        $params = [$data['judul'], $data['deskripsi'], $data['bidang']];

        if (!empty($data['file_proposal'])) {
            $fields[] = 'file_proposal=?';
            $params[] = $data['file_proposal'];
        }

        $params[] = $id;
        $sql      = "UPDATE pengajuan_judul SET " . implode(', ', $fields) . " WHERE id=?";
        $stmt     = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update status pengajuan
     */
    public function updateStatus(int $id, string $status, ?string $catatan = null): bool {
        $stmt = $this->db->prepare(
            "UPDATE pengajuan_judul
             SET status=?, catatan_terakhir=?, updated_at=NOW()
             WHERE id=?"
        );
        return $stmt->execute([$status, $catatan, $id]);
    }

    /**
     * Submit pengajuan dari draft ke diajukan
     */
    public function submit(int $id): bool {
        return $this->updateStatus($id, STATUS_DIAJUKAN);
    }

    /**
     * Verifikasi oleh Staff
     */
    public function verifikasi(int $id, bool $lengkap, string $catatan = ''): bool {
        $status = $lengkap ? STATUS_DIVERIFIKASI : STATUS_REVISI;
        $stmt   = $this->db->prepare(
            "UPDATE pengajuan_judul
             SET status=?, catatan_terakhir=?, verified_at=NOW(), updated_at=NOW()
             WHERE id=?"
        );
        return $stmt->execute([$status, $catatan, $id]);
    }

    /**
     * Hapus pengajuan (hanya status draft)
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM pengajuan_judul WHERE id=? AND status='draft'");
        return $stmt->execute([$id]);
    }

    /**
     * Statistik status pengajuan (untuk dashboard)
     */
    public function getStats(?int $userId = null): array {
        $sql    = "SELECT status, COUNT(*) as total FROM pengajuan_judul WHERE 1=1";
        $params = [];
        if ($userId) {
            $sql    .= " AND user_id = ?";
            $params[] = $userId;
        }
        $sql .= " GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows  = $stmt->fetchAll();
        $stats = [];
        foreach ($rows as $row) {
            $stats[$row['status']] = (int)$row['total'];
        }
        return $stats;
    }

    /**
     * Pengajuan terbaru (untuk dashboard)
     */
    public function getRecent(int $limit = 5, ?string $status = null): array {
        $sql    = "SELECT pj.*, u.nama AS nama_mahasiswa, m.nim
                   FROM pengajuan_judul pj
                   JOIN users u ON u.id = pj.user_id
                   JOIN mahasiswa m ON m.user_id = pj.user_id
                   WHERE 1=1";
        $params = [];
        if ($status) {
            $sql    .= " AND pj.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY pj.updated_at DESC LIMIT $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
