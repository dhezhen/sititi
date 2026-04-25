<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$res = $db->query("SELECT pj.*, u.nama, m.user_id as m_user_id 
                    FROM pengajuan_judul pj 
                    LEFT JOIN users u ON u.id = pj.user_id 
                    LEFT JOIN mahasiswa m ON m.user_id = pj.user_id 
                    WHERE u.nama LIKE '%Ade%'")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
