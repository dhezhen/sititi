<?php
/**
 * Script to setup database
 */

$host = 'localhost;port=3307';
$user = 'root';
$pass = '';
$sqlFile = 'database/sititi.sql';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Importing SQL file...\n";
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    // This is a simple split, might not handle complex triggers but should work for this file
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }

    echo "Database setup successful!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
