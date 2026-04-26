<?php
require_once __DIR__ . '/../config/database.php';

$db = getDB();
$stmt = $db->query("SELECT id, user_id, judul, status FROM pengajuan_judul ORDER BY id DESC LIMIT 5");
$data = $stmt->fetchAll();

echo "5 Pengajuan Terakhir:\n";
foreach ($data as $row) {
    echo "ID: {$row['id']} | User: {$row['user_id']} | Status: {$row['status']} | Judul: {$row['judul']}\n";
}
