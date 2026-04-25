<?php
require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';
$db = getDB();

$db->exec("DELETE FROM role_menus");

$rolesMap = [];
$res = $db->query("SELECT id, kode FROM roles")->fetchAll(PDO::FETCH_ASSOC);
foreach ($res as $r) { $rolesMap[$r['kode']] = $r['id']; }

$menusMap = [];
$res = $db->query("SELECT id, kode_page FROM menus")->fetchAll(PDO::FETCH_ASSOC);
foreach ($res as $m) { $menusMap[$m['kode_page']] = $m['id']; }

$roleMenus = [
    'mahasiswa' => ['pengajuan', 'pengajuan.create'],
    'staff' => ['staff', 'admin.mahasiswa', 'admin.dosen'],
    'kaprodi' => ['kaprodi', 'admin.dosen', 'admin.periode'],
    'dewan' => ['dewan', 'admin.dosen'],
    'superadmin' => ['superadmin.roles', 'superadmin.menus']
];

$stmtRm = $db->prepare("INSERT INTO role_menus (role_id, menu_id) VALUES (?, ?)");
$inserted = 0;
foreach ($roleMenus as $roleCode => $menuCodes) {
    if (!isset($rolesMap[$roleCode])) continue;
    $roleId = $rolesMap[$roleCode];
    foreach ($menuCodes as $mc) {
        if (!isset($menusMap[$mc])) {
            echo "Menu $mc not found!\n";
            continue;
        }
        $stmtRm->execute([$roleId, $menusMap[$mc]]);
        $inserted++;
    }
}
echo "Inserted $inserted role menus.\n";
