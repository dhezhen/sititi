<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
$stmt = $db->query("SELECT id, nama, kode FROM prodi ORDER BY nama ASC");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
