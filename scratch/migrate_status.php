<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    // Tambah 'direview_dewan' ke ENUM status
    $sql = "ALTER TABLE pengajuan_judul MODIFY COLUMN status ENUM('draft','diajukan','diverifikasi','direview','direview_dewan','revisi','ditolak','disetujui','pembimbing_ditentukan') NOT NULL DEFAULT 'draft'";
    $db->exec($sql);
    echo "SUCCESS: Status column updated to include 'direview_dewan'.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
