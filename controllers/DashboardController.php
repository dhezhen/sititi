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
            case ROLE_SUPERADMIN:
                $this->superadminDashboard();
                break;
            default:
                redirect('?page=403');
        }
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
        
        $notifasi = (new Notifikasi())->getByUserId(Auth::id(), 5);
        require BASE_PATH . '/views/dashboard/superadmin.php';
    }
}
