<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$sql = 'SELECT d.id, d.nama, m.angkatan, COUNT(p.id) as total FROM dosen d LEFT JOIN pembimbing p ON d.id = p.dosen_id LEFT JOIN pengajuan_judul pj ON p.pengajuan_id = pj.id LEFT JOIN mahasiswa m ON pj.user_id = m.user_id WHERE d.is_active = 1 GROUP BY d.id, m.angkatan ORDER BY d.nama ASC, m.angkatan DESC';
print_r($db->query($sql)->fetchAll(PDO::FETCH_ASSOC));
