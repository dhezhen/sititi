<?php
require 'config/database.php';
$db = getDB();
echo "--- Role distribution ---\n";
$stmt = $db->query("SELECT role, COUNT(*) as total FROM users GROUP BY role");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n--- Users with 'dewan' role ---\n";
$stmt = $db->query("SELECT id, nama, role, is_active FROM users WHERE role = 'dewan'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
