<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$res = $db->query("SELECT status, COUNT(*) as total FROM pengajuan_judul GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
foreach($res as $r) {
    echo "Status: " . $r['status'] . " -> Total: " . $r['total'] . "\n";
}

$res2 = $db->query("SELECT pj.id, pj.judul, pj.status, u.nama as mahasiswa 
                    FROM pengajuan_judul pj 
                    JOIN users u ON u.id = pj.user_id")->fetchAll(PDO::FETCH_ASSOC);
echo "\nDetail Pengajuan:\n";
foreach($res2 as $r) {
    echo "[#{$r['id']}] {$r['mahasiswa']} : {$r['judul']} ({$r['status']})\n";
}
