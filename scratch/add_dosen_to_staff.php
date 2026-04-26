<?php
/**
 * Add 'admin.dosen' menu back to staff role
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();

$stmtRole = $db->prepare("SELECT id FROM roles WHERE kode = 'staff'");
$stmtRole->execute();
$staffRole = $stmtRole->fetch();

if (!$staffRole) {
    die("Role staff not found.");
}

$dosenPages = ['admin.dosen', 'admin.dosen.store', 'admin.dosen.update', 'admin.dosen.delete', 'admin.dosen.import'];

foreach ($dosenPages as $p) {
    $stmtMenu = $db->prepare("SELECT id FROM menus WHERE kode_page = ?");
    $stmtMenu->execute([$p]);
    $menu = $stmtMenu->fetch();

    if ($menu) {
        $stmtCheck = $db->prepare("SELECT 1 FROM role_menus WHERE role_id = ? AND menu_id = ?");
        $stmtCheck->execute([$staffRole['id'], $menu['id']]);
        if (!$stmtCheck->fetch()) {
            $db->prepare("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)")
               ->execute([$staffRole['id'], $menu['id']]);
            echo "Added $p to staff.\n";
        } else {
            echo "Already had $p.\n";
        }
    } else {
        echo "Menu $p not found.\n";
    }
}
echo "Done.\n";
