<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';

$db = getDB();
$dosen = $db->query("SELECT * FROM dosen")->fetchAll(PDO::FETCH_ASSOC);
echo "Total Dosen: " . count($dosen) . "\n";
print_r($dosen);
