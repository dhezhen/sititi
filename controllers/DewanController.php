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
        $status  = get('status', STATUS_DIREVIEW);
        if (get('status') === 'all') $status = '';
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        $filters = ['search' => $search, 'status' => $status, 'dewan_id' => Auth::id()];
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
            // Jika satu dewan minta revisi, langsung status REVISI
            $this->pengajuanModel->updateStatus($id, STATUS_REVISI, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_REVISI, Auth::id(), $catatan);
            
            // Notif ke Mahasiswa
            $this->notifModel->send($pengajuan['user_id'], "Proposal '{$pengajuan['judul']}' memerlukan revisi dari Dewan Reviewer. Catatan: $catatan", $id);

            setFlash('warning', 'Permintaan revisi telah dikirim ke mahasiswa.');
            redirect('?page=dewan');
        }

        // Aksi: Setujui
        // Log keputusan dewan ini dulu (Gunakan status_baru = STATUS_DIREVIEW_DEWAN agar terhitung sudah review)
        $this->logModel->add($id, $pengajuan['status'], STATUS_DIREVIEW_DEWAN, Auth::id(), 
                             $catatan ?: 'Proposal disetujui oleh Reviewer.');

        // Cek apakah dewan lainnya juga sudah setuju
        $otherDewanId = ($pengajuan['dewan_1_id'] == Auth::id()) ? $pengajuan['dewan_2_id'] : $pengajuan['dewan_1_id'];
        
        $bothApproved = false;
        if (!$otherDewanId) {
            $bothApproved = true;
        } else {
            // Cek apakah dewan lain sudah pernah setuju (cek log)
            $otherLog = $this->logModel->getLastDecisionByDewan($id, $otherDewanId);
            if ($otherLog && $otherLog['status_baru'] === STATUS_DIREVIEW_DEWAN) {
                $bothApproved = true;
            }
        }

        if ($bothApproved) {
            // Update status global pengajuan menjadi DIREVIEW_DEWAN
            $this->pengajuanModel->updateStatus($id, STATUS_DIREVIEW_DEWAN, 'Proposal telah disetujui oleh kedua Dewan Reviewer.');
            
            // Notif ke Kaprodi
            $this->notifModel->sendToRole(ROLE_KAPRODI, "Proposal '{$pengajuan['judul']}' telah disetujui oleh kedua Dewan. Menunggu persetujuan akhir Anda.", $id);
            
            setFlash('success', 'Review selesai. Proposal diteruskan ke Kaprodi untuk persetujuan akhir.');
        } else {
            // Status global tetap (STATUS_DIREVIEW) agar masih muncul di dashboard dewan lain
            setFlash('info', 'Keputusan Anda telah disimpan. Menunggu keputusan dari Reviewer lainnya.');
        }

        redirect('?page=dewan');
    }
    public function history(): void {
        Auth::requireRole(ROLE_DEWAN);
        $search  = get('search', '');
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        
        $total   = $this->pengajuanModel->countReviewedBy(Auth::id(), $search);
        $pages   = (int)ceil($total / $perPage);
        $list    = $this->pengajuanModel->getReviewedBy(Auth::id(), $search, $page, $perPage);
        
        require BASE_PATH . '/views/dewan/history.php';
    }
}
