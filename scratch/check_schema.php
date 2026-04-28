<?php
require 'config/database.php';
$db = getDB();
echo "Table: mahasiswa\n";
print_r($db->query("DESCRIBE mahasiswa")->fetchAll(PDO::FETCH_ASSOC));
echo "\nTable: users\n";
print_r($db->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC));
