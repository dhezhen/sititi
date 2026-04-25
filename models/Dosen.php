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
        $sql    = "SELECT d.*, p.nama AS nama_prodi, u.role AS user_role
                   FROM dosen d
                   LEFT JOIN prodi p ON p.id = d.prodi_id
                   LEFT JOIN users u ON u.email = d.email
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
    public function getActiveList(?int $angkatan = null): array {
        $params = [];
        $sql = "SELECT id, nama, nidn, bidang_keahlian, jabatan_akademik";
        if ($angkatan) {
            $sql .= ", (SELECT COUNT(*) FROM pembimbing pb 
                        JOIN pengajuan_judul pj ON pj.id = pb.pengajuan_id 
                        JOIN mahasiswa m ON m.user_id = pj.mahasiswa_id 
                        WHERE pb.dosen_id = dosen.id AND m.angkatan = ?) as workload";
            $params[] = $angkatan;
        }
        $sql .= " FROM dosen WHERE is_active = 1 ORDER BY nama ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getJabatanAkademikOptions(): array {
        return [
            'Asisten Ahli' => 'Asisten Ahli',
            'Lektor' => 'Lektor',
            'Lektor Kepala' => 'Lektor Kepala',
            'Guru Besar (professor)' => 'Guru Besar (Professor)'
        ];
    }
    public function getBidangKeahlianOptions(): array { 
        return [
            'Pemrograman Web' => 'Pemrograman Web',
            'Data Sains'        => 'Data Sains',
            'Pemrograman Mobile' => 'Pemrograman Mobile',
            'Jaringan Komputer' => 'Jaringan Komputer',
            'Kecerdasan Buatan' => 'Kecerdasan Buatan',
            'Pemmrograman Game' => 'Pemmrograman Game',
            'Computer Vision'  => 'Computer Vision',
            'Analisis Data'     => 'Analisis Data',
            'Cyber Security' => 'Cyber Security',
            'rekayasa Perangkat Lunak' => 'Rekayasa Perangkat Lunak',
            'Keamanan Sistem' => 'Keamanan Sistem',
            'augmented Reality' => 'Augmented Reality',
            'cloud computing' => 'Cloud Computing',
            'machine learning' => 'Machine Learning',
            'Virtual Reality' => 'Virtual Reality',
            'Internet of Things' => 'Internet of Things',
            'Lainnya' => 'Lainnya'
        ];
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
            "INSERT INTO dosen (nama, nidn, email, prodi_id, bidang_keahlian, no_hp, jabatan_akademik, pendidikan, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)"
        );
        $stmt->execute([
            $data['nama'],
            $data['nidn'],
            $data['email']           ?? null,
            $data['prodi_id']        ?? null,
            $data['bidang_keahlian'] ?? null,
            $data['no_hp']           ?? null,
            $data['jabatan_akademik'] ?? null,
            $data['pendidikan']      ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }


    /**
     * Update data dosen
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE dosen
             SET nama=?, nidn=?, email=?, prodi_id=?, bidang_keahlian=?, no_hp=?, jabatan_akademik=?, pendidikan=?, is_active=?
             WHERE id=?"
        );
        return $stmt->execute([
            $data['nama'],
            $data['nidn'],
            $data['email']           ?? null,
            $data['prodi_id']        ?? null,
            $data['bidang_keahlian'] ?? null,
            $data['no_hp']           ?? null,
            $data['jabatan_akademik'] ?? null,
            $data['pendidikan']      ?? null,
            $data['is_active']       ?? 1,
            $id,
        ]);
    }

    /**
     * Ambil jumlah bimbingan dosen per angkatan
     */
    public function getWorkloadByAngkatan(int $dosenId): array {
        $sql = "SELECT m.angkatan, COUNT(*) as total
                FROM pembimbing p
                JOIN pengajuan_judul pj ON pj.id = p.pengajuan_id
                JOIN mahasiswa m ON m.user_id = pj.mahasiswa_id
                WHERE p.dosen_id = ?
                GROUP BY m.angkatan
                ORDER BY m.angkatan DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dosenId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil beban kerja seluruh dosen
     */
    public function getAllWorkload(?int $prodiId = null): array {
        $sql = "SELECT d.id, d.nama, d.nidn, d.jabatan_akademik,
                       m.angkatan, COUNT(pb.id) as total
                FROM dosen d
                LEFT JOIN pembimbing pb ON pb.dosen_id = d.id
                LEFT JOIN pengajuan_judul pj ON pj.id = pb.pengajuan_id
                LEFT JOIN mahasiswa m ON m.user_id = pj.mahasiswa_id
                WHERE d.is_active = 1";
        
        $params = [];
        if ($prodiId) {
            $sql .= " AND d.prodi_id = ?";
            $params[] = $prodiId;
        }
        
        $sql .= " GROUP BY d.id, m.angkatan
                  ORDER BY d.nama ASC, m.angkatan DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hapus dosen
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM dosen WHERE id = ?");
        return $stmt->execute([$id]);
    }

}
