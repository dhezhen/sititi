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

    // ── STAFF MANAGEMENT ──────────────────────────────────────────

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

    // ── MAHASISWA ───────────────────────────────────────────────────

    public function mahasiswa(): void {
        Auth::requireRole([ROLE_STAFF, ROLE_KAPRODI]); // Staff dan Kaprodi boleh akses

        $search  = get('search', '');
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        
        $total = $this->mhsModel->count($search);
        $pages = (int)ceil($total / $perPage);
        $list  = $this->mhsModel->getAll($search, $page, $perPage);

        // Ambil daftar prodi untuk dropdown
        $db = getDB();
        $prodiList = $db->query("SELECT id, nama, kode FROM prodi ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

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
            
            // Update User
            $userData = ['nama' => $nama, 'email' => $email];
            if (!empty($password)) {
                $userData['password'] = $password;
            }
            $this->userModel->update($mhs['user_id'], $userData);

            // Update Mahasiswa
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

    // ── DOSEN ───────────────────────────────────────────────────────

    public function dosen(): void {
        Auth::requireLogin();
        // Hanya Staff, Kaprodi, Dewan yang boleh akses data master dosen
        if (!in_array(Auth::role(), [ROLE_STAFF, ROLE_KAPRODI, ROLE_DEWAN, ROLE_SUPERADMIN])) {
            redirect('?page=403');
        }

        $search  = get('search', '');
        $page    = max(1, (int)get('p', 1));
        $perPage = 15;
        
        $total = $this->dosenModel->count($search);
        $pages = (int)ceil($total / $perPage);
        $list  = $this->dosenModel->getAll(false, $search, $page, $perPage);
        
        // Ambil role untuk setiap dosen
        $userModel = new User();
        foreach ($list as &$r) {
            $r['user_roles'] = $userModel->getRolesByEmail($r['email']);
        }
        
        $dosenModel = $this->dosenModel; // expose ke view

        // Ambil daftar prodi untuk dropdown
        $db = getDB();
        $prodiList = $db->query("SELECT id, nama, kode FROM prodi ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

        require BASE_PATH . '/views/admin/dosen/index.php';
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

            // Karena role dewan sudah ada untuk mengurus pembimbing, kita set ke dewan atau biarkan null (but user table need role).
            // Actually, dosen in this system usually acts as Dewan or Kaprodi. Let's just set it to 'dosen' (even if not strictly in app.php, or use 'dewan').
            // Wait, looking at app.php, there's no ROLE_DOSEN. We will set role to 'dewan' as a placeholder or we can just update userModel if needed.
            // Let's check `User.php` role enum if it exists. No enum. We'll set role as 'dewan' so they can login. Or just don't create user if not requested? "termasuk password untuk masuk... dosen mahasiswa staff". Let's use 'dewan' for dosen if they don't have a specific role, or let's just make it 'dosen'.
            
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

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('?page=admin.dosen');
        }

        try {
            $db->beginTransaction();

                if ($userId) {
                    // Update User
                    $userData = ['nama' => $nama, 'email' => $email];
                    if (!empty($password)) {
                        $userData['password'] = $password;
                    }
                    $this->userModel->update($userId, $userData);

                    // Sync Multiple Roles
                    $roles = post('roles');
                    if (is_array($roles) && !empty($roles)) {
                        $this->userModel->setRoles($userId, $roles);
                    }
                } elseif (!empty($password)) {
                    // Buat user jika belum ada
                    $roles = post('roles');
                    if (!is_array($roles) || empty($roles)) {
                        $roles = [post('role', ROLE_DOSEN)];
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
