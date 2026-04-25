<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();

// Cek apakah menu sudah ada
$stmt = $db->prepare("SELECT id FROM menus WHERE kode_page = 'kaprodi.history'");
$stmt->execute();
if (!$stmt->fetch()) {
    $db->query("INSERT INTO menus (judul, icon, urutan, kode_page) VALUES ('Histori Pengajuan', 'bi bi-clock-history', 3, 'kaprodi.history')");
    $menuId = $db->lastInsertId();
    
    // get role_id for kaprodi
    $roleId = $db->query("SELECT id FROM roles WHERE kode = 'kaprodi'")->fetchColumn();
    if ($roleId) {
        $db->prepare("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)")->execute([$roleId, $menuId]);
        echo "Menu inserted successfully.\n";
    }
} else {
    echo "Menu already exists.\n";
}
