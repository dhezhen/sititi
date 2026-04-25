<?php
require 'config/app.php';
require 'config/database.php';

$db = getDB();

try {
    // 1. Buat tabel user_roles
    $db->exec("CREATE TABLE IF NOT EXISTS user_roles (
        user_id INT UNSIGNED NOT NULL,
        role_kode VARCHAR(50) NOT NULL,
        PRIMARY KEY (user_id, role_kode),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    echo "Tabel user_roles berhasil dibuat.\n";

    // 2. Migrasi data role lama
    $users = $db->query("SELECT id, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
    $count = 0;
    foreach ($users as $u) {
        $stmt = $db->prepare("INSERT IGNORE INTO user_roles (user_id, role_kode) VALUES (?, ?)");
        $stmt->execute([$u['id'], $u['role']]);
        $count++;
    }

    echo "$count data role berhasil dimigrasi.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
