<?php
require 'config/app.php';
require 'config/database.php';
$db = getDB();
$res = $db->query("SELECT m.id, m.judul, m.kode_page FROM menus m JOIN role_menus rm ON m.id = rm.menu_id JOIN roles r ON rm.role_id = r.id WHERE r.kode = 'dewan'")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
