<?php
require 'config/database.php';
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    $stmt = $db->query("SELECT r.kode as role, m.kode_page as menu 
                        FROM role_menus rm 
                        JOIN roles r ON rm.role_id = r.id 
                        JOIN menus m ON rm.menu_id = m.id 
                        WHERE r.kode = 'kaprodi'");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
} catch (Exception $e) {
    echo $e->getMessage();
}
