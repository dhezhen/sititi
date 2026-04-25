<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();

// Get role_id for dewan
$roleId = $db->query("SELECT id FROM roles WHERE kode = 'dewan'")->fetchColumn();

if ($roleId) {
    // Menu ID 5 is 'Data Dosen'
    $db->prepare("DELETE FROM role_menus WHERE role_id = ? AND menu_id = 5")->execute([$roleId]);
    echo "Menu 'Data Dosen' removed from Dewan role.\n";
}
