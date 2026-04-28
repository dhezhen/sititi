<?php
require 'config/database.php';
$db = getDB();
echo "--- dosen table structure ---\n";
$stmt = $db->query("DESCRIBE dosen");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
