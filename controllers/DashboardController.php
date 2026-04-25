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
                SELECT p.*, m.nama AS nama_mahasiswa, m.nim, pb.jabatan
                FROM pembimbing pb
                JOIN pengajuan_judul p ON p.id = pb.pengajuan_id
                JOIN mahasiswa m ON m.user_id = p.mahasiswa_id
                WHERE pb.dosen_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmtB->execute([$dosenId]);
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
        $recent      = $pengajuanModel->getRecent(5, STATUS_DIAJUKAN);
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
        
        $dosenModel = new Dosen();
        $dosenWorkload = $dosenModel->getAllWorkload();

        require BASE_PATH . '/views/dashboard/staff.php';
    }

    /**
     * Dashboard Kaprodi
     */
    private function kaprodiDashboard(): void {
        $pengajuanModel = new Pengajuan();

        $diverifikasi = $pengajuanModel->count(['status' => STATUS_DIVERIFIKASI]);
        $direview     = $pengajuanModel->count(['status' => STATUS_DIREVIEW]);
        $disetujui    = $pengajuanModel->count(['status' => STATUS_DISETUJUI]);
        $ditolak      = $pengajuanModel->count(['status' => STATUS_DITOLAK]);
        $recent       = $pengajuanModel->getRecent(5, STATUS_DIVERIFIKASI);
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
        }

        $dosenModel = new Dosen();
        $dosenWorkload = $dosenModel->getAllWorkload($prodiId);

        require BASE_PATH . '/views/dashboard/kaprodi.php';
    }

    /**
     * Dashboard Dewan Bimbingan Skripsi
     */
    private function dewanDashboard(): void {
        $pengajuanModel = new Pengajuan();

        $disetujui  = $pengajuanModel->count(['status' => STATUS_DISETUJUI]);
        $pembimbing = $pengajuanModel->count(['status' => STATUS_PEMBIMBING]);
        $recent     = $pengajuanModel->getRecent(5, STATUS_DISETUJUI);
        $notifasi   = (new Notifikasi())->getByUserId(Auth::id(), 5);

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

        $notifasi = (new Notifikasi())->getByUserId(Auth::id(), 5);
        require BASE_PATH . '/views/dashboard/superadmin.php';
    }
}
