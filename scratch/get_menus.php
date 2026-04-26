<?php
require 'config/database.php';
$db = getDB();
$stmt = $db->query('SELECT r.kode as role, m.judul, m.kode_page, m.urutan FROM role_menus rm JOIN roles r ON rm.role_id = r.id JOIN menus m ON rm.menu_id = m.id ORDER BY r.kode, m.urutan');
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$roles = [];
foreach ($data as $row) {
    $roles[$row['role']][] = $row;
}
print_r($roles);
