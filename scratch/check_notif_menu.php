<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
$stmt = $db->query("SELECT * FROM menus WHERE kode_page = 'notif'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
