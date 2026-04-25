<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
$stmt = $db->query("SELECT COUNT(*) FROM mahasiswa");
echo "Total Mahasiswa: " . $stmt->fetchColumn() . "\n";

$stmt = $db->query("SELECT angkatan, COUNT(*) FROM mahasiswa GROUP BY angkatan");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
