<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();

// 1. Sinkronkan Kaprodi Demo dengan tabel Dosen
$stmt = $db->prepare("UPDATE dosen SET email = 'kaprodi@demo.com' WHERE id = 1");
$stmt->execute();

// 2. Tambahkan Data Mahasiswa Contoh
$students = [
    ['nama' => 'M. Rizky', 'email' => 'rizky@uniku.ac.id', 'nim' => '2021001', 'angkatan' => 2021, 'prodi_id' => 1],
    ['nama' => 'Siti Aminah', 'email' => 'siti@uniku.ac.id', 'nim' => '2021002', 'angkatan' => 2021, 'prodi_id' => 1],
    ['nama' => 'Budi Utomo', 'email' => 'budi@uniku.ac.id', 'nim' => '2022001', 'angkatan' => 2022, 'prodi_id' => 1],
    ['nama' => 'Lani Wijaya', 'email' => 'lani@uniku.ac.id', 'nim' => '2020001', 'angkatan' => 2020, 'prodi_id' => 1],
];

foreach ($students as $s) {
    // Buat User
    $stmt = $db->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$s['nama'], $s['email'], password_hash('password', PASSWORD_DEFAULT), 'mahasiswa']);
    $userId = $db->lastInsertId();
    
    // Buat Mahasiswa
    $stmt = $db->prepare("INSERT INTO mahasiswa (user_id, nim, angkatan, prodi_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $s['nim'], $s['angkatan'], $s['prodi_id']]);
    
    // Buat Pengajuan untuk beberapa mahasiswa
    if ($s['angkatan'] == 2020 || $s['nama'] == 'M. Rizky') {
        $stmt = $db->prepare("INSERT INTO pengajuan_judul (user_id, judul, status) VALUES (?, ?, ?)");
        $stmt->execute([$userId, 'Judul Skripsi ' . $s['nama'], 'diajukan']);
    }
}

echo "Seeding completed.\n";
