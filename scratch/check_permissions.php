<?php
require 'config/database.php';
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    echo "--- ROLES ---\n";
    $stmt = $db->query("SELECT * FROM roles");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    echo "\n--- MENUS ---\n";
    $stmt = $db->query("SELECT * FROM menus");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    echo "\n--- ROLE MENUS ---\n";
    $stmt = $db->query("SELECT rm.*, r.kode as role_kode, m.kode_page FROM role_menus rm JOIN roles r ON rm.role_id = r.id JOIN menus m ON rm.menu_id = m.id");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
} catch (Exception $e) {
    echo $e->getMessage();
}
