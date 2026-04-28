<?php
/**
 * Migration: Add 'sk_terbit' to status_log status_baru ENUM
 */
require 'config/database.php';

try {
    $db = getDB();
    
    // MySQL ALTER TABLE for status_log
    $sqlLog = "ALTER TABLE status_log MODIFY COLUMN status_baru ENUM(
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
    ) DEFAULT NULL";
    
    $db->exec($sqlLog);
    echo "Migration successful: 'sk_terbit' added to status_log.status_baru enum.\n";
    
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
