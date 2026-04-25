<?php
$host = '127.0.0.1:3307';
$user = 'root';
$pass = '';
$db   = 'sititi_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    echo "--- TABLE: dosen ---\n";
    $stmt = $pdo->query("DESCRIBE dosen");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Default']}\n";
    }

    echo "\n--- TABLE: pengajuan_judul ---\n";
    $stmt = $pdo->query("DESCRIBE pengajuan_judul");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Default']}\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
