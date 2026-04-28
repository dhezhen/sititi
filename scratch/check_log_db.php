<?php
require 'config/database.php';
$db = getDB();
echo "--- status_log table structure ---\n";
$stmt = $db->query("DESCRIBE status_log");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
