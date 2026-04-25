<?php
$host = '127.0.0.1:3307';
$user = 'root';
$pass = '';
$db   = 'sititi_db';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db` ");
    
    // Simple way to check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    if ($stmt->rowCount() > 0) {
        echo "Database already has tables. Skipping import.\n";
        exit;
    }

    $sql = file_get_contents('database/sititi.sql');
    if ($sql === false) {
        die("Could not read database/sititi.sql\n");
    }

    // Split SQL by semicolon, but be careful with delimiters if any (not likely in simple dumps)
    // PDO::exec can only execute one statement at a time or we can use the multi-query if we use mysqli
    // For simple dumps, we can just use exec on the whole thing if the driver supports it
    $pdo->exec($sql);
    echo "Database imported successfully.\n";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
