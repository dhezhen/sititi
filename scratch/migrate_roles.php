<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';

$db = getDB();

try {

    // 1. Ubah tipe data role dari ENUM menjadi VARCHAR(50)
    $db->exec("ALTER TABLE `users` MODIFY `role` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mahasiswa'");

    // 2. Buat tabel roles
    $db->exec("CREATE TABLE IF NOT EXISTS `roles` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `kode` VARCHAR(50) NOT NULL,
        `nama` VARCHAR(100) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `kode` (`kode`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 3. Buat tabel menus
    $db->exec("CREATE TABLE IF NOT EXISTS `menus` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `kode_page` VARCHAR(100) NOT NULL,
        `judul` VARCHAR(100) NOT NULL,
        `icon` VARCHAR(50) DEFAULT NULL,
        `urutan` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        UNIQUE KEY `kode_page` (`kode_page`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 4. Buat tabel role_menus
    $db->exec("CREATE TABLE IF NOT EXISTS `role_menus` (
        `role_id` INT UNSIGNED NOT NULL,
        `menu_id` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`role_id`, `menu_id`),
        FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 5. Seed default Roles
    $rolesData = [
        ['mahasiswa', 'Mahasiswa'],
        ['staff', 'Staff / Verifikator'],
        ['kaprodi', 'Ketua Program Studi'],
        ['dewan', 'Dewan Pembimbing'],
        ['superadmin', 'Super Admin']
    ];
    $stmtRole = $db->prepare("INSERT IGNORE INTO roles (kode, nama) VALUES (?, ?)");
    foreach ($rolesData as $rd) {
        $stmtRole->execute($rd);
    }

    // 6. Seed default Menus
    // Kita list semua base menus:
    $menusData = [
        // Mahasiswa
        ['pengajuan', 'Daftar Pengajuan', 'bi bi-file-earmark-text', 10],
        ['pengajuan.create', 'Buat Pengajuan', 'bi bi-plus-circle', 11],
        // Staff
        ['staff', 'Verifikasi Berkas', 'bi bi-clipboard-check', 20],
        ['admin.mahasiswa', 'Data Mahasiswa', 'bi bi-people', 21],
        ['admin.dosen', 'Data Dosen', 'bi bi-person-badge', 22],
        // Kaprodi
        ['kaprodi', 'Review Pengajuan', 'bi bi-eye', 30],
        ['admin.periode', 'Data Periode', 'bi bi-calendar3', 31],
        // Dewan
        ['dewan', 'Tentukan Pembimbing', 'bi bi-people-fill', 40],
        // Superadmin
        ['superadmin.roles', 'Manajemen Role', 'bi bi-shield-lock', 90],
        ['superadmin.menus', 'Manajemen Menu', 'bi bi-list-nested', 91]
    ];
    $stmtMenu = $db->prepare("INSERT IGNORE INTO menus (kode_page, judul, icon, urutan) VALUES (?, ?, ?, ?)");
    foreach ($menusData as $md) {
        $stmtMenu->execute($md);
    }

    // 7. Seed Role_Menus (Mappping Default)
    // Ambil ID Roles
    $rolesMap = [];
    $res = $db->query("SELECT id, kode FROM roles")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $r) { $rolesMap[$r['kode']] = $r['id']; }

    // Ambil ID Menus
    $menusMap = [];
    $res = $db->query("SELECT id, kode_page FROM menus")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $m) { $menusMap[$m['kode_page']] = $m['id']; }

    $roleMenus = [
        'mahasiswa' => ['pengajuan', 'pengajuan.create'],
        'staff' => ['staff', 'admin.mahasiswa', 'admin.dosen'],
        'kaprodi' => ['kaprodi', 'admin.dosen', 'admin.periode'],
        'dewan' => ['dewan', 'admin.dosen'],
        'superadmin' => ['superadmin.roles', 'superadmin.menus']
    ];

    $stmtRm = $db->prepare("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (?, ?)");
    foreach ($roleMenus as $roleCode => $menuCodes) {
        if (!isset($rolesMap[$roleCode])) continue;
        $roleId = $rolesMap[$roleCode];
        foreach ($menuCodes as $mc) {
            if (!isset($menusMap[$mc])) continue;
            $stmtRm->execute([$roleId, $menusMap[$mc]]);
        }
    }

    // 8. Seed Superadmin User
    $stmtCheckSuper = $db->query("SELECT id FROM users WHERE email = 'superadmin@uniku.ac.id'");
    if (!$stmtCheckSuper->fetchColumn()) {
        $hash = password_hash('password', PASSWORD_BCRYPT);
        $db->exec("INSERT INTO users (nama, email, password, role) VALUES ('Super Admin', 'superadmin@uniku.ac.id', '$hash', 'superadmin')");
    }

    echo "Migration Success!\n";

} catch (Exception $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
}
