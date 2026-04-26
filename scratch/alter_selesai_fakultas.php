<?php
require 'config/database.php';
$db = getDB();

try {
    $db->exec("ALTER TABLE pengajuan_judul MODIFY COLUMN status ENUM('draft','diajukan','diverifikasi','direview','direview_dewan','revisi','ditolak','disetujui','pembimbing_ditentukan','selesai','selesai_fakultas') DEFAULT 'draft'");
    $db->exec("ALTER TABLE status_log MODIFY COLUMN status_baru ENUM('draft','diajukan','diverifikasi','direview','direview_dewan','revisi','ditolak','disetujui','pembimbing_ditentukan','selesai','selesai_fakultas')");
    echo "Success altering tables.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
