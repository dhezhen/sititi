<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();
$db->exec("ALTER TABLE dosen ADD COLUMN `jabatan_akademik` VARCHAR(100) NULL AFTER `bidang_keahlian`");
$db->exec("ALTER TABLE dosen ADD COLUMN `pendidikan` VARCHAR(100) NULL AFTER `jabatan_akademik`");
echo "Migration Success!\n";
