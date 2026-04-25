<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$users = $db->query("SELECT id, nama, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
echo "Users table:\n";
print_r($users);

$roles = $db->query("SELECT * FROM user_roles")->fetchAll(PDO::FETCH_ASSOC);
echo "\nUser_roles table:\n";
print_r($roles);
