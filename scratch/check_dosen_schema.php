<?php
require 'config/database.php';
$db = getDB();
print_r($db->query("DESCRIBE dosen")->fetchAll());
