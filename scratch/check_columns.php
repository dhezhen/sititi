<?php
require 'config/database.php';
$db = getDB();
echo "pengajuan_judul id:\n";
print_r($db->query("DESCRIBE pengajuan_judul id")->fetch());
echo "\ndosen id:\n";
print_r($db->query("DESCRIBE dosen id")->fetch());
