<?php
/**
 * Admin Controller - Manajemen Data Master & Import
 * File: controllers/AdminController.php
 */

class AdminController {

    private Mahasiswa $mhsModel;
    private Dosen $dosenModel;
    private User $userModel;
    private Periode $periodeModel;

    public function __construct() {
        $this->mhsModel     = new Mahasiswa();
        $this->dosenModel   = new Dosen();
        $this->userModel    = new User();
        $this->periodeModel = new Periode();
    }

    // ── MAHASISWA ───────────────────────────────────────────────────

    public function mahasiswa(): void {
        Auth::requireRole(ROLE_STAFF); // Atau admin jika ada

        $search  = get('search', '');
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        
        $total = $this->mhsModel->count($search);
        $pages = (int)ceil($total / $perPage);
        $list  = $this->mhsModel->getAll($search, $page, $perPage);

        require BASE_PATH . '/views/admin/mahasiswa/index.php';
    }

    public function importMahasiswa(): void {
        Auth::requireRole(ROLE_STAFF);

        if (!isPost()) redirect('?page=admin.mahasiswa');
        verifyCsrf();

        if (empty($_FILES['file_csv']['name'])) {
            setFlash('danger', 'Pilih file CSV terlebih dahulu.');
            redirect('?page=admin.mahasiswa');
        }

        $file = $_FILES['file_csv']['tmp_name'];
        if (!file_exists($file)) {
            setFlash('danger', 'File tidak ditemukan.');
            redirect('?page=admin.mahasiswa');
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Baca header
        
        $success = 0;
        $failed  = 0;
        $db = getDB();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) continue; // Skip baris kosong

            $nama     = trim($row[0] ?? '');
            $email    = trim($row[1] ?? '');
            $nim      = trim($row[2] ?? '');
            $angkatan = trim($row[3] ?? '');
            $semester = trim($row[4] ?? '');
            $kd_prodi = trim($row[5] ?? '');
            $no_hp    = trim($row[6] ?? '');
            $alamat   = trim($row[7] ?? '');

            if (empty($nama) || empty($email) || empty($nim)) {
                $failed++;
                continue;
            }

            // Cek apakah email sudah ada
            if ($this->userModel->emailExists($email)) {
                $failed++;
                continue;
            }

            // Cari ID Prodi berdasarkan kode
            $prodiId = null;
            if ($kd_prodi) {
                $stmt = $db->prepare("SELECT id FROM prodi WHERE kode = ? LIMIT 1");
                $stmt->execute([$kd_prodi]);
                $prodiId = $stmt->fetchColumn();
            }

            try {
                $db->beginTransaction();
                
                // Buat User
                $userId = $this->userModel->create([
                    'nama'     => $nama,
                    'email'    => $email,
                    'password' => 'password', // Default password
                    'role'     => ROLE_MAHASISWA
                ]);

                // Buat Profil Mahasiswa
                $this->mhsModel->create([
                    'user_id'  => $userId,
                    'nim'      => $nim,
                    'angkatan' => $angkatan ?: date('Y'),
                    'semester' => $semester ?: 1,
                    'prodi_id' => $prodiId ?: null,
                    'no_hp'    => $no_hp,
                    'alamat'   => $alamat
                ]);

                $db->commit();
                $success++;
            } catch (Exception $e) {
                $db->rollBack();
                $failed++;
            }
        }
        fclose($handle);

        setFlash('success', "Import selesai. Berhasil: $success, Gagal/Lewati: $failed.");
        redirect('?page=admin.mahasiswa');
    }

    // ── DOSEN ───────────────────────────────────────────────────────

    public function dosen(): void {
        Auth::requireLogin();
        // Hanya Staff, Kaprodi, Dewan yang boleh akses data master dosen
        if (!in_array(Auth::role(), [ROLE_STAFF, ROLE_KAPRODI, ROLE_DEWAN])) {
            redirect('?page=403');
        }

        $search  = get('search', '');
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        
        $total = $this->dosenModel->count($search);
        $pages = (int)ceil($total / $perPage);
        $list  = $this->dosenModel->getAll($search, $page, $perPage);

        require BASE_PATH . '/views/admin/dosen/index.php';
    }

    public function importDosen(): void {
        Auth::requireRole(ROLE_STAFF);

        if (!isPost()) redirect('?page=admin.dosen');
        verifyCsrf();

        if (empty($_FILES['file_csv']['name'])) {
            setFlash('danger', 'Pilih file CSV terlebih dahulu.');
            redirect('?page=admin.dosen');
        }

        $file = $_FILES['file_csv']['tmp_name'];
        if (!file_exists($file)) {
            setFlash('danger', 'File tidak ditemukan.');
            redirect('?page=admin.dosen');
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);
        
        $success = 0;
        $failed  = 0;
        $db = getDB();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) continue;

            $nama     = trim($row[0] ?? '');
            $email    = trim($row[1] ?? '');
            $nidn     = trim($row[2] ?? '');
            $kd_prodi = trim($row[3] ?? '');
            $bidang   = trim($row[4] ?? '');
            $no_hp    = trim($row[5] ?? '');

            if (empty($nama) || empty($nidn)) {
                $failed++;
                continue;
            }

            // Cari ID Prodi berdasarkan kode
            $prodiId = null;
            if ($kd_prodi) {
                $stmt = $db->prepare("SELECT id FROM prodi WHERE kode = ? LIMIT 1");
                $stmt->execute([$kd_prodi]);
                $prodiId = $stmt->fetchColumn();
            }

            // Cek NIDN duplikat
            $stmt = $db->prepare("SELECT id FROM dosen WHERE nidn = ?");
            $stmt->execute([$nidn]);
            if ($stmt->fetchColumn()) {
                $failed++;
                continue;
            }

            try {
                $this->dosenModel->create([
                    'nama'            => $nama,
                    'nidn'            => $nidn,
                    'email'           => $email,
                    'prodi_id'        => $prodiId ?: null,
                    'bidang_keahlian' => $bidang,
                    'no_hp'           => $no_hp
                ]);
                $success++;
            } catch (Exception $e) {
                $failed++;
            }
        }
        fclose($handle);

        setFlash('success', "Import selesai. Berhasil: $success, Gagal/Lewati: $failed.");
        redirect('?page=admin.dosen');
    }

    // ── PERIODE ─────────────────────────────────────────────────────

    public function periode(): void {
        Auth::requireRole(ROLE_KAPRODI);

        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        
        $total = $this->periodeModel->count();
        $pages = (int)ceil($total / $perPage);
        $list  = $this->periodeModel->getAll($page, $perPage);

        require BASE_PATH . '/views/admin/periode/index.php';
    }

    public function periodeStore(): void {
        Auth::requireRole(ROLE_KAPRODI);
        if (!isPost()) redirect('?page=admin.periode');
        verifyCsrf();

        $nama   = trim(post('nama'));
        $mulai  = trim(post('tanggal_mulai'));
        $selesai= trim(post('tanggal_selesai'));
        
        if (empty($nama) || empty($mulai) || empty($selesai)) {
            setFlash('danger', 'Semua field wajib diisi.');
            redirect('?page=admin.periode');
        }

        $this->periodeModel->create([
            'nama' => $nama,
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'is_active' => post('is_active') ? 1 : 0
        ]);

        setFlash('success', 'Periode berhasil ditambahkan.');
        redirect('?page=admin.periode');
    }

    public function periodeUpdate(): void {
        Auth::requireRole(ROLE_KAPRODI);
        if (!isPost()) redirect('?page=admin.periode');
        verifyCsrf();

        $id     = (int)post('id');
        $nama   = trim(post('nama'));
        $mulai  = trim(post('tanggal_mulai'));
        $selesai= trim(post('tanggal_selesai'));
        
        $this->periodeModel->update($id, [
            'nama' => $nama,
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai
        ]);

        setFlash('success', 'Periode berhasil diupdate.');
        redirect('?page=admin.periode');
    }

    public function periodeSetActive(): void {
        Auth::requireRole(ROLE_KAPRODI);
        $id = (int)get('id');
        $this->periodeModel->setActive($id);
        setFlash('success', 'Periode aktif berhasil diubah.');
        redirect('?page=admin.periode');
    }

    public function periodeDelete(): void {
        Auth::requireRole(ROLE_KAPRODI);
        $id = (int)get('id');
        $this->periodeModel->delete($id);
        setFlash('success', 'Periode berhasil dihapus.');
        redirect('?page=admin.periode');
    }
}
