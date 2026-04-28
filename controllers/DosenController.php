<?php
/**
 * Dosen Controller - Manajemen Bimbingan bagi Dosen
 */
class DosenController {
    private Dosen $dosenModel;
    private CatatanBimbingan $catatanModel;
    private Pengajuan $pengajuanModel;

    public function __construct() {
        $this->dosenModel   = new Dosen();
        $this->catatanModel = new CatatanBimbingan();
        $this->pengajuanModel = new Pengajuan();
    }

    /**
     * Halaman daftar mahasiswa bimbingan
     */
    public function bimbingan(): void {
        Auth::requireRole(ROLE_DOSEN);
        
        $user  = Auth::user();
        $dosen = $this->dosenModel->findByEmail($user['email']);
        
        if (!$dosen) {
            setFlash('danger', 'Data dosen tidak ditemukan.');
            redirect('?page=dashboard');
        }

        // Ambil mahasiswa bimbingan (di mana dosen adalah pembimbing 1 atau 2)
        // Ambil mahasiswa bimbingan:
        // 1. Resmi (di tabel pembimbing) - Status harus STATUS_SELESAI_FAKULTAS ke atas
        // 2. Calon (di kolom pembimbing_1_id/pembimbing_2_id) - Selama proses pengajuan
        $db = getDB();
        $stmt = $db->prepare("
            (SELECT pj.*, u.nama as nama_mahasiswa, m.nim, p.nama as nama_prodi, 'Resmi' as tipe_pembimbing
            FROM pengajuan_judul pj
            JOIN mahasiswa m ON m.user_id = pj.user_id
            JOIN users u ON u.id = m.user_id
            JOIN prodi p ON p.id = m.prodi_id
            JOIN pembimbing pb ON pb.pengajuan_id = pj.id
            WHERE pb.dosen_id = ? AND pj.status = ?)
            
            UNION
            
            (SELECT pj.*, u.nama as nama_mahasiswa, m.nim, p.nama as nama_prodi, 'Calon' as tipe_pembimbing
            FROM pengajuan_judul pj
            JOIN mahasiswa m ON m.user_id = pj.user_id
            JOIN users u ON u.id = m.user_id
            JOIN prodi p ON p.id = m.prodi_id
            WHERE (pj.pembimbing_1_id = ? OR pj.pembimbing_2_id = ?) 
            AND pj.status NOT IN (?, ?))
            
            ORDER BY created_at DESC
        ");
        
        $stmt->execute([
            $dosen['id'], STATUS_SELESAI_FAKULTAS,
            $dosen['id'], $dosen['id'], 
            STATUS_SELESAI_FAKULTAS, STATUS_DITOLAK
        ]);
        $listBimbingan = $stmt->fetchAll();

        require BASE_PATH . '/views/dosen/bimbingan_list.php';
    }

    /**
     * Halaman detail bimbingan & tambah catatan
     */
    public function showBimbingan(): void {
        Auth::requireRole(ROLE_DOSEN);
        
        $id    = (int)get('id');
        $user  = Auth::user();
        $dosen = $this->dosenModel->findByEmail($user['email']);

        $pengajuan = $this->pengajuanModel->findById($id);
        if (!$pengajuan) {
            setFlash('danger', 'Pengajuan tidak ditemukan.');
            redirect('?page=dosen.bimbingan');
        }

        // Pastikan dosen ini memang pembimbingnya (Resmi atau Calon)
        $db = getDB();
        $stmt = $db->prepare("
            SELECT (
                SELECT COUNT(*) FROM pembimbing WHERE pengajuan_id = ? AND dosen_id = ?
            ) + (
                SELECT COUNT(*) FROM pengajuan_judul WHERE id = ? AND (pembimbing_1_id = ? OR pembimbing_2_id = ?)
            ) as is_pembimbing
        ");
        $stmt->execute([$id, $dosen['id'], $id, $dosen['id'], $dosen['id']]);
        if ($stmt->fetchColumn() == 0) {
            setFlash('danger', 'Anda tidak memiliki akses ke bimbingan ini.');
            redirect('?page=dosen.bimbingan');
        }

        $catatan = $this->catatanModel->getByPengajuanId($id);

        require BASE_PATH . '/views/dosen/bimbingan_show.php';
    }

    /**
     * Simpan catatan bimbingan
     */
    public function storeCatatan(): void {
        Auth::requireRole(ROLE_DOSEN);
        if (!isPost()) redirect('?page=dosen.bimbingan');
        verifyCsrf();

        $pengajuanId = (int)post('pengajuan_id');
        $catatan     = trim(post('catatan'));
        
        $user  = Auth::user();
        $dosen = $this->dosenModel->findByEmail($user['email']);

        if (empty($catatan)) {
            setFlash('warning', 'Catatan tidak boleh kosong.');
            redirect('?page=dosen.bimbingan.show&id=' . $pengajuanId);
        }

        $result = $this->catatanModel->create([
            'pengajuan_id' => $pengajuanId,
            'dosen_id'     => $dosen['id'],
            'catatan'      => $catatan
        ]);

        if ($result) {
            setFlash('success', 'Catatan berhasil ditambahkan.');
            
            // Cek jika kedua pembimbing sudah memberi masukan
            if ($this->catatanModel->hasFeedbackFromBothAdvisors($pengajuanId)) {
                // Update status pengajuan ke STATUS_DIVERIFIKASI (atau status selanjutnya)
                $db = getDB();
                $stmt = $db->prepare("UPDATE pengajuan_judul SET status = ? WHERE id = ? AND status = ?");
                $stmt->execute([STATUS_DIVERIFIKASI, $pengajuanId, STATUS_DIAJUKAN]);
            }
        } else {
            setFlash('danger', 'Gagal menambahkan catatan.');
        }

        redirect('?page=dosen.bimbingan.show&id=' . $pengajuanId);
    }
}
