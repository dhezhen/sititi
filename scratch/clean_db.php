<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';

$db = getDB();

try {
    $db->beginTransaction();
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");

    // List of tables to truncate
    $tables = [
        'pembimbing',
        'status_log',
        'revisi',
        'notifikasi',
        'pengajuan_judul',
        'mahasiswa',
        'dosen'
    ];

    foreach ($tables as $table) {
        $db->exec("DELETE FROM `$table` ");
        $db->exec("ALTER TABLE `$table` AUTO_INCREMENT = 1");
    }

    // Clean users except superadmin
    $db->exec("DELETE FROM users WHERE role != 'superadmin'");
    
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    $db->commit();

    echo "Database cleaned successfully. All data removed except Super Admin accounts.\n";
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
