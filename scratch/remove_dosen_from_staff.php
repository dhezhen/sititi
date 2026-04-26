<?php
/**
 * Remove 'admin.dosen' menu from staff role
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Remove admin.dosen from staff role
$stmt = $db->prepare(
    "DELETE rm FROM role_menus rm 
     JOIN roles r ON rm.role_id = r.id 
     JOIN menus m ON rm.menu_id = m.id 
     WHERE r.kode = 'staff' AND m.kode_page = 'admin.dosen'"
);
$stmt->execute();
echo "Removed admin.dosen from staff: " . $stmt->rowCount() . " row(s)\n";

// Also remove admin.dosen.store, admin.dosen.update, admin.dosen.delete, admin.dosen.import
$dosenPages = ['admin.dosen.store', 'admin.dosen.update', 'admin.dosen.delete', 'admin.dosen.import'];
foreach ($dosenPages as $p) {
    $stmt = $db->prepare(
        "DELETE rm FROM role_menus rm 
         JOIN roles r ON rm.role_id = r.id 
         JOIN menus m ON rm.menu_id = m.id 
         WHERE r.kode = 'staff' AND m.kode_page = ?"
    );
    $stmt->execute([$p]);
    if ($stmt->rowCount() > 0) {
        echo "Removed $p from staff\n";
    }
}

// Show remaining staff menus
echo "\nStaff menus remaining:\n";
$stmt2 = $db->prepare(
    "SELECT m.kode_page, m.judul FROM role_menus rm 
     JOIN roles r ON rm.role_id = r.id 
     JOIN menus m ON rm.menu_id = m.id 
     WHERE r.kode = 'staff' ORDER BY m.urutan"
);
$stmt2->execute();
foreach ($stmt2->fetchAll() as $r) {
    echo "  - {$r['kode_page']} ({$r['judul']})\n";
}

echo "\nDone!\n";
