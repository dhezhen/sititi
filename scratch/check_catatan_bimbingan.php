<?php
require 'config/database.php';
$db = getDB();
echo "--- catatan_bimbingan table structure ---\n";
$stmt = $db->query("DESCRIBE catatan_bimbingan");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
