<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$res = $db->query("SELECT * FROM dosen WHERE email LIKE '%kaprodi%'")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
