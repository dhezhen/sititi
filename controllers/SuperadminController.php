<?php
/**
 * Superadmin Controller - Manajemen Role & Menu Akses
 * File: controllers/SuperadminController.php
 */

class SuperadminController {

    public function __construct() {
        Auth::requireRole(ROLE_SUPERADMIN);
    }

    // ── ROLES ────────────────────────────────────────────────────────
    
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

    // ── MENUS ────────────────────────────────────────────────────────

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

    // ── ROLE MENUS (HAK AKSES) ───────────────────────────────────────

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

    // ── DATABASE BACKUP ──────────────────────────────────────────────

    public function database(): void {
        $db = getDB();
        $tables = $db->query("SHOW TABLE STATUS")->fetchAll(PDO::FETCH_ASSOC);
        $pageTitle = 'Manajemen Database';
        require BASE_PATH . '/views/superadmin/database.php';
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

            // Get Data
            $stmt = $db->query("SELECT * FROM `$table`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($rows) > 0) {
                $sql .= "-- Data for table `$table`\n";
                foreach ($rows as $row) {
                    $keys = array_keys($row);
                    $values = array_values($row);
                    
                    // Escape values
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
        
        // Set headers for download
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $sql;
        exit;
    }
}
