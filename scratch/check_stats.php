<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();

// Query statistik per angkatan
$stmt = $db->query("
    SELECT 
        m.angkatan,
        COUNT(DISTINCT m.id) AS total_mahasiswa,
        COUNT(DISTINCT CASE WHEN p.id IS NOT NULL THEN m.id END) AS sudah_mengajukan,
        COUNT(DISTINCT CASE WHEN p.status NOT IN ('ditolak') AND p.id IS NOT NULL THEN m.id END) AS pengajuan_aktif
    FROM mahasiswa m
    LEFT JOIN pengajuan_judul p ON p.mahasiswa_id = m.id
    GROUP BY m.angkatan
    ORDER BY m.angkatan DESC
");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
