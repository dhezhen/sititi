<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();

// Matikan foreign key checks sementara
$db->exec("SET FOREIGN_KEY_CHECKS = 0");

// Kosongkan tabel prodi
$db->exec("TRUNCATE TABLE prodi");

// Isi dengan prodi yang diinginkan
$prodis = [
    ['nama' => 'Teknik Informatika', 'kode' => 'IF'],
    ['nama' => 'Teknik Sipil', 'kode' => 'TS'],
    ['nama' => 'Desain Komunikasi Visual', 'kode' => 'DKV'],
    ['nama' => 'Sistem Informasi', 'kode' => 'SI'],
];

foreach ($prodis as $p) {
    $stmt = $db->prepare("INSERT INTO prodi (nama, kode) VALUES (?, ?)");
    $stmt->execute([$p['nama'], $p['kode']]);
}

// Hidupkan kembali foreign key checks
$db->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "Prodi table updated successfully.\n";
