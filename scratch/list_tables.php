<?php
require 'config/database.php';
$db = getDB();
echo "List of tables:\n";
print_r($db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN));
