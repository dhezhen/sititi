<?php
require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
$db = getDB();
$roles = $db->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($roles, JSON_PRETTY_PRINT);
