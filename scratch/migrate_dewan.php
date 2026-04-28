<?php
require 'config/database.php';
$db = getDB();
try {
    $db->exec("ALTER TABLE pengajuan_judul ADD COLUMN dewan_1_id INT UNSIGNED NULL AFTER pembimbing_2_id");
    $db->exec("ALTER TABLE pengajuan_judul ADD COLUMN dewan_2_id INT UNSIGNED NULL AFTER dewan_1_id");
    $db->exec("ALTER TABLE pengajuan_judul ADD CONSTRAINT fk_dewan_1 FOREIGN KEY (dewan_1_id) REFERENCES users(id)");
    $db->exec("ALTER TABLE pengajuan_judul ADD CONSTRAINT fk_dewan_2 FOREIGN KEY (dewan_2_id) REFERENCES users(id)");
    echo "Migration successful: Added dewan_1_id and dewan_2_id to pengajuan_judul.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
