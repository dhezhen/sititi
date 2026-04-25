<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
echo "USERS:\n";
print_r($db->query("SELECT id, nama, email, role FROM users")->fetchAll(PDO::FETCH_ASSOC));
echo "\nDOSEN:\n";
print_r($db->query("SELECT id, nama, email, prodi_id FROM dosen")->fetchAll(PDO::FETCH_ASSOC));
echo "\nMAHASISWA:\n";
print_r($db->query("SELECT id, user_id, angkatan, prodi_id FROM mahasiswa")->fetchAll(PDO::FETCH_ASSOC));
