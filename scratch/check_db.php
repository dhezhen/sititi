<?php
require 'config/database.php';
$db = getDB();
$stmt = $db->query("DESCRIBE pengajuan_judul status");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($row);
