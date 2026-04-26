<?php
require 'config/database.php';
$db = getDB();

// Find kaprodi role id
$stmt = $db->query("SELECT id FROM roles WHERE kode = 'kaprodi'");
$roleKaprodi = $stmt->fetchColumn();

// Find dewan and dewan.history menu ids
$stmt = $db->query("SELECT id FROM menus WHERE kode_page IN ('dewan', 'dewan.history')");
$dewanMenus = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($roleKaprodi && !empty($dewanMenus)) {
    foreach ($dewanMenus as $menuId) {
        $stmt = $db->prepare("DELETE FROM role_menus WHERE role_id = ? AND menu_id = ?");
        $stmt->execute([$roleKaprodi, $menuId]);
    }
    echo "Removed dewan menus from Kaprodi successfully.\n";
} else {
    echo "Role or menus not found.\n";
}
