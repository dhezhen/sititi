<?php
require 'config/database.php';
$db = getDB();
echo "Submissions with user_id 0 or NULL:\n";
print_r($db->query("SELECT id, user_id, judul FROM pengajuan_judul WHERE user_id IS NULL OR user_id = 0")->fetchAll());
echo "\nTotal submissions:\n";
print_r($db->query("SELECT COUNT(*) FROM pengajuan_judul")->fetchColumn());
