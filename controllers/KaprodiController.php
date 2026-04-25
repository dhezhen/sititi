<?php
/**
 * Kaprodi Controller - Review dan Approval pengajuan
 * File: controllers/KaprodiController.php
 */

class KaprodiController {

    private Pengajuan  $pengajuanModel;
    private StatusLog  $logModel;
    private Notifikasi $notifModel;

    public function __construct() {
        $this->pengajuanModel = new Pengajuan();
        $this->logModel       = new StatusLog();
        $this->notifModel     = new Notifikasi();
    }

    public function index(): void {
        Auth::requireRole(ROLE_KAPRODI);
        $search  = get('search', '');
        $status  = get('status', STATUS_DIVERIFIKASI);
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        $filters = ['search' => $search, 'status' => $status];
        $total   = $this->pengajuanModel->count($filters);
        $pages   = (int)ceil($total / $perPage);
        $list    = $this->pengajuanModel->getAll($filters, $page, $perPage);
        require BASE_PATH . '/views/kaprodi/index.php';
    }

    public function review(): void {
        Auth::requireRole(ROLE_KAPRODI);
        $id        = (int)get('id');
        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) { setFlash('danger', 'Pengajuan tidak ditemukan.'); redirect('?page=kaprodi'); }
        $logs = (new StatusLog())->getByPengajuanId($id);
        require BASE_PATH . '/views/kaprodi/review.php';
    }

    public function prosesReview(): void {
        Auth::requireRole(ROLE_KAPRODI);
        if (!isPost()) redirect('?page=kaprodi');
        verifyCsrf();

        $id        = (int)post('pengajuan_id');
        $aksi      = post('aksi');
        $catatan   = trim(post('catatan'));
        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) { setFlash('danger', 'Tidak ditemukan.'); redirect('?page=kaprodi'); }

        if ($pengajuan['status'] === STATUS_DIVERIFIKASI) {
            $this->pengajuanModel->updateStatus($id, STATUS_DIREVIEW);
        }

        if ($aksi === 'setujui') {
            $this->pengajuanModel->updateStatus($id, STATUS_DISETUJUI, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_DISETUJUI, Auth::id(), $catatan ?: 'Disetujui.');
            $this->notifModel->sendToRole(ROLE_DEWAN, "Pengajuan '{$pengajuan['judul']}' disetujui. Tentukan pembimbing.", $id);
            $this->notifModel->send($pengajuan['user_id'], "Judul skripsi Anda disetujui Kaprodi!", $id);
            setFlash('success', 'Pengajuan disetujui dan diteruskan ke Dewan Bimbingan.');

        } elseif ($aksi === 'tolak') {
            if (empty($catatan)) { setFlash('danger', 'Alasan penolakan wajib diisi.'); redirect('?page=kaprodi.review&id=' . $id); }
            $this->pengajuanModel->updateStatus($id, STATUS_DITOLAK, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_DITOLAK, Auth::id(), $catatan);
            $this->notifModel->send($pengajuan['user_id'], "Pengajuan Anda ditolak. Alasan: $catatan", $id);
            setFlash('danger', 'Pengajuan ditolak.');

        } elseif ($aksi === 'revisi') {
            if (empty($catatan)) { setFlash('danger', 'Catatan revisi wajib diisi.'); redirect('?page=kaprodi.review&id=' . $id); }
            $this->pengajuanModel->updateStatus($id, STATUS_REVISI, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_REVISI, Auth::id(), $catatan);
            $this->notifModel->send($pengajuan['user_id'], "Pengajuan perlu direvisi. Catatan: $catatan", $id);
            setFlash('warning', 'Pengajuan dikembalikan untuk revisi.');
        }

        redirect('?page=kaprodi');
    }
}
