<?php
/**
 * Superadmin Controller - Manajemen Role & Menu Akses
 * File: controllers/SuperadminController.php
 */

class SuperadminController {

    public function __construct() {
        Auth::requireRole(ROLE_SUPERADMIN);
    }

    
    public function roles(): void {
        $db = getDB();
        $list = $db->query("SELECT * FROM roles ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        require BASE_PATH . '/views/superadmin/roles/index.php';
    }

    public function roleStore(): void {
        if (!isPost()) redirect('?page=superadmin.roles');
        verifyCsrf();

        $kode = strtolower(trim(post('kode')));
        $nama = trim(post('nama'));

        if (empty($kode) || empty($nama)) {
            setFlash('danger', 'Semua field wajib diisi.');
            redirect('?page=superadmin.roles');
        }

        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM roles WHERE kode = ?");
        $stmt->execute([$kode]);
        if ($stmt->fetchColumn()) {
            setFlash('danger', 'Kode Role sudah digunakan.');
            redirect('?page=superadmin.roles');
        }

        $stmt = $db->prepare("INSERT INTO roles (kode, nama) VALUES (?, ?)");
        $stmt->execute([$kode, $nama]);

        setFlash('success', 'Role berhasil ditambahkan.');
        redirect('?page=superadmin.roles');
    }

    public function roleDelete(): void {
        $id = (int)get('id');
        if ($id) {
            $db = getDB();
            // Mencegah hapus superadmin atau role inti jika diperlukan
            $stmt = $db->prepare("DELETE FROM roles WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Role berhasil dihapus.');
        }
        redirect('?page=superadmin.roles');
    }


    public function menus(): void {
        $db = getDB();
        $list = $db->query("SELECT * FROM menus ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC);
        require BASE_PATH . '/views/superadmin/menus/index.php';
    }

    public function menuStore(): void {
        if (!isPost()) redirect('?page=superadmin.menus');
        verifyCsrf();

        $kode_page = trim(post('kode_page'));
        $judul     = trim(post('judul'));
        $icon      = trim(post('icon'));
        $urutan    = (int)post('urutan');

        if (empty($kode_page) || empty($judul)) {
            setFlash('danger', 'Kode Page dan Judul wajib diisi.');
            redirect('?page=superadmin.menus');
        }

        $db = getDB();
        $stmt = $db->prepare("INSERT INTO menus (kode_page, judul, icon, urutan) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE judul=?, icon=?, urutan=?");
        $stmt->execute([$kode_page, $judul, $icon, $urutan, $judul, $icon, $urutan]);

        setFlash('success', 'Menu berhasil disimpan.');
        redirect('?page=superadmin.menus');
    }

    public function menuDelete(): void {
        $id = (int)get('id');
        if ($id) {
            $db = getDB();
            $stmt = $db->prepare("DELETE FROM menus WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Menu berhasil dihapus.');
        }
        redirect('?page=superadmin.menus');
    }


    public function roleMenus(): void {
        $db = getDB();
        $roles = $db->query("SELECT * FROM roles ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        $menus = $db->query("SELECT * FROM menus ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil mapping saat ini
        $roleMenusRaw = $db->query("SELECT role_id, menu_id FROM role_menus")->fetchAll(PDO::FETCH_ASSOC);
        
        // Bentuk matrix: $matrix[role_id][menu_id] = true
        $matrix = [];
        foreach ($roleMenusRaw as $rm) {
            $matrix[$rm['role_id']][$rm['menu_id']] = true;
        }

        require BASE_PATH . '/views/superadmin/roles/hak_akses.php';
    }

    public function roleMenusUpdate(): void {
        if (!isPost()) redirect('?page=superadmin.role_menus');
        verifyCsrf();

        $access = $_POST['access'] ?? []; // array format access[role_id][menu_id] = "on"
        
        $db = getDB();
        try {
            $db->beginTransaction();
            
            // Hapus semua mapping terlebih dahulu
            $db->exec("DELETE FROM role_menus");

            // Insert mapping yang dicentang
            $stmt = $db->prepare("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)");
            foreach ($access as $roleId => $menuList) {
                foreach ($menuList as $menuId => $val) {
                    if ($val === 'on') {
                        $stmt->execute([$roleId, $menuId]);
                    }
                }
            }

            $db->commit();
            setFlash('success', 'Hak akses berhasil diperbarui.');
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('danger', 'Gagal memperbarui hak akses.');
        }

        redirect('?page=superadmin.role_menus');
    }


    public function database(): void {
        $db = getDB();
        $tables = $db->query("SHOW TABLE STATUS")->fetchAll(PDO::FETCH_ASSOC);
        $pageTitle = 'Manajemen Database';
        require BASE_PATH . '/views/superadmin/database.php';
    }

    public function pengumuman(): void {
        $model = new Pengumuman();
        $roles = $model->getRoles();
        $roleNameByCode = array_column($roles, 'nama', 'kode');
        $pengumumanList = $model->getRecent();
        $smtpEnabled = (new SMTPSettings())->isEnabled();
        $mailConfigured = Mailer::isConfigured();
        $mailFromAddress = Mailer::fromAddress();
        $pageTitle = 'Pengumuman';
        require BASE_PATH . '/views/superadmin/pengumuman.php';
    }

    public function smtpSettings(): void {
        $smtpModel = new SMTPSettings();
        $settings = $smtpModel->get();
        $smtpConfigured = $smtpModel->isConfigured();
        unset($settings['password'], $settings['password_encrypted']);
        $pageTitle = 'Pengaturan Email';
        require BASE_PATH . '/views/superadmin/smtp.php';
    }

    public function saveSmtpSettings(): void {
        if (!isPost()) redirect('?page=superadmin.smtp');
        verifyCsrf();

        $host = trim((string)post('host'));
        $port = (int)post('port');
        $encryption = (string)post('encryption');
        $username = trim((string)post('username'));
        $password = (string)post('smtp_password');
        $fromEmail = trim((string)post('from_email'));
        $fromName = trim((string)post('from_name'));
        $enabled = post('enabled') === '1';
        $clearPassword = post('clear_password') === '1';

        $hostValid = strlen($host) <= 253
            && preg_match('/^[A-Za-z0-9.-]+$/', $host) === 1
            && !str_contains($host, '..')
            && !str_starts_with($host, '.')
            && !str_ends_with($host, '.');
        if ($host === '' || !$hostValid || $port < 1 || $port > 65535
            || !in_array($encryption, ['tls', 'ssl', 'none'], true)
            || ($encryption === 'none' && $username !== '')
            || strlen($username) > 255
            || strlen($password) > 2048
            || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)
            || strlen($fromEmail) > 150
            || strlen($fromName) > 150
            || preg_match('/[\r\n]/', $fromName)) {
            setFlash('danger', 'Periksa kembali host, port, keamanan koneksi, dan alamat pengirim. Autentikasi SMTP memerlukan TLS atau SSL.');
            redirect('?page=superadmin.smtp');
        }

        try {
            (new SMTPSettings())->save([
                'enabled' => $enabled,
                'host' => $host,
                'port' => $port,
                'encryption' => $encryption,
                'username' => $username,
                'from_email' => $fromEmail,
                'from_name' => $fromName,
            ], $password, $clearPassword);
            setFlash('success', 'Pengaturan SMTP berhasil disimpan.');
        } catch (Throwable $e) {
            error_log('Gagal menyimpan pengaturan SMTP: ' . $e->getMessage());
            setFlash('danger', 'Pengaturan SMTP tidak tersimpan. Pastikan SMTP_ENCRYPTION_KEY tersedia di server.');
        }
        redirect('?page=superadmin.smtp');
    }

    public function testSmtpSettings(): void {
        if (!isPost()) redirect('?page=superadmin.smtp');
        verifyCsrf();

        $testEmail = trim((string)post('test_email'));
        $smtpModel = new SMTPSettings();
        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            setFlash('danger', 'Masukkan alamat email tujuan yang valid.');
            redirect('?page=superadmin.smtp');
        }
        if (!$smtpModel->isConfigured()) {
            setFlash('danger', 'SMTP belum aktif atau konfigurasinya belum lengkap.');
            redirect('?page=superadmin.smtp');
        }

        $body = "Email ini mengonfirmasi bahwa pengaturan SMTP SiTiTi berhasil digunakan.\n\nWaktu pengujian: " . date('d-m-Y H:i:s');
        if (Mailer::send($testEmail, APP_NAME . ': Email percobaan SMTP', $body)) {
            setFlash('success', 'Server SMTP menerima email percobaan untuk diproses.');
        } else {
            setFlash('danger', 'Email percobaan gagal dikirim. Periksa host, port, keamanan, kredensial, dan log server.');
        }
        redirect('?page=superadmin.smtp');
    }

    public function storePengumuman(): void {
        if (!isPost()) redirect('?page=superadmin.pengumuman');
        verifyCsrf();

        $title = trim(post('judul'));
        $message = trim(post('isi'));
        $allUsers = post('semua_pengguna') === '1';
        $sendEmail = post('kirim_email') === '1';
        $submittedRoles = post('roles', []);
        $roles = is_array($submittedRoles)
            ? array_values(array_unique(array_filter(array_map('strval', $submittedRoles))))
            : [];

        $model = new Pengumuman();
        $availableRoles = $model->getRoles();
        $validRoleCodes = array_column($availableRoles, 'kode');

        if ($title === '' || $message === '') {
            setFlash('danger', 'Judul dan isi pengumuman wajib diisi.');
            redirect('?page=superadmin.pengumuman');
        }

        $titleLength = function_exists('mb_strlen') ? mb_strlen($title) : strlen($title);
        $messageLength = function_exists('mb_strlen') ? mb_strlen($message) : strlen($message);
        if ($titleLength > 180 || $messageLength > 10000) {
            setFlash('danger', 'Judul maksimal 180 karakter dan isi maksimal 10.000 karakter.');
            redirect('?page=superadmin.pengumuman');
        }

        if (!$allUsers && empty($roles)) {
            setFlash('danger', 'Pilih semua pengguna atau minimal satu role penerima.');
            redirect('?page=superadmin.pengumuman');
        }

        if (array_diff($roles, $validRoleCodes)) {
            setFlash('danger', 'Pilihan role penerima tidak valid.');
            redirect('?page=superadmin.pengumuman');
        }

        if ($allUsers) {
            $roles = [];
        }

        $recipients = $model->getRecipients($roles);
        if (empty($recipients)) {
            setFlash('danger', 'Tidak ada pengguna aktif pada pilihan penerima tersebut.');
            redirect('?page=superadmin.pengumuman');
        }

        try {
            $announcementId = $model->createAndNotify(
                $title,
                $message,
                $roles,
                (int)Auth::id(),
                $sendEmail,
                $recipients
            );
        } catch (Throwable $e) {
            error_log('Gagal menyimpan pengumuman: ' . $e->getMessage());
            setFlash('danger', 'Pengumuman gagal disimpan. Silakan coba lagi.');
            redirect('?page=superadmin.pengumuman');
        }

        $recipientCount = count($recipients);
        if (!$sendEmail) {
            setFlash('success', "Pengumuman dan notifikasi internal telah dikirim ke {$recipientCount} pengguna.");
            redirect('?page=superadmin.pengumuman');
        }

        if (!Mailer::isConfigured()) {
            $model->updateEmailResult($announcementId, 'belum_dikonfigurasi', 0, 0);
            setFlash(
                'warning',
                "Notifikasi internal telah dikirim ke {$recipientCount} pengguna. Email belum dikirim karena MAIL_FROM_ADDRESS belum dikonfigurasi."
            );
            redirect('?page=superadmin.pengumuman');
        }

        $accepted = 0;
        $failed = 0;
        $subject = APP_NAME . ': ' . $title;
        foreach ($recipients as $recipient) {
            $emailBody = "Halo {$recipient['nama']},\n\n{$message}\n\n" . APP_NAME;
            if (Mailer::send((string)$recipient['email'], $subject, $emailBody)) {
                $accepted++;
            } else {
                $failed++;
            }
        }

        $emailStatus = $accepted === 0
            ? 'gagal'
            : ($failed === 0 ? 'diterima' : 'sebagian_gagal');
        $model->updateEmailResult($announcementId, $emailStatus, $accepted, $failed);

        if ($failed === 0) {
            setFlash('success', "Notifikasi internal dikirim ke {$recipientCount} pengguna. Server email menerima {$accepted} email untuk diproses.");
        } else {
            setFlash(
                'warning',
                "Notifikasi internal dikirim ke {$recipientCount} pengguna. Server email menerima {$accepted} email; {$failed} email gagal diserahkan."
            );
        }
        redirect('?page=superadmin.pengumuman');
    }

    public function backupDatabase(): void {
        $db = getDB();
        
        // Ambil semua tabel
        $tables = [];
        $result = $db->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql = "-- SiTiTi Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Host: " . DB_HOST . "\n";
        $sql .= "-- Database: " . DB_NAME . "\n\n";
        
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Drop Table if exists
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            
            // Create Table structure
            $res = $db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
            $sql .= $res['Create Table'] . ";\n\n";

            $stmt = $db->query("SELECT * FROM `$table`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($rows) > 0) {
                $sql .= "-- Data for table `$table`\n";
                foreach ($rows as $row) {
                    $keys = array_keys($row);
                    $values = array_values($row);
                    
                    $escapedValues = array_map(function($v) use ($db) {
                        if ($v === null) return 'NULL';
                        return $db->quote($v);
                    }, $values);
                    
                    $sql .= "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'backup_db_' . DB_NAME . '_' . date('Ymd_His') . '.sql';
        
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $sql;
        exit;
    }
}
