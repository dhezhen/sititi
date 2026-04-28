<?php
require 'config/database.php';
$db = getDB();
echo "--- pengajuan_judul table structure ---\n";
$stmt = $db->query("DESCRIBE pengajuan_judul");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
