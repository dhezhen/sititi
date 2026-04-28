<?php
/**
 * Migration: Add 'sk_terbit' to pengajuan_judul status ENUM
 */
require 'config/database.php';

try {
    $db = getDB();
    
    // MySQL ALTER TABLE to update ENUM
    $sql = "ALTER TABLE pengajuan_judul MODIFY COLUMN status ENUM(
        'draft',
        'diajukan',
        'diverifikasi',
        'direview',
        'direview_dewan',
        'revisi',
        'ditolak',
        'disetujui',
        'pembimbing_ditentukan',
        'selesai',
        'selesai_fakultas',
        'sk_terbit'
    ) DEFAULT 'draft'";
    
    $db->exec($sql);
    echo "Migration successful: 'sk_terbit' added to status enum.\n";
    
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
