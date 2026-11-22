<?php
/**
 * Dashboard Controller - Redirect ke view sesuai role
 * File: controllers/DashboardController.php
 */

class DashboardController {

    /**
     * Tampilkan dashboard sesuai role user yang login
     */
    public function index(): void {
        Auth::requireLogin();

        $role = Auth::role();

        switch ($role) {
            case ROLE_MAHASISWA:
                $this->mahasiswaDashboard();
                break;
            case ROLE_STAFF:
                $this->staffDashboard();
                break;
            case ROLE_KAPRODI:
                $this->kaprodiDashboard();
                break;
            case ROLE_DEWAN:
                $this->dewanDashboard();
                break;
            case ROLE_DOSEN:
                $this->dosenDashboard();
                break;
            case ROLE_SUPERADMIN:
                $this->superadminDashboard();
                break;
            default:
                redirect('?page=403');
        }
    }

    /**
     * Dashboard Dosen (Pembimbing saja)
     */
    private function dosenDashboard(): void {
        $pembimbingModel = new Pembimbing();
        $notifModel      = new Notifikasi();
        
        $userId   = Auth::id();
        $notifasi = $notifModel->getByUserId($userId, 5);
        
        // Ambil mahasiswa bimbingan
        $db = getDB();
        $userEmail = Auth::user()['email'];
        $stmtD = $db->prepare("SELECT id FROM dosen WHERE email = ? LIMIT 1");
        $stmtD->execute([$userEmail]);
        $dosenId = $stmtD->fetchColumn();
        
        $bimbingan = [];
        $workload = [];
        if ($dosenId) {
            $dosenModel = new Dosen();
            $workload = $dosenModel->getWorkloadByAngkatan($dosenId);
            
            $stmtB = $db->prepare("
                SELECT p.*, u.nama AS nama_mahasiswa, m.nim, 
                       CASE WHEN p.pembimbing_1_id = ? THEN 1 ELSE 2 END as urutan
                FROM pengajuan_judul p
                JOIN mahasiswa m ON m.user_id = p.user_id
                JOIN users u ON u.id = m.user_id
                WHERE (p.pembimbing_1_id = ? OR p.pembimbing_2_id = ?)
                ORDER BY p.created_at DESC
            ");
            $stmtB->execute([$dosenId, $dosenId, $dosenId]);
            $bimbingan = $stmtB->fetchAll(PDO::FETCH_ASSOC);
        }

        require BASE_PATH . '/views/dashboard/dosen.php';
    }

    /**
     * Dashboard Mahasiswa
     */
    private function mahasiswaDashboard(): void {
        $pengajuanModel  = new Pengajuan();
        $notifModel      = new Notifikasi();
        $mahasiswaModel  = new Mahasiswa();

        $userId   = Auth::id();
        $profil   = $mahasiswaModel->findByUserId($userId);
        $stats    = $pengajuanModel->getStats($userId);
        $riwayat  = $pengajuanModel->getByUserId($userId, 1, 5);
        $notifasi = $notifModel->getByUserId($userId, 5);
        $hasActive = $pengajuanModel->hasActiveSubmission($userId);

        $dosenModel = new Dosen();
        $listDosen  = $dosenModel->getAllWithWorkload();

        // Ambil pembimbing resmi jika ada (untuk pengajuan yang sudah disetujui/selesai fakultas)
        $pembimbing = [];
        $activeSubmission = $pengajuanModel->getActiveSubmission($userId);
        if ($activeSubmission) {
            $pbModel = new Pembimbing();
            $pembimbing = $pbModel->getByPengajuanId($activeSubmission['id']);
        }

        require BASE_PATH . '/views/dashboard/mahasiswa.php';
    }

    /**
     * Dashboard Staff Akademik
     */
    private function staffDashboard(): void {
        $pengajuanModel = new Pengajuan();
        $notifModel     = new Notifikasi();

        $diajukan    = $pengajuanModel->count(['status' => STATUS_DIAJUKAN]);
        $diverifikasi = $pengajuanModel->count(['status' => STATUS_DIVERIFIKASI]);
        $siapFakultasCount = $pengajuanModel->count(['status' => [STATUS_PEMBIMBING, STATUS_SELESAI, STATUS_SELESAI_FAKULTAS]]);
        $skTerbitCount = $pengajuanModel->count(['status' => STATUS_SK_TERBIT]);
        
        $recent      = $pengajuanModel->getRecent(5, STATUS_DIAJUKAN);
        $siapFakultas = $pengajuanModel->getRecent(5, [STATUS_PEMBIMBING, STATUS_SELESAI, STATUS_SELESAI_FAKULTAS]);
        $recentSK    = $pengajuanModel->getRecent(5, STATUS_SK_TERBIT);
        $notifasi    = (new Notifikasi())->getByUserId(Auth::id(), 5);

        // Statistik Pengajuan per Angkatan
        $db = getDB();
        $angkatanStats = $db->query("
            SELECT 
                m.angkatan,
                COUNT(DISTINCT m.id) AS total_mahasiswa,
                COUNT(DISTINCT p.id) AS sudah_mengajukan,
                COUNT(DISTINCT CASE WHEN p.status NOT IN ('ditolak', 'draft') THEN p.id END) AS pengajuan_aktif
            FROM mahasiswa m
            LEFT JOIN pengajuan_judul p ON m.user_id = p.user_id
            GROUP BY m.angkatan
            ORDER BY m.angkatan DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Statistik Beban Kerja Dosen
        $dosenModel = new Dosen();
        $workloadDosen = $dosenModel->getAllWorkloadGrouped();

        require BASE_PATH . '/views/dashboard/staff.php';
    }

    /**
     * Dashboard Kaprodi
     */
    private function kaprodiDashboard(): void {
        $pengajuanModel = new Pengajuan();

        $diverifikasi = $pengajuanModel->count(['status' => STATUS_DIVERIFIKASI]);
        $direview     = $pengajuanModel->count(['status' => STATUS_DIREVIEW]);
        $siapAccCount = $pengajuanModel->count(['status' => STATUS_DIREVIEW_DEWAN]);
        $disetujui    = $pengajuanModel->count(['status' => STATUS_DISETUJUI]);
        $ditolak      = $pengajuanModel->count(['status' => STATUS_DITOLAK]);
        
        $recent       = $pengajuanModel->getRecent(10, STATUS_DIVERIFIKASI);
        $siapAcc      = $pengajuanModel->getRecent(10, STATUS_DIREVIEW_DEWAN);
        $notifasi     = (new Notifikasi())->getByUserId(Auth::id(), 5);

        // Statistik Pengajuan per Angkatan (Khusus Prodi Kaprodi)
        $db = getDB();
        $userEmail = Auth::user()['email'];
        $stmtK = $db->prepare("SELECT prodi_id FROM dosen WHERE email = ? LIMIT 1");
        $stmtK->execute([$userEmail]);
        $prodiId = $stmtK->fetchColumn();

        $angkatanStats = [];
        if ($prodiId) {
            $stmtS = $db->prepare("
                SELECT 
                    m.angkatan,
                    COUNT(DISTINCT m.id) AS total_mahasiswa,
                    COUNT(DISTINCT p.id) AS sudah_mengajukan,
                    COUNT(DISTINCT CASE WHEN p.status NOT IN ('ditolak', 'draft') THEN p.id END) AS pengajuan_aktif
                FROM mahasiswa m
                LEFT JOIN pengajuan_judul p ON m.user_id = p.user_id
                WHERE m.prodi_id = ?
                GROUP BY m.angkatan
                ORDER BY m.angkatan DESC
            ");
            $stmtS->execute([$prodiId]);
            $angkatanStats = $stmtS->fetchAll(PDO::FETCH_ASSOC);

            // Statistik Bidang/Topik
            $stmtB = $db->prepare("
                SELECT bidang, COUNT(*) as jumlah 
                FROM pengajuan_judul p 
                JOIN mahasiswa m ON m.user_id = p.user_id 
                WHERE m.prodi_id = ? 
                GROUP BY bidang 
                ORDER BY jumlah DESC
            ");
            $stmtB->execute([$prodiId]);
            $bidangStats = $stmtB->fetchAll(PDO::FETCH_ASSOC);

            // Statistik Pengajuan per Bulan (6 Bulan Terakhir)
            $stmtT = $db->prepare("
                SELECT DATE_FORMAT(p.created_at, '%Y-%m') as bulan, COUNT(*) as jumlah 
                FROM pengajuan_judul p 
                JOIN mahasiswa m ON m.user_id = p.user_id 
                WHERE m.prodi_id = ? AND p.created_at >= DATE_SUB(NOW(), INTERVAL 5 MONTH) 
                GROUP BY bulan 
                ORDER BY bulan ASC
            ");
            $stmtT->execute([$prodiId]);
            $trendStats = $stmtT->fetchAll(PDO::FETCH_ASSOC);
        }

        // Statistik Beban Kerja Dosen
        $dosenModel = new Dosen();
        $workloadDosen = $dosenModel->getAllWorkloadGrouped();

        require BASE_PATH . '/views/dashboard/kaprodi.php';
    }

    /**
     * Dashboard Komisi Tugas Akhir
     */
    private function dewanDashboard(): void {
        $pengajuanModel = new Pengajuan();
        $dewanId = Auth::id();

        $perluReview = $pengajuanModel->countPendingReview($dewanId);
        $telahReview = $pengajuanModel->countCompletedReview($dewanId);
        $totalProses = $pengajuanModel->count(['status' => STATUS_PEMBIMBING, 'dewan_id' => $dewanId]);
        
        $recent   = $pengajuanModel->getPendingReview($dewanId, 5);
        $notifasi = (new Notifikasi())->getByUserId($dewanId, 5);

        require BASE_PATH . '/views/dashboard/dewan.php';
    }

    /**
     * Dashboard Super Admin
     */
    private function superadminDashboard(): void {
        $db = getDB();
        $stats = [
            'users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'roles' => $db->query("SELECT COUNT(*) FROM roles")->fetchColumn(),
            'menus' => $db->query("SELECT COUNT(*) FROM menus")->fetchColumn(),
        ];
        
        // Statistik Pengajuan per Angkatan
        $angkatanStats = $db->query("
            SELECT 
                m.angkatan,
                COUNT(DISTINCT m.id) AS total_mahasiswa,
                COUNT(DISTINCT p.id) AS sudah_mengajukan,
                COUNT(DISTINCT CASE WHEN p.status NOT IN ('ditolak', 'draft') THEN p.id END) AS pengajuan_aktif
            FROM mahasiswa m
            LEFT JOIN pengajuan_judul p ON m.user_id = p.user_id
            GROUP BY m.angkatan
            ORDER BY m.angkatan DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Statistik Beban Kerja Dosen
        $dosenModel = new Dosen();
        $workloadDosen = $dosenModel->getAllWorkloadGrouped();

        $notifasi = (new Notifikasi())->getByUserId(Auth::id(), 5);
        require BASE_PATH . '/views/dashboard/superadmin.php';
    }
}
