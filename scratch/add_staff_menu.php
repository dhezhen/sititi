<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';

$db = getDB();

try {
    $db->beginTransaction();

    // 1. Tambah Menu admin.staff
    $stmt = $db->prepare("INSERT INTO menus (kode_page, judul, icon, urutan) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE judul=?, icon=?, urutan=?");
    $stmt->execute(['admin.staff', 'Data Staff', 'bi bi-person-workspace', 23, 'Data Staff', 'bi bi-person-workspace', 23]);
    $menuId = $db->query("SELECT id FROM menus WHERE kode_page = 'admin.staff'")->fetchColumn();

    // 2. Beri akses ke Superadmin (role_id = 5 berdasarkan sititi_db.sql)
    // Tapi lebih aman cari role_id berdasarkan kode 'superadmin'
    $roleId = $db->query("SELECT id FROM roles WHERE kode = 'superadmin'")->fetchColumn();

    if ($menuId && $roleId) {
        $stmtAccess = $db->prepare("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (?, ?)");
        $stmtAccess->execute([$roleId, $menuId]);
    }

    $db->commit();
    echo "Menu 'Data Staff' added and access granted to Superadmin.\n";
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
