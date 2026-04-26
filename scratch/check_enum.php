<?php
require 'config/database.php';
$db = getDB();

$stmt = $db->query("SHOW COLUMNS FROM pengajuan_judul LIKE 'status'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));

$stmt = $db->query("SHOW COLUMNS FROM status_log LIKE 'status_baru'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));

$stmt = $db->query("SHOW COLUMNS FROM status_log LIKE 'status_lama'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
