<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    $db = getDB();
    $sql = "CREATE TABLE IF NOT EXISTS catatan_bimbingan (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        pengajuan_id INT UNSIGNED NOT NULL,
        dosen_id INT UNSIGNED NOT NULL,
        catatan TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pengajuan_id) REFERENCES pengajuan_judul(id) ON DELETE CASCADE,
        FOREIGN KEY (dosen_id) REFERENCES dosen(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sql);
    echo "Successfully created 'catatan_bimbingan' table.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
