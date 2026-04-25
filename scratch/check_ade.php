<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$res = $db->query("SELECT u.id, u.nama, u.role, m.id as mhs_id 
                    FROM users u 
                    LEFT JOIN mahasiswa m ON m.user_id = u.id 
                    WHERE u.nama LIKE '%Ade%'")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
