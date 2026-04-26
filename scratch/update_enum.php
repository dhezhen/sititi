<?php
require 'config/database.php';
$db = getDB();

// Get current enum for pengajuan_judul
$stmt = $db->query("SHOW COLUMNS FROM pengajuan_judul LIKE 'status'");
$col = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($col);

// Update pengajuan_judul
$db->exec("ALTER TABLE pengajuan_judul MODIFY COLUMN status ENUM('draft','diajukan','diverifikasi','direview','direview_dewan','revisi','ditolak','disetujui','pembimbing_ditentukan','selesai') DEFAULT 'draft'");

// Update status_log as well
$db->exec("ALTER TABLE status_log MODIFY COLUMN status_baru ENUM('draft','diajukan','diverifikasi','direview','direview_dewan','revisi','ditolak','disetujui','pembimbing_ditentukan','selesai')");
$db->exec("ALTER TABLE status_log MODIFY COLUMN status_lama ENUM('draft','diajukan','diverifikasi','direview','direview_dewan','revisi','ditolak','disetujui','pembimbing_ditentukan','selesai') NULL");

echo "Database schema updated successfully.\n";
