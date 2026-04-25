<?php
/**
 * Pengajuan Controller - CRUD pengajuan judul oleh Mahasiswa
 * File: controllers/PengajuanController.php
 */

class PengajuanController {

    private Pengajuan  $pengajuanModel;
    private StatusLog  $logModel;
    private Notifikasi $notifModel;

    public function __construct() {
        $this->pengajuanModel = new Pengajuan();
        $this->logModel       = new StatusLog();
        $this->notifModel     = new Notifikasi();
    }

    /**
     * Daftar pengajuan mahasiswa (dengan pagination & search)
     */
    public function index(): void {
        Auth::requireLogin();

        $userId  = Auth::id();
        $page    = max(1, (int)get('p', 1));
        $total   = $this->pengajuanModel->countByUserId($userId);
        $perPage = 10;
        $pages   = (int)ceil($total / $perPage);
        $list    = $this->pengajuanModel->getByUserId($userId, $page, $perPage);
        $hasActive = $this->pengajuanModel->hasActiveSubmission($userId);

        require BASE_PATH . '/views/pengajuan/index.php';
    }

    /**
     * Form buat pengajuan baru
     */
    public function create(): void {
        Auth::requireRole(ROLE_MAHASISWA);

        if ($this->pengajuanModel->hasActiveSubmission(Auth::id())) {
            setFlash('warning', 'Anda sudah memiliki pengajuan judul yang aktif. Maksimal 1 judul.');
            redirect('?page=pengajuan');
        }

        // Ambil periode aktif
        $db      = getDB();
        $stmt    = $db->prepare("SELECT * FROM periode WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $periode = $stmt->fetch();

        require BASE_PATH . '/views/pengajuan/create.php';
    }

    /**
     * Simpan pengajuan baru
     */
    public function store(): void {
        Auth::requireRole(ROLE_MAHASISWA);

        if (!isPost()) redirect('?page=pengajuan');

        verifyCsrf();

        $userId = Auth::id();

        if ($this->pengajuanModel->hasActiveSubmission($userId)) {
            setFlash('danger', 'Anda sudah memiliki pengajuan judul yang aktif.');
            redirect('?page=pengajuan');
        }

        $judul  = trim(post('judul'));
        $desk   = trim(post('deskripsi'));
        $bidang = trim(post('bidang'));
        $perIdx = post('periode_id');
        $errors = [];

        // Validasi
        if (empty($judul) || mb_strlen($judul) < 10) {
            $errors[] = 'Judul minimal 10 karakter.';
        }
        if (empty($desk) || mb_strlen($desk) < 30) {
            $errors[] = 'Deskripsi minimal 30 karakter.';
        }
        if (empty($bidang)) {
            $errors[] = 'Bidang/topik wajib dipilih.';
        }

        // Upload file (opsional saat draft)
        $filename = null;
        if (!empty($_FILES['file_proposal']['name'])) {
            $upload = uploadProposal($_FILES['file_proposal']);
            if (!$upload['success']) {
                $errors[] = $upload['error'];
            } else {
                $filename = $upload['filename'];
            }
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('?page=pengajuan.create');
        }

        $id = $this->pengajuanModel->create([
            'user_id'       => $userId,
            'periode_id'    => $perIdx ?: null,
            'judul'         => $judul,
            'deskripsi'     => $desk,
            'bidang'        => $bidang,
            'file_proposal' => $filename,
        ]);

        // Log
        $this->logModel->add($id, '', STATUS_DRAFT, $userId, 'Pengajuan dibuat.');

        setFlash('success', 'Pengajuan berhasil dibuat sebagai draft.');
        redirect('?page=pengajuan.show&id=' . $id);
    }

    /**
     * Detail pengajuan
     */
    public function show(): void {
        Auth::requireLogin();

        $id        = (int)get('id');
        $pengajuan = $this->pengajuanModel->findById($id);

        if (!$pengajuan) {
            setFlash('danger', 'Pengajuan tidak ditemukan.');
            redirect('?page=pengajuan');
        }

        // Mahasiswa hanya bisa lihat miliknya
        if (Auth::role() === ROLE_MAHASISWA && $pengajuan['user_id'] !== Auth::id()) {
            redirect('?page=403');
        }

        $logModel  = new StatusLog();
        $pbModel   = new Pembimbing();
        $logs      = $logModel->getByPengajuanId($id);
        $pembimbing = $pbModel->getByPengajuanId($id);

        require BASE_PATH . '/views/pengajuan/show.php';
    }

    /**
     * Form edit pengajuan (hanya status draft/revisi)
     */
    public function edit(): void {
        Auth::requireRole(ROLE_MAHASISWA);

        $id        = (int)get('id');
        $pengajuan = $this->pengajuanModel->findById($id);

        if (!$pengajuan || $pengajuan['user_id'] !== Auth::id()) {
            redirect('?page=403');
        }

        if (!in_array($pengajuan['status'], [STATUS_DRAFT, STATUS_REVISI])) {
            setFlash('warning', 'Pengajuan tidak dapat diedit pada status saat ini.');
            redirect('?page=pengajuan.show&id=' . $id);
        }

        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM periode WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $periode = $stmt->fetch();

        require BASE_PATH . '/views/pengajuan/edit.php';
    }

    /**
     * Update pengajuan
     */
    public function update(): void {
        Auth::requireRole(ROLE_MAHASISWA);

        if (!isPost()) redirect('?page=pengajuan');

        verifyCsrf();

        $id        = (int)post('id');
        $pengajuan = $this->pengajuanModel->findById($id);

        if (!$pengajuan || $pengajuan['user_id'] !== Auth::id()) {
            redirect('?page=403');
        }

        if (!in_array($pengajuan['status'], [STATUS_DRAFT, STATUS_REVISI])) {
            setFlash('warning', 'Pengajuan tidak dapat diedit.');
            redirect('?page=pengajuan.show&id=' . $id);
        }

        $judul  = trim(post('judul'));
        $desk   = trim(post('deskripsi'));
        $bidang = trim(post('bidang'));
        $errors = [];

        if (empty($judul) || mb_strlen($judul) < 10) {
            $errors[] = 'Judul minimal 10 karakter.';
        }
        if (empty($desk) || mb_strlen($desk) < 30) {
            $errors[] = 'Deskripsi minimal 30 karakter.';
        }
        if (empty($bidang)) {
            $errors[] = 'Bidang wajib dipilih.';
        }

        $filename = null;
        if (!empty($_FILES['file_proposal']['name'])) {
            $upload = uploadProposal($_FILES['file_proposal']);
            if (!$upload['success']) {
                $errors[] = $upload['error'];
            } else {
                // Hapus file lama
                if ($pengajuan['file_proposal']) {
                    @unlink(UPLOAD_PATH . '/' . $pengajuan['file_proposal']);
                }
                $filename = $upload['filename'];
            }
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('?page=pengajuan.edit&id=' . $id);
        }

        $this->pengajuanModel->update($id, [
            'judul'         => $judul,
            'deskripsi'     => $desk,
            'bidang'        => $bidang,
            'file_proposal' => $filename,
        ]);

        $this->logModel->add($id, $pengajuan['status'], $pengajuan['status'],
                             Auth::id(), 'Data pengajuan diperbarui.');

        setFlash('success', 'Pengajuan berhasil diperbarui.');
        redirect('?page=pengajuan.show&id=' . $id);
    }

    /**
     * Submit pengajuan (draft → diajukan)
     */
    public function submit(): void {
        Auth::requireRole(ROLE_MAHASISWA);

        $id        = (int)get('id');
        $pengajuan = $this->pengajuanModel->findById($id);

        if (!$pengajuan || $pengajuan['user_id'] !== Auth::id()) {
            redirect('?page=403');
        }

        if (!in_array($pengajuan['status'], [STATUS_DRAFT, STATUS_REVISI])) {
            setFlash('warning', 'Tidak dapat diajukan kembali.');
            redirect('?page=pengajuan.show&id=' . $id);
        }

        if (empty($pengajuan['file_proposal'])) {
            setFlash('danger', 'Upload file proposal terlebih dahulu sebelum mengajukan.');
            redirect('?page=pengajuan.show&id=' . $id);
        }

        $this->pengajuanModel->submit($id);
        $this->logModel->add($id, $pengajuan['status'], STATUS_DIAJUKAN,
                             Auth::id(), 'Pengajuan diajukan oleh mahasiswa.');

        // Notif ke Staff
        $this->notifModel->sendToRole(ROLE_STAFF,
            "Pengajuan baru dari " . Auth::user()['nama'] . " menunggu verifikasi.", $id);

        setFlash('success', 'Pengajuan berhasil diajukan. Menunggu verifikasi staff.');
        redirect('?page=pengajuan.show&id=' . $id);
    }

    /**
     * Hapus pengajuan (hanya status draft)
     */
    public function delete(): void {
        Auth::requireRole(ROLE_MAHASISWA);

        $id        = (int)get('id');
        $pengajuan = $this->pengajuanModel->findById($id);

        if (!$pengajuan || $pengajuan['user_id'] !== Auth::id()) {
            redirect('?page=403');
        }

        if ($pengajuan['status'] !== STATUS_DRAFT) {
            setFlash('warning', 'Hanya pengajuan berstatus draft yang dapat dihapus.');
            redirect('?page=pengajuan');
        }

        // Hapus file
        if ($pengajuan['file_proposal']) {
            @unlink(UPLOAD_PATH . '/' . $pengajuan['file_proposal']);
        }

        $this->pengajuanModel->delete($id);
        setFlash('success', 'Pengajuan berhasil dihapus.');
        redirect('?page=pengajuan');
    }
}
