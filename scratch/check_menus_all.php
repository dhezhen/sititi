<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
$stmt = $db->query("SELECT * FROM menus");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
