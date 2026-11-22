<?php
/**
 * Admin Controller - Manajemen Data Master & Import
 * File: controllers/AdminController.php
 */

use PhpOffice\PhpSpreadsheet\IOFactory;

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


    public function staff(): void {
        Auth::requireRole([ROLE_SUPERADMIN, ROLE_KAPRODI]); // Hanya superadmin yang boleh kelola staff
        
        $search  = trim(get('search'));
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        $offset  = ($page - 1) * $perPage;

        $db = getDB();
        $where = "role = 'staff'";
        $params = [];
        if ($search) {
            $where .= " AND (nama LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE $where ORDER BY nama ASC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        $list = $stmt->fetchAll();

        $stmtCount = $db->prepare("SELECT COUNT(*) FROM users WHERE $where");
        $stmtCount->execute($params);
        $total = $stmtCount->fetchColumn();
        $pages = ceil($total / $perPage);

        require BASE_PATH . '/views/admin/staff/index.php';
    }

    public function staffStore(): void {
        Auth::requireRole([ROLE_SUPERADMIN, ROLE_KAPRODI]);
        if (!isPost()) redirect('?page=admin.staff');
        verifyCsrf();

        $nama     = trim(post('nama'));
        $email    = trim(post('email'));
        $password = post('password');

        if (empty($nama) || empty($email) || empty($password)) {
            setFlash('danger', 'Semua field wajib diisi.');
            redirect('?page=admin.staff');
        }

        if ($this->userModel->emailExists($email)) {
            setFlash('danger', 'Email sudah terdaftar.');
            redirect('?page=admin.staff');
        }

        $this->userModel->create([
            'nama'     => $nama,
            'email'    => $email,
            'password' => $password,
            'role'     => ROLE_STAFF
        ]);

        setFlash('success', 'Staff berhasil ditambahkan.');
        redirect('?page=admin.staff');
    }

    public function staffUpdate(): void {
        Auth::requireRole([ROLE_SUPERADMIN, ROLE_KAPRODI]);
        if (!isPost()) redirect('?page=admin.staff');
        verifyCsrf();

        $id       = (int)post('id');
        $nama     = trim(post('nama'));
        $email    = trim(post('email'));
        $password = post('password');
        $active   = (int)post('is_active');

        if ($this->userModel->emailExists($email, $id)) {
            setFlash('danger', 'Email sudah terdaftar pada user lain.');
            redirect('?page=admin.staff');
        }

        $data = [
            'nama'      => $nama,
            'email'     => $email,
            'is_active' => $active
        ];
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $this->userModel->update($id, $data);

        setFlash('success', 'Data staff berhasil diperbarui.');
        redirect('?page=admin.staff');
    }

    public function staffDelete(): void {
        Auth::requireRole([ROLE_SUPERADMIN, ROLE_KAPRODI]);
        $id = (int)get('id');
        if ($id) {
            $this->userModel->delete($id);
            setFlash('success', 'Staff berhasil dihapus.');
        }
        redirect('?page=admin.staff');
    }


    public function mahasiswa(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI]); // Staff dan Kaprodi boleh akses

        $searchInput = get('search', '');
        $search  = is_string($searchInput) ? trim($searchInput) : '';
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;

        $prodiInput = get('prodi_id', '');
        $prodiId = is_string($prodiInput) && ctype_digit($prodiInput) && (int)$prodiInput > 0
            ? (int)$prodiInput
            : null;
        $angkatanInput = get('angkatan', '');
        $angkatan = is_string($angkatanInput) && preg_match('/^\d{4}$/', $angkatanInput)
            ? $angkatanInput
            : '';
        $statusInput = get('status', '');
        $status = is_string($statusInput) && in_array($statusInput, ['aktif', 'nonaktif'], true)
            ? $statusInput
            : '';
        $filters = [
            'prodi_id' => $prodiId,
            'angkatan' => $angkatan,
            'is_active' => $status === '' ? null : (int)($status === 'aktif'),
        ];

        $total = $this->mhsModel->count($search, $filters);
        $pages = (int)ceil($total / $perPage);
        $list  = $this->mhsModel->getAll($search, $page, $perPage, $filters);

        $db = getDB();
        $prodiList = $db->query("SELECT id, nama, kode FROM prodi ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        $angkatanList = $db->query("SELECT DISTINCT angkatan FROM mahasiswa ORDER BY angkatan DESC")->fetchAll(PDO::FETCH_COLUMN);

        require BASE_PATH . '/views/admin/mahasiswa/index.php';
    }

    public function mahasiswaStore(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI]);

        if (!isPost()) redirect('?page=admin.mahasiswa');
        verifyCsrf();

        $nama     = trim(post('nama'));
        $email    = trim(post('email'));
        $password = post('password');
        $nim      = trim(post('nim'));
        $angkatan = trim(post('angkatan'));
        
        // Ekstrak angkatan dari NIM (4 digit pertama) jika angkatan kosong
        if (empty($angkatan) && strlen($nim) >= 4) {
            $year = substr($nim, 0, 4);
            if (is_numeric($year)) $angkatan = $year;
        }

        $semester = trim(post('semester'));
        $prodi_id = post('prodi_id');
        $no_hp    = trim(post('no_hp'));
        $alamat   = trim(post('alamat'));

        $errors = [];
        if (empty($nama) || empty($email) || empty($password) || empty($nim)) {
            $errors[] = 'Nama, Email, Password, dan NIM wajib diisi.';
        }
        
        if (!str_ends_with($email, '@uniku.ac.id')) {
            $errors[] = 'Email wajib menggunakan domain @uniku.ac.id';
        }

        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Email sudah terdaftar.';
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('?page=admin.mahasiswa');
        }

        $db = getDB();
        try {
            $db->beginTransaction();
            
            $userId = $this->userModel->create([
                'nama'     => $nama,
                'email'    => $email,
                'password' => $password,
                'role'     => ROLE_MAHASISWA
            ]);

            $this->mhsModel->create([
                'user_id'  => $userId,
                'nim'      => $nim,
                'angkatan' => $angkatan ?: date('Y'),
                'semester' => $semester ?: 1,
                'prodi_id' => $prodi_id ?: null,
                'no_hp'    => $no_hp,
                'alamat'   => $alamat
            ]);

            $db->commit();
            setFlash('success', 'Data Mahasiswa berhasil ditambahkan.');
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('danger', 'Gagal menambahkan data mahasiswa.');
        }

        redirect('?page=admin.mahasiswa');
    }

    public function mahasiswaUpdate(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI]);
        if (!isPost()) redirect('?page=admin.mahasiswa');
        verifyCsrf();

        $id       = (int)post('id');
        $nama     = trim(post('nama'));
        $email    = trim(post('email'));
        $password = post('password');
        $nim      = trim(post('nim'));
        $angkatan = trim(post('angkatan'));

        // Ekstrak angkatan dari NIM (4 digit pertama) jika angkatan kosong
        if (empty($angkatan) && strlen($nim) >= 4) {
            $year = substr($nim, 0, 4);
            if (is_numeric($year)) $angkatan = $year;
        }

        $semester = trim(post('semester'));
        $prodi_id = post('prodi_id');
        $no_hp    = trim(post('no_hp'));
        $alamat   = trim(post('alamat'));

        $mhs = $this->mhsModel->findById($id);
        if (!$mhs) {
            setFlash('danger', 'Mahasiswa tidak ditemukan.');
            redirect('?page=admin.mahasiswa');
        }

        $errors = [];
        if (empty($nama) || empty($email) || empty($nim)) {
            $errors[] = 'Nama, Email, dan NIM wajib diisi.';
        }
        
        if (!str_ends_with($email, '@uniku.ac.id')) {
            $errors[] = 'Email wajib menggunakan domain @uniku.ac.id';
        }

        if ($this->userModel->emailExists($email, $mhs['user_id'])) {
            $errors[] = 'Email sudah terdaftar pada user lain.';
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('?page=admin.mahasiswa');
        }

        $db = getDB();
        try {
            $db->beginTransaction();
            
            $userData = ['nama' => $nama, 'email' => $email];
            if (!empty($password)) {
                $userData['password'] = $password;
            }
            $this->userModel->update($mhs['user_id'], $userData);

            $this->mhsModel->update($id, [
                'nim'      => $nim,
                'angkatan' => $angkatan ?: date('Y'),
                'semester' => $semester ?: 1,
                'prodi_id' => $prodi_id ?: null,
                'no_hp'    => $no_hp,
                'alamat'   => $alamat
            ]);

            $db->commit();
            setFlash('success', 'Data Mahasiswa berhasil diperbarui.');
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('danger', 'Gagal memperbarui data mahasiswa.');
        }

        redirect('?page=admin.mahasiswa');
    }

    public function mahasiswaDelete(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI]);
        $id = (int)get('id');
        
        $mhs = $this->mhsModel->findById($id);
        if ($mhs) {
            try {
                // Delete user (akan otomatis cascade delete mahasiswa jika constraint di-set, 
                // tapi kita panggil manual untuk aman jika strict)
                $this->userModel->delete($mhs['user_id']);
                setFlash('success', 'Data Mahasiswa dan akun login berhasil dihapus.');
            } catch (Exception $e) {
                // Jika foreign key constraint gagal (misal sudah ada pengajuan)
                setFlash('danger', 'Data Mahasiswa tidak dapat dihapus karena sudah berelasi dengan data lain (misalnya Pengajuan Skripsi).');
            }
        }
        redirect('?page=admin.mahasiswa');
    }

    public function importMahasiswa(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI]);

        if (!isPost()) redirect('?page=admin.mahasiswa');
        verifyCsrf();

        if (empty($_FILES['file_csv']['name'])) {
            setFlash('danger', 'Pilih file Excel/CSV terlebih dahulu.');
            redirect('?page=admin.mahasiswa');
        }

        $file = $_FILES['file_csv']['tmp_name'];
        if (!file_exists($file)) {
            setFlash('danger', 'File tidak ditemukan.');
            redirect('?page=admin.mahasiswa');
        }

        try {
            $spreadsheet = IOFactory::load($file);
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();
            
            // Hapus header
            array_shift($rows);
            
            $success = 0;
            $failed  = 0;
            $db = getDB();

            foreach ($rows as $row) {
                if (empty($row[0]) || empty($row[2])) continue; // Skip jika Nama atau NIM kosong

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

                if (!empty($email) && !str_ends_with($email, '@uniku.ac.id')) {
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

                // Cek NIM duplikat
                $stmt = $db->prepare("SELECT id FROM mahasiswa WHERE nim = ?");
                $stmt->execute([$nim]);
                if ($stmt->fetchColumn()) {
                    $failed++;
                    continue;
                }

                try {
                    $db->beginTransaction();

                    // Buat User (Default password: nim)
                    $userId = $this->userModel->create([
                        'nama'     => $nama,
                        'email'    => $email,
                        'password' => $nim,
                        'role'     => ROLE_MAHASISWA
                    ]);

                    $this->mhsModel->create([
                        'user_id'  => $userId,
                        'nim'      => $nim,
                        'angkatan' => $angkatan ?: date('Y'),
                        'semester' => $semester ?: 1,
                        'prodi_id' => $prodiId,
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

            setFlash('success', "Import selesai. Berhasil: $success, Gagal: $failed.");
        } catch (Exception $e) {
            setFlash('danger', 'Gagal membaca file: ' . $e->getMessage());
        }

        redirect('?page=admin.mahasiswa');
    }


    public function dosen(): void {
        Auth::requireLogin();
        // Hanya Staff, Kaprodi, Dewan yang boleh akses data master dosen
        if (!in_array(Auth::role(), [ROLE_STAFF, ROLE_KAPRODI, ROLE_DEWAN, ROLE_SUPERADMIN])) {
            redirect('?page=403');
        }

        $searchInput = get('search', '');
        $search = is_string($searchInput) ? trim($searchInput) : '';
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;

        $prodiInput = get('prodi_id', '');
        $prodiId = is_string($prodiInput) && ctype_digit($prodiInput) && (int)$prodiInput > 0
            ? (int)$prodiInput
            : null;
        $jabatanInput = get('jabatan', '');
        $db = getDB();
        $jabatanList = $db->query("SELECT DISTINCT jabatan_akademik FROM dosen WHERE jabatan_akademik IS NOT NULL AND jabatan_akademik NOT IN ('', '-') ORDER BY jabatan_akademik ASC")
            ->fetchAll(PDO::FETCH_COLUMN);
        $jabatan = is_string($jabatanInput)
            && ($jabatanInput === '__none' || in_array($jabatanInput, $jabatanList, true))
            ? $jabatanInput
            : '';
        $statusInput = get('status', '');
        $status = is_string($statusInput) && in_array($statusInput, ['aktif', 'nonaktif'], true)
            ? $statusInput
            : '';
        $filters = [
            'prodi_id' => $prodiId,
            'jabatan_akademik' => $jabatan === '__none' ? '' : $jabatan,
            'jabatan_is_empty' => $jabatan === '__none',
            'is_active' => $status === '' ? null : (int)($status === 'aktif'),
        ];

        $total = $this->dosenModel->count($search, $filters);
        $pages = (int)ceil($total / $perPage);
        $list  = $this->dosenModel->getAll(false, $search, $page, $perPage, $filters);
        
        // Ambil role untuk setiap dosen
        $userModel = new User();
        foreach ($list as &$r) {
            $r['user_roles'] = $userModel->getRolesByEmail($r['email']);
        }
        
        $dosenModel = $this->dosenModel; // expose ke view

        $prodiList = $db->query("SELECT id, nama, kode FROM prodi ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        $roleList = $db->query("SELECT kode, nama FROM roles ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

        require BASE_PATH . '/views/admin/dosen/index.php';
    }

    public function bulkDosen(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI, ROLE_SUPERADMIN]);
        if (!isPost()) redirect('?page=admin.dosen');
        verifyCsrf();

        $submittedIds = $_POST['ids'] ?? [];
        $ids = [];
        if (is_array($submittedIds)) {
            foreach ($submittedIds as $submittedId) {
                if (!is_string($submittedId) && !is_int($submittedId)) continue;
                $id = filter_var($submittedId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if ($id !== false) $ids[] = $id;
            }
            $ids = array_values(array_unique($ids));
        }
        if (!$ids || count($ids) > 1000) {
            setFlash('danger', 'Pilih satu sampai 1.000 dosen untuk diproses.');
            redirect('?page=admin.dosen');
        }

        $actionInput = post('bulk_action');
        $action = is_string($actionInput) ? $actionInput : '';
        $db = getDB();
        if ($action === 'edit') {
            $updates = [];
            if (post('update_jabatan') === '1') {
                $jabatanInput = post('bulk_jabatan');
                $jabatan = is_string($jabatanInput) ? $jabatanInput : '';
                $jabatanOptions = $db->query("SELECT DISTINCT jabatan_akademik FROM dosen WHERE jabatan_akademik IS NOT NULL AND jabatan_akademik NOT IN ('', '-')")
                    ->fetchAll(PDO::FETCH_COLUMN);
                if ($jabatan === '__clear') {
                    $updates['jabatan_akademik'] = null;
                } elseif (in_array($jabatan, $jabatanOptions, true)) {
                    $updates['jabatan_akademik'] = $jabatan;
                } else {
                    setFlash('danger', 'Pilih jabatan akademik yang tersedia.');
                    redirect('?page=admin.dosen');
                }
            }
            if (post('update_status') === '1') {
                $statusInput = post('bulk_status');
                $status = is_string($statusInput) ? $statusInput : '';
                if (!in_array($status, ['0', '1'], true)) {
                    setFlash('danger', 'Pilih status dosen yang valid.');
                    redirect('?page=admin.dosen');
                }
                $updates['is_active'] = (int)$status;
            }
            if (!$updates) {
                setFlash('danger', 'Pilih minimal satu data yang akan diubah.');
                redirect('?page=admin.dosen');
            }

            try {
                $db->beginTransaction();
                $changed = $this->dosenModel->bulkUpdate($ids, $updates);
                $db->commit();
                setFlash('success', "Perubahan diterapkan. {$changed} data dosen berubah.");
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                error_log('Bulk edit dosen gagal: ' . $e->getMessage());
                setFlash('danger', 'Data dosen gagal diperbarui.');
            }
            redirect('?page=admin.dosen');
        }

        if ($action === 'delete') {
            try {
                $db->beginTransaction();
                foreach ($ids as $id) {
                    $dosen = $this->dosenModel->findById($id);
                    if (!$dosen) throw new RuntimeException('Data dosen tidak ditemukan.');

                    $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1 FOR UPDATE');
                    $stmt->execute([$dosen['email']]);
                    $userId = $stmt->fetchColumn();
                    $this->dosenModel->delete($id);
                    if ($userId) $this->userModel->delete((int)$userId);
                }
                $db->commit();
                setFlash('success', count($ids) . ' data dosen beserta akun terkait berhasil dihapus.');
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                error_log('Bulk delete dosen gagal: ' . $e->getMessage());
                setFlash('danger', 'Penghapusan dibatalkan. Periksa relasi data dosen yang dipilih.');
            }
            redirect('?page=admin.dosen');
        }

        setFlash('danger', 'Aksi massal tidak valid.');
        redirect('?page=admin.dosen');
    }

    public function dosenStore(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI, ROLE_SUPERADMIN]);

        if (!isPost()) redirect('?page=admin.dosen');
        verifyCsrf();

        $nama     = trim(post('nama'));
        $email    = trim(post('email'));
        $password = post('password');
        $nidn     = trim(post('nidn'));
        $prodi_id = post('prodi_id');
        $bidang   = trim(post('bidang_keahlian'));
        $no_hp    = trim(post('no_hp'));
        $jabatan  = trim(post('jabatan_akademik'));
        $pendidikan = trim(post('pendidikan'));

        $errors = [];
        if (empty($nama) || empty($email) || empty($password) || empty($nidn)) {
            $errors[] = 'Nama, Email, Password, dan NIDN wajib diisi.';
        }
        
        if (!str_ends_with($email, '@uniku.ac.id')) {
            $errors[] = 'Email wajib menggunakan domain @uniku.ac.id';
        }

        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Email sudah terdaftar pada user lain.';
        }

        // Cek NIDN duplikat
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM dosen WHERE nidn = ?");
        $stmt->execute([$nidn]);
        if ($stmt->fetchColumn()) {
            $errors[] = 'NIDN sudah terdaftar pada dosen lain.';
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('?page=admin.dosen');
        }

        try {
            $db->beginTransaction();
            
            // Buat User untuk dosen
            $roles = post('roles');
            if (!is_array($roles) || empty($roles)) {
                $roles = [post('role', ROLE_DOSEN)];
            }

            $userId = $this->userModel->create([
                'nama'     => $nama,
                'email'    => $email,
                'password' => $password,
                'role'     => $roles[0]
            ]);

            // Simpan multiple roles
            $this->userModel->setRoles($userId, $roles);

            
            $this->dosenModel->create([
                'nama'             => $nama,
                'nidn'             => $nidn,
                'email'            => $email,
                'prodi_id'         => $prodi_id ?: null,
                'bidang_keahlian'  => $bidang,
                'no_hp'            => $no_hp,
                'jabatan_akademik' => $jabatan,
                'pendidikan'       => $pendidikan
            ]);

            $db->commit();
            setFlash('success', 'Data Dosen berhasil ditambahkan.');
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('danger', 'Gagal menambahkan data dosen.');
        }

        redirect('?page=admin.dosen');
    }

    public function dosenUpdate(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI, ROLE_SUPERADMIN]);
        if (!isPost()) redirect('?page=admin.dosen');
        verifyCsrf();

        $id         = (int)post('id');
        $nama     = trim(post('nama'));
        $email    = trim(post('email'));
        $password = post('password');
        $nidn     = trim(post('nidn'));
        $prodi_id = post('prodi_id');
        $bidang   = trim(post('bidang_keahlian'));
        $no_hp    = trim(post('no_hp'));
        $jabatan  = trim(post('jabatan_akademik'));
        $pendidikan = trim(post('pendidikan'));

        $dosen = $this->dosenModel->findById($id);
        if (!$dosen) {
            setFlash('danger', 'Dosen tidak ditemukan.');
            redirect('?page=admin.dosen');
        }

        $errors = [];
        if (empty($nama) || empty($email) || empty($nidn)) {
            $errors[] = 'Nama, Email, dan NIDN wajib diisi.';
        }
        
        if (!str_ends_with($email, '@uniku.ac.id')) {
            $errors[] = 'Email wajib menggunakan domain @uniku.ac.id';
        }

        $db = getDB();
        // Cek NIDN duplikat
        $stmt = $db->prepare("SELECT id FROM dosen WHERE nidn = ? AND id != ?");
        $stmt->execute([$nidn, $id]);
        if ($stmt->fetchColumn()) {
            $errors[] = 'NIDN sudah terdaftar pada dosen lain.';
        }

        // Cari user yang emailnya cocok dengan dosen sebelumnya
        $stmtUser = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmtUser->execute([$dosen['email']]);
        $userId = $stmtUser->fetchColumn();

        if ($userId && $this->userModel->emailExists($email, $userId)) {
            $errors[] = 'Email sudah terdaftar pada user lain.';
        } elseif (!$userId && $this->userModel->emailExists($email)) {
            $errors[] = 'Email sudah terdaftar pada user lain.';
        }

        $canChangeRoles = Auth::role() === ROLE_SUPERADMIN;
        $roles = [];
        if ($canChangeRoles) {
            $submittedRoles = post('roles', []);
            if (!is_array($submittedRoles)) {
                $errors[] = 'Pilihan role tidak valid.';
            } else {
                foreach ($submittedRoles as $submittedRole) {
                    if (is_string($submittedRole) && $submittedRole !== '') $roles[] = $submittedRole;
                }
                $roles = array_values(array_unique($roles));
                $validRoleCodes = array_column($db->query('SELECT kode FROM roles')->fetchAll(PDO::FETCH_ASSOC), 'kode');
                if (array_diff($roles, $validRoleCodes)) {
                    $errors[] = 'Pilih role yang tersedia.';
                }
            }
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('?page=admin.dosen');
        }

        try {
            $db->beginTransaction();

                if ($userId) {
                    $userData = ['nama' => $nama, 'email' => $email];
                    if (!empty($password)) {
                        $userData['password'] = $password;
                    }
                    $this->userModel->update($userId, $userData);

                    if ($canChangeRoles && !empty($roles)) {
                        $this->userModel->setRoles($userId, $roles);
                    }
                } elseif (!empty($password)) {
                    if (!$canChangeRoles || empty($roles)) {
                        $roles = [ROLE_DOSEN];
                    }
                    $newUserId = $this->userModel->create([
                        'nama'     => $nama,
                        'email'    => $email,
                        'password' => $password,
                        'role'     => $roles[0]
                    ]);
                    $this->userModel->setRoles($newUserId, $roles);
                }

            $this->dosenModel->update($id, [
                'nama'             => $nama,
                'nidn'             => $nidn,
                'email'            => $email,
                'prodi_id'         => $prodi_id ?: null,
                'bidang_keahlian'  => $bidang,
                'no_hp'            => $no_hp,
                'jabatan_akademik' => $jabatan,
                'pendidikan'       => $pendidikan
            ]);

            $db->commit();
            setFlash('success', 'Data Dosen berhasil diperbarui.');
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('danger', 'Gagal memperbarui data dosen.');
        }

        redirect('?page=admin.dosen');
    }

    public function dosenDelete(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI, ROLE_SUPERADMIN]);
        $id = (int)get('id');
        
        $dosen = $this->dosenModel->findById($id);
        if ($dosen) {
            $db = getDB();
            try {
                $db->beginTransaction();
                
                // Cari User ID
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$dosen['email']]);
                $userId = $stmt->fetchColumn();

                $this->dosenModel->delete($id);

                if ($userId) {
                    $this->userModel->delete($userId);
                }
                
                $db->commit();
                setFlash('success', 'Data Dosen berhasil dihapus.');
            } catch (Exception $e) {
                $db->rollBack();
                setFlash('danger', 'Data Dosen tidak dapat dihapus karena sudah berelasi dengan data lain.');
            }
        }
        redirect('?page=admin.dosen');
    }

    public function importDosen(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI, ROLE_SUPERADMIN]);

        if (!isPost()) redirect('?page=admin.dosen');
        verifyCsrf();

        if (empty($_FILES['file_csv']['name'])) {
            setFlash('danger', 'Pilih file Excel/CSV terlebih dahulu.');
            redirect('?page=admin.dosen');
        }

        $file = $_FILES['file_csv']['tmp_name'];
        if (!file_exists($file)) {
            setFlash('danger', 'File tidak ditemukan.');
            redirect('?page=admin.dosen');
        }

        try {
            $spreadsheet = IOFactory::load($file);
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();
            
            // Hapus header
            array_shift($rows);

            $success = 0;
            $failed  = 0;
            $db = getDB();

            foreach ($rows as $row) {
                if (empty($row[0]) || empty($row[2])) continue;

                $nama       = trim($row[0] ?? '');
                $email      = trim($row[1] ?? '');
                $nidn       = trim($row[2] ?? '');
                $kd_prodi   = trim($row[3] ?? '');
                $bidang     = trim($row[4] ?? '');
                $no_hp      = trim($row[5] ?? '');
                $jabatan    = trim($row[6] ?? '');
                $pendidikan = trim($row[7] ?? '');
                $role_input = strtolower(trim($row[8] ?? ''));
                $password_input = trim($row[9] ?? '');

                if (empty($nama) || empty($nidn)) {
                    $failed++;
                    continue;
                }

                if (!empty($email) && !str_ends_with($email, '@uniku.ac.id')) {
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
                    $db->beginTransaction();

                    // Create user if email is provided
                    $userId = null;
                    if (!empty($email)) {
                        // Check if user already exists
                        $stmtUser = $db->prepare("SELECT id FROM users WHERE email = ?");
                        $stmtUser->execute([$email]);
                        $userId = $stmtUser->fetchColumn();

                        if (!$userId) {
                            // Tentukan role: dosen, dewan, kaprodi
                            $validRoles = [ROLE_DOSEN, ROLE_DEWAN, ROLE_KAPRODI, ROLE_STAFF];
                            $finalRole = in_array($role_input, $validRoles) ? $role_input : ROLE_DOSEN;

                            $userId = $this->userModel->create([
                                'nama'     => $nama,
                                'email'    => $email,
                                'password' => !empty($password_input) ? $password_input : $nidn, // Gunakan password input atau default NIDN
                                'role'     => $finalRole
                            ]);
                        }
                    }

                    $this->dosenModel->create([
                        'nama'            => $nama,
                        'nidn'            => $nidn,
                        'email'           => $email,
                        'prodi_id'        => $prodiId ?: null,
                        'bidang_keahlian' => $bidang,
                        'no_hp'           => $no_hp,
                        'jabatan_akademik' => $jabatan,
                        'pendidikan'       => $pendidikan
                    ]);
                    
                    $db->commit();
                    $success++;
                } catch (Exception $e) {
                    if ($db->inTransaction()) $db->rollBack();
                    $failed++;
                }
            }

            setFlash('success', "Import selesai. Berhasil: $success, Gagal: $failed.");
        } catch (Exception $e) {
            setFlash('danger', 'Gagal membaca file: ' . $e->getMessage());
        }

        redirect('?page=admin.dosen');
    }


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
