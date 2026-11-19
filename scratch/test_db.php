<?php
$ports = [3306, 3307];
foreach ($ports as $port) {
    try {
        $dsn = "mysql:host=127.0.0.1;port=$port;dbname=sititi_db;charset=utf8mb4";
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        echo "Successfully connected to port $port!\n";
        $stmt = $pdo->query('SELECT id, nama, email, role, is_active FROM users ORDER BY id ASC');
        foreach ($stmt->fetchAll() as $u) {
            echo "[ID {$u['id']}] {$u['nama']} ({$u['email']}) - Role: {$u['role']}\n";
        }
        break;
    } catch (PDOException $e) {
        echo "Port $port failed: " . $e->getMessage() . "\n";
    }
}
