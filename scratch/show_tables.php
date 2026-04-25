<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
$stmt = $db->query("SHOW TABLES");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
