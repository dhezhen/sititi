<?php
$host = '127.0.0.1:3307';
$user = 'root';
$pass = '';
$db   = 'sititi_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    $sql = "ALTER TABLE pengajuan_judul 
            ADD COLUMN pembimbing_1_id INT UNSIGNED NULL AFTER file_proposal,
            ADD COLUMN pembimbing_2_id INT UNSIGNED NULL AFTER pembimbing_1_id,
            ADD CONSTRAINT fk_pj_pembimbing_1 FOREIGN KEY (pembimbing_1_id) REFERENCES dosen(id) ON DELETE SET NULL,
            ADD CONSTRAINT fk_pj_pembimbing_2 FOREIGN KEY (pembimbing_2_id) REFERENCES dosen(id) ON DELETE SET NULL";
    
    $pdo->exec($sql);
    echo "Database updated successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
