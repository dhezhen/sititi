<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
try {
    $db->prepare("INSERT INTO roles (kode, nama) VALUES (?, ?)")->execute(['dosen', 'Dosen Pembimbing']);
    echo "Role dosen added successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
