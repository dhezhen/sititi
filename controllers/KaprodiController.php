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
        
        $db = getDB();
        $stmt = $db->prepare("SELECT prodi_id FROM dosen WHERE email = ? LIMIT 1");
        $stmt->execute([Auth::user()['email']]);
        $prodiId = $stmt->fetchColumn();

        $search  = get('search', '');
        $status  = get('status', ''); // Default: Semua status

        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        
        $filters = ['search' => $search, 'status' => $status, 'prodi_id' => $prodiId];
        $total   = $this->pengajuanModel->count($filters);
        $pages   = (int)ceil($total / $perPage);
        $list    = $this->pengajuanModel->getAll($filters, $page, $perPage);
        require BASE_PATH . '/views/kaprodi/index.php';
    }

    public function history(): void {
        Auth::requireRole(ROLE_KAPRODI);
        
        $db = getDB();
        $stmt = $db->prepare("SELECT prodi_id FROM dosen WHERE email = ? LIMIT 1");
        $stmt->execute([Auth::user()['email']]);
        $prodiId = $stmt->fetchColumn();

        $search  = get('search', '');
        $status  = get('status', ''); // Semua status
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        
        $filters = ['search' => $search, 'status' => $status, 'prodi_id' => $prodiId];
        $total   = $this->pengajuanModel->count($filters);
        $pages   = (int)ceil($total / $perPage);
        $list    = $this->pengajuanModel->getAll($filters, $page, $perPage);
        
        require BASE_PATH . '/views/kaprodi/history.php';
    }

    public function review(): void {
        Auth::requireRole(ROLE_KAPRODI);
        $id        = (int)get('id');
        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) { setFlash('danger', 'Pengajuan tidak ditemukan.'); redirect('?page=kaprodi'); }
        $logs = (new StatusLog())->getByPengajuanId($id);
        $dosenModel = new Dosen();
        $userModel  = new User();
        $listDosen  = $dosenModel->getActiveList();
        $listDewan  = $userModel->getListByRole(ROLE_DEWAN);
        require BASE_PATH . '/views/kaprodi/review.php';
    }

    public function prosesReview(): void {
        Auth::requireRole(ROLE_KAPRODI);
        if (!isPost()) redirect('?page=kaprodi');
        verifyCsrf();

        $id        = (int)post('pengajuan_id');
        $aksi      = post('aksi');
        $catatan   = trim(post('catatan'));
        $p1        = post('pembimbing_1_id') ? (int)post('pembimbing_1_id') : null;
        $p2        = post('pembimbing_2_id') ? (int)post('pembimbing_2_id') : null;
        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) { setFlash('danger', 'Tidak ditemukan.'); redirect('?page=kaprodi'); }

        if ($pengajuan['status'] === STATUS_DIVERIFIKASI) {
            // Step 1: Kaprodi Assigns Advisor & Dewan -> Go to Dewan
            if ($aksi === 'setujui') {
                if (!$p1) { setFlash('danger', 'Pembimbing 1 wajib ditentukan.'); redirect('?page=kaprodi.review&id=' . $id); }
                $dw1 = (int)post('dewan_1_id');
                $dw2 = (int)post('dewan_2_id');
                if (!$dw1 || !$dw2) { setFlash('danger', 'Kedua Dewan Reviewer wajib ditentukan.'); redirect('?page=kaprodi.review&id=' . $id); }

                $dosenModel = new Dosen();
                $d1 = $dosenModel->findById($p1);
                if ($d1 && $d1['jabatan_akademik'] === 'Asisten Ahli') {
                    setFlash('danger', 'Asisten Ahli tidak dapat menjadi Pembimbing 1.'); redirect('?page=kaprodi.review&id=' . $id);
                }

                // Update pembimbing & dewan
                $this->pengajuanModel->update($id, [
                    'pembimbing_1_id' => $p1, 
                    'pembimbing_2_id' => $p2,
                    'dewan_1_id' => $dw1,
                    'dewan_2_id' => $dw2
                ]);
                (new Pembimbing())->assignAll($id, $p1, $p2);

                $this->pengajuanModel->updateStatus($id, STATUS_DIREVIEW, $catatan ?: 'Menunggu review proposal oleh Dewan.');
                $this->logModel->add($id, $pengajuan['status'], STATUS_DIREVIEW, Auth::id(), $catatan ?: 'Disetujui Kaprodi & Menunggu Review Dewan.');
                
                // Notif ke Dewan spesifik
                $this->notifModel->send($dw1, "Anda ditugaskan sebagai Dewan Reviewer 1 untuk pengajuan '{$pengajuan['judul']}'.", $id);
                $this->notifModel->send($dw2, "Anda ditugaskan sebagai Dewan Reviewer 2 untuk pengajuan '{$pengajuan['judul']}'.", $id);
                
                setFlash('success', 'Berhasil diteruskan ke Dewan.');
            }
        } elseif ($pengajuan['status'] === STATUS_DIREVIEW_DEWAN) {
            // Step 2: Final Approval after Dewan Review
            if ($aksi === 'setujui') {
                $this->pengajuanModel->updateStatus($id, STATUS_PEMBIMBING, $catatan ?: 'Disetujui Kaprodi & Diajukan ke Fakultas.');
                $this->logModel->add($id, $pengajuan['status'], STATUS_PEMBIMBING, Auth::id(), $catatan ?: 'Persetujuan Akhir Kaprodi.');
                $this->notifModel->send($pengajuan['user_id'], "Judul skripsi Anda telah disetujui akhir dan diajukan ke Fakultas.", $id);
                setFlash('success', 'Pengajuan telah disetujui akhir.');
            }
        }

        if ($aksi === 'tolak') {
            if (empty($catatan)) { setFlash('danger', 'Alasan penolakan wajib diisi.'); redirect('?page=kaprodi.review&id=' . $id); }
            $this->pengajuanModel->updateStatus($id, STATUS_DITOLAK, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_DITOLAK, Auth::id(), $catatan);
            setFlash('danger', 'Pengajuan ditolak.');
        } elseif ($aksi === 'revisi') {
            if (empty($catatan)) { setFlash('danger', 'Catatan revisi wajib diisi.'); redirect('?page=kaprodi.review&id=' . $id); }
            $this->pengajuanModel->updateStatus($id, STATUS_REVISI, $catatan);
            $this->logModel->add($id, $pengajuan['status'], STATUS_REVISI, Auth::id(), $catatan);
            setFlash('warning', 'Pengajuan dikembalikan untuk revisi.');
        }

        redirect('?page=kaprodi');
    }
}
