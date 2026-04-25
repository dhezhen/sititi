<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();

// Update Dewan menu title
$db->query("UPDATE menus SET judul = 'Review & Penentuan' WHERE kode_page = 'dewan'");

echo "Dewan menu title updated.\n";
