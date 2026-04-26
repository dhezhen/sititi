<?php
require_once __DIR__ . '/../config/database.php';
$db = getDB();
$users = $db->query("SELECT id, nama, email, role FROM users")->fetchAll();
foreach($users as $u) {
    echo "ID: {$u['id']} | Nama: {$u['nama']} | Email: {$u['email']} | Role: {$u['role']}\n";
}
