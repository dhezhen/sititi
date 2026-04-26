<?php
/**
 * Script to add "Siap Diajukan ke Fakultas" menu for Staff role
 * Run once: php scratch/add_siap_fakultas_menu.php
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Check if menu already exists
$stmt = $db->prepare("SELECT id FROM menus WHERE kode_page = ?");
$stmt->execute(['staff.siap_fakultas']);
$existing = $stmt->fetch();

if ($existing) {
    echo "Menu 'staff.siap_fakultas' already exists (ID: {$existing['id']}).\n";
    $menuId = $existing['id'];
} else {
    // Get max urutan for ordering
    $maxUrutan = $db->query("SELECT COALESCE(MAX(urutan), 0) FROM menus")->fetchColumn();
    
    // Also get the urutan of the existing staff menu to place it right after
    $stmtStaff = $db->prepare("SELECT urutan FROM menus WHERE kode_page = ?");
    $stmtStaff->execute(['staff']);
    $staffMenu = $stmtStaff->fetch();
    $newUrutan = $staffMenu ? $staffMenu['urutan'] + 1 : $maxUrutan + 1;

    // Shift existing menus after this position
    $db->prepare("UPDATE menus SET urutan = urutan + 1 WHERE urutan >= ?")->execute([$newUrutan]);

    // Insert the new menu
    $stmt = $db->prepare(
        "INSERT INTO menus (judul, kode_page, icon, urutan) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        'Siap Ajukan Fakultas',
        'staff.siap_fakultas',
        'bi bi-send-check',
        $newUrutan
    ]);
    $menuId = $db->lastInsertId();
    echo "Menu 'staff.siap_fakultas' created (ID: {$menuId}, urutan: {$newUrutan}).\n";
}

// Assign menu to Staff role
$stmtRole = $db->prepare("SELECT id FROM roles WHERE kode = ?");
$stmtRole->execute(['staff']);
$staffRole = $stmtRole->fetch();

if ($staffRole) {
    // Check if already assigned
    $stmtCheck = $db->prepare("SELECT 1 FROM role_menus WHERE role_id = ? AND menu_id = ?");
    $stmtCheck->execute([$staffRole['id'], $menuId]);
    
    if ($stmtCheck->fetch()) {
        echo "Menu already assigned to 'staff' role.\n";
    } else {
        $db->prepare("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)")
           ->execute([$staffRole['id'], $menuId]);
        echo "Menu assigned to 'staff' role.\n";
    }
} else {
    echo "WARNING: 'staff' role not found in roles table. Please assign manually via Superadmin.\n";
}

// Also assign to kaprodi role (they can see it too)
$stmtKap = $db->prepare("SELECT id FROM roles WHERE kode = ?");
$stmtKap->execute(['kaprodi']);
$kaprodiRole = $stmtKap->fetch();

if ($kaprodiRole) {
    $stmtCheck = $db->prepare("SELECT 1 FROM role_menus WHERE role_id = ? AND menu_id = ?");
    $stmtCheck->execute([$kaprodiRole['id'], $menuId]);
    
    if ($stmtCheck->fetch()) {
        echo "Menu already assigned to 'kaprodi' role.\n";
    } else {
        $db->prepare("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)")
           ->execute([$kaprodiRole['id'], $menuId]);
        echo "Menu assigned to 'kaprodi' role.\n";
    }
} else {
    echo "WARNING: 'kaprodi' role not found.\n";
}

echo "\nDone! The menu should now appear in the sidebar for Staff and Kaprodi users.\n";
