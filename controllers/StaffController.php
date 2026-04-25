<?php
/**
 * Staff Controller - Verifikasi berkas pengajuan
 * File: controllers/StaffController.php
 */

class StaffController {

    private Pengajuan  $pengajuanModel;
    private StatusLog  $logModel;
    private Notifikasi $notifModel;

    public function __construct() {
        $this->pengajuanModel = new Pengajuan();
        $this->logModel       = new StatusLog();
        $this->notifModel     = new Notifikasi();
    }

    /**
     * Daftar pengajuan yang masuk (status: diajukan)
     */
    public function index(): void {
        Auth::requireRole(ROLE_STAFF);

        $search  = get('search', '');
        $status  = get('status', STATUS_DIAJUKAN);
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;

        $filters = ['search' => $search, 'status' => $status];
        $total   = $this->pengajuanModel->count($filters);
        $pages   = (int)ceil($total / $perPage);
        $list    = $this->pengajuanModel->getAll($filters, $page, $perPage);

        require BASE_PATH . '/views/staff/index.php';
    }

    /**
     * Halaman verifikasi detail satu pengajuan
     */
    public function verifikasi(): void {
        Auth::requireRole(ROLE_STAFF);

        $id        = (int)get('id');
        $pengajuan = $this->pengajuanModel->findById($id);

        if (!$pengajuan) {
            setFlash('danger', 'Pengajuan tidak ditemukan.');
            redirect('?page=staff');
        }

        $logs = (new StatusLog())->getByPengajuanId($id);

        require BASE_PATH . '/views/staff/verifikasi.php';
    }

    /**
     * Proses verifikasi (lengkap → kirim ke kaprodi, tidak lengkap → revisi)
     */
    public function prosesVerifikasi(): void {
        Auth::requireRole(ROLE_STAFF);

        if (!isPost()) redirect('?page=staff');

        verifyCsrf();

        $id      = (int)post('pengajuan_id');
        $aksi    = post('aksi'); // 'lengkap' atau 'tidak_lengkap'
        $catatan = trim(post('catatan'));

        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) {
            setFlash('danger', 'Pengajuan tidak ditemukan.');
            redirect('?page=staff');
        }

        if ($aksi === 'lengkap') {
            // Verifikasi lengkap → diverifikasi, kirim ke Kaprodi
            $this->pengajuanModel->verifikasi($id, true, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_DIVERIFIKASI,
                                 Auth::id(), $catatan ?: 'Berkas dinyatakan lengkap oleh Staff.');

            // Notif ke Kaprodi
            $this->notifModel->sendToRole(ROLE_KAPRODI,
                "Pengajuan '{$pengajuan['judul']}' telah diverifikasi, menunggu review Anda.", $id);

            // Notif ke Mahasiswa
            $this->notifModel->send($pengajuan['user_id'],
                "Pengajuan Anda telah diverifikasi dan diteruskan ke Kaprodi.", $id);

            setFlash('success', 'Pengajuan dinyatakan lengkap dan diteruskan ke Kaprodi/Sekprodi.');

        } elseif ($aksi === 'tidak_lengkap') {
            // Tidak lengkap → revisi
            if (empty($catatan)) {
                setFlash('danger', 'Catatan revisi wajib diisi jika berkas tidak lengkap.');
                redirect('?page=staff.verifikasi&id=' . $id);
            }

            $this->pengajuanModel->verifikasi($id, false, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_REVISI,
                                 Auth::id(), $catatan);

            // Notif ke Mahasiswa
            $this->notifModel->send($pengajuan['user_id'],
                "Pengajuan Anda dikembalikan untuk revisi oleh Staff. Catatan: $catatan", $id);

            setFlash('warning', 'Pengajuan dikembalikan ke mahasiswa untuk revisi.');
        }

        redirect('?page=staff');
    }
}
