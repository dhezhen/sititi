<?php
require 'config/database.php';
$db = getDB();
echo "Table: pengajuan_judul\n";
print_r($db->query("DESCRIBE pengajuan_judul")->fetchAll(PDO::FETCH_ASSOC));
