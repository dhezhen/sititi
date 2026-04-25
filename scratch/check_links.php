<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
echo "Roles:\n";
print_r($db->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC));
echo "\nMenus:\n";
print_r($db->query("SELECT * FROM menus")->fetchAll(PDO::FETCH_ASSOC));
echo "\nRole Menus for Kaprodi (Role 3):\n";
print_r($db->query("SELECT * FROM role_menus WHERE role_id = 3")->fetchAll(PDO::FETCH_ASSOC));
