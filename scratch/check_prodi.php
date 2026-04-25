<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$ade = $db->query("SELECT prodi_id FROM mahasiswa WHERE user_id = 30")->fetchColumn();
echo "Prodi Ade: $ade\n";

$kaprodiEmail = 'kaprodi@demo.com'; // Wait, let's check who the user is logged in as.
// The user said "saya login sebagai kaprodi"
$kaprodi = $db->query("SELECT prodi_id FROM dosen WHERE email LIKE '%kaprodi%'")->fetchColumn();
echo "Prodi Kaprodi: $kaprodi\n";
