<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$res = $db->query("SELECT * FROM user_roles WHERE user_id = 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
