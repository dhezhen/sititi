<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    $stmt = $db->query('SELECT id, nama, email, role, is_active FROM users ORDER BY id ASC');
    $users = $stmt->fetchAll();
    foreach ($users as $u) {
        echo "[ID {$u['id']}] {$u['nama']} ({$u['email']}) - Role: {$u['role']} - Active: {$u['is_active']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
