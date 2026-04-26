<?php
require 'config/database.php';
$db = getDB();

$stmt = $db->prepare("UPDATE pengajuan_judul SET status = 'selesai' WHERE id = 1");
try {
    $stmt->execute();
    echo "Success!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
