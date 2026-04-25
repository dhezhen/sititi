<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
$stmt = $db->query("SHOW CREATE TABLE users");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
