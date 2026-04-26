<?php
require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Get the student's prodi_id
$stmt = $db->prepare("SELECT m.prodi_id FROM mahasiswa m JOIN pengajuan_judul pj ON m.user_id = pj.user_id WHERE pj.id = 1");
$stmt->execute();
$studentProdiId = $stmt->fetchColumn();
echo "Student Prodi ID for Proposal 1: " . ($studentProdiId ?: 'NULL') . "\n";

// Get Kaprodi users
$stmt = $db->query("SELECT u.email, d.prodi_id FROM users u LEFT JOIN dosen d ON u.email = d.email WHERE u.role = 'kaprodi'");
$kaprodis = $stmt->fetchAll();
echo "Kaprodi Users:\n";
foreach ($kaprodis as $k) {
    echo "Email: {$k['email']} | Prodi ID: " . ($k['prodi_id'] ?: 'NULL') . "\n";
}
