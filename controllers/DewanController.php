<?php
/**
 * Dewan Controller - Penentuan pembimbing
 * File: controllers/DewanController.php
 */

class DewanController {

    private Pengajuan  $pengajuanModel;
    private Pembimbing $pembimbingModel;
    private StatusLog  $logModel;
    private Notifikasi $notifModel;

    public function __construct() {
        $this->pengajuanModel  = new Pengajuan();
        $this->pembimbingModel = new Pembimbing();
        $this->logModel        = new StatusLog();
        $this->notifModel      = new Notifikasi();
    }

    public function index(): void {
        Auth::requireRole(ROLE_DEWAN);
        $search  = get('search', '');
        $status  = get('status', STATUS_DISETUJUI);
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        $filters = ['search' => $search, 'status' => $status];
        $total   = $this->pengajuanModel->count($filters);
        $pages   = (int)ceil($total / $perPage);
        $list    = $this->pengajuanModel->getAll($filters, $page, $perPage);
        require BASE_PATH . '/views/dewan/index.php';
    }

    public function pembimbing(): void {
        Auth::requireRole(ROLE_DEWAN);
        $id        = (int)get('id');
        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) { setFlash('danger', 'Tidak ditemukan.'); redirect('?page=dewan'); }
        $dosenModel       = new Dosen();
        $listDosen        = $dosenModel->getActiveList((int)$pengajuan['angkatan']);
        $pembimbingAktif  = $this->pembimbingModel->getByPengajuanId($id);
        $logs             = $this->logModel->getByPengajuanId($id);
        require BASE_PATH . '/views/dewan/pembimbing.php';
    }

    public function prosesPembimbing(): void {
        Auth::requireRole(ROLE_DEWAN);
        if (!isPost()) redirect('?page=dewan');
        verifyCsrf();

        $id      = (int)post('pengajuan_id');
        $aksi    = post('aksi'); // 'tetapkan' atau 'revisi'
        $catatan = trim(post('catatan'));

        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) { setFlash('danger', 'Tidak ditemukan.'); redirect('?page=dewan'); }

        if ($aksi === 'revisi') {
            if (empty($catatan)) { 
                setFlash('danger', 'Catatan revisi wajib diisi.'); 
                redirect('?page=dewan.pembimbing&id=' . $id); 
            }
            $this->pengajuanModel->updateStatus($id, STATUS_REVISI, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_REVISI, Auth::id(), $catatan);
            $this->notifModel->send($pengajuan['user_id'], "Pengajuan perlu direvisi oleh Dewan. Catatan: $catatan", $id);
            setFlash('warning', 'Pengajuan dikembalikan untuk revisi.');
            redirect('?page=dewan');
        }

        // Aksi: Setujui
        $this->pengajuanModel->updateStatus($id, STATUS_PEMBIMBING, $catatan ?: 'Proposal disetujui oleh Dewan Bimbingan.');
        $this->logModel->add($id, $pengajuan['status'], STATUS_PEMBIMBING, Auth::id(),
                             $catatan ?: 'Proposal disetujui oleh Dewan Bimbingan.');

        $this->notifModel->send($pengajuan['user_id'],
            "Proposal Anda telah disetujui oleh Dewan Bimbingan. Segera hubungi pembimbing Anda.", $id);

        setFlash('success', 'Proposal berhasil disetujui.');
        redirect('?page=dewan');
    }
}
