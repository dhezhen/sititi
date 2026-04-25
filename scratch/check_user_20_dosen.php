<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$res = $db->query("SELECT * FROM dosen WHERE email = (SELECT email FROM users WHERE id = 20)")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
