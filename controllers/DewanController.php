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
        $dosen1  = (int)post('dosen1_id');
        $dosen2  = post('dosen2_id') ? (int)post('dosen2_id') : null;
        $catatan = trim(post('catatan'));

        if (!$dosen1) { setFlash('danger', 'Pembimbing 1 wajib dipilih.'); redirect('?page=dewan.pembimbing&id=' . $id); }
        
        // Validasi Asisten Ahli
        $dosenModel = new Dosen();
        $d1 = $dosenModel->findById($dosen1);
        if ($d1 && $d1['jabatan_akademik'] === 'Asisten Ahli') {
            setFlash('danger', 'Dosen dengan jabatan Asisten Ahli tidak dapat menjadi Pembimbing 1.');
            redirect('?page=dewan.pembimbing&id=' . $id);
        }

        if ($dosen2 && $dosen2 === $dosen1) { setFlash('danger', 'Pembimbing 1 dan 2 tidak boleh sama.'); redirect('?page=dewan.pembimbing&id=' . $id); }

        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) { setFlash('danger', 'Tidak ditemukan.'); redirect('?page=dewan'); }

        $this->pembimbingModel->assignAll($id, $dosen1, $dosen2);
        $this->pengajuanModel->updateStatus($id, STATUS_PEMBIMBING, $catatan ?: 'Pembimbing telah ditentukan.');
        $this->logModel->add($id, $pengajuan['status'], STATUS_PEMBIMBING, Auth::id(),
                             $catatan ?: 'Pembimbing 1 & 2 ditentukan oleh Dewan.');

        $this->notifModel->send($pengajuan['user_id'],
            "Dosen pembimbing skripsi Anda telah ditentukan. Segera hubungi pembimbing Anda.", $id);

        setFlash('success', 'Pembimbing berhasil ditentukan.');
        redirect('?page=dewan');
    }
}
