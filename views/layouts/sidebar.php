<?php
/**
 * Sidebar navigasi dinamis sesuai role
 * File: views/layouts/sidebar.php
 */
$role        = Auth::role();
$currentPage = get('page', 'dashboard');
?>
<aside class="sititi-sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-label">MENU UTAMA</span>
    </div>

    <nav class="sidebar-nav">
        <a href="?page=dashboard"
           class="sidebar-item <?= str_starts_with($currentPage, 'dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <?php
        $db = getDB();
        // Ambil menu yang diizinkan untuk role saat ini, urutkan berdasarkan 'urutan'
        if ($role === ROLE_SUPERADMIN) {
            $stmt = $db->query("SELECT * FROM menus ORDER BY urutan ASC");
            $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $db->prepare("
                SELECT m.* FROM menus m
                JOIN role_menus rm ON m.id = rm.menu_id
                JOIN roles r ON rm.role_id = r.id
                WHERE r.kode = ?
                ORDER BY m.urutan ASC
            ");
            $stmt->execute([$role]);
            $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (!empty($menus)):
        ?>
        <div class="sidebar-header mt-3"><span class="sidebar-label">MENU AKSES</span></div>
        <?php
        // Cek apakah ada menu yang pas (exact match)
        $exactMatch = false;
        foreach ($menus as $m) {
            if ($currentPage === $m['kode_page']) {
                $exactMatch = $m['kode_page'];
                break;
            }
        }

        // Kelompokkan menu berdasarkan prefix (sebelum tanda titik)
        $tempGroups = [];
        foreach ($menus as $m) {
            $group = explode('.', $m['kode_page'])[0];
            $tempGroups[$group][] = $m;
        }

        $groupedMenus = ['single' => []];
        foreach ($tempGroups as $groupKey => $items) {
            if (count($items) > 1) {
                $groupedMenus[$groupKey] = $items;
            } else {
                $groupedMenus['single'][] = $items[0];
            }
        }

        // Label khusus untuk grup menu
        $groupLabels = [
            'admin' => ['judul' => 'Data Master', 'icon' => 'bi-database'],
            'superadmin' => ['judul' => 'Sistem & Keamanan', 'icon' => 'bi-shield-lock'],
            'staff' => ['judul' => 'Panel Staff', 'icon' => 'bi-building'],
            'kaprodi' => ['judul' => 'Panel Kaprodi', 'icon' => 'bi-award'],
            'dewan' => ['judul' => 'Panel Dewan', 'icon' => 'bi-people'],
            'pengajuan' => ['judul' => 'Mahasiswa', 'icon' => 'bi-journal-bookmark']
        ];

        // Render single menus (tidak punya prefix/grup)
        if (!empty($groupedMenus['single'])) {
            foreach ($groupedMenus['single'] as $m) {
                if ($exactMatch) {
                    $isActive = ($currentPage === $m['kode_page']);
                } else {
                    $isActive = ($currentPage === $m['kode_page']) || (str_starts_with($currentPage, $m['kode_page'].'.'));
                }
                ?>
                <a href="?page=<?= $m['kode_page'] ?>" class="sidebar-item <?= $isActive ? 'active' : '' ?>">
                    <i class="<?= e($m['icon']) ?>"></i>
                    <span><?= e($m['judul']) ?></span>
                </a>
                <?php
            }
        }

        // Render menu dengan grup (dropdown)
        $groupIdx = 0;
        foreach ($groupedMenus as $groupKey => $groupItems) {
            if ($groupKey === 'single') continue;
            
            $groupIdx++;
            $groupTitle = $groupLabels[$groupKey]['judul'] ?? ucfirst($groupKey);
            $groupIcon = $groupLabels[$groupKey]['icon'] ?? 'bi-folder';
            
            // Cek apakah ada menu aktif di dalam grup ini
            $isGroupActive = false;
            foreach ($groupItems as $m) {
                if ($exactMatch) {
                    if ($currentPage === $m['kode_page']) $isGroupActive = true;
                } else {
                    if (($currentPage === $m['kode_page']) || (str_starts_with($currentPage, $m['kode_page'].'.'))) $isGroupActive = true;
                }
            }
            ?>
            <div class="sidebar-dropdown">
                <a href="#submenu-<?= $groupIdx ?>" data-bs-toggle="collapse" class="sidebar-item sidebar-dropdown-toggle <?= $isGroupActive ? '' : 'collapsed' ?>" aria-expanded="<?= $isGroupActive ? 'true' : 'false' ?>">
                    <i class="<?= $groupIcon ?>"></i>
                    <span><?= $groupTitle ?></span>
                    <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
                </a>
                <div class="collapse <?= $isGroupActive ? 'show' : '' ?>" id="submenu-<?= $groupIdx ?>">
                    <div class="sidebar-submenu">
                        <?php foreach ($groupItems as $m): 
                            if ($exactMatch) {
                                $isActive = ($currentPage === $m['kode_page']);
                            } else {
                                $isActive = ($currentPage === $m['kode_page']) || (str_starts_with($currentPage, $m['kode_page'].'.'));
                            }
                        ?>
                        <a href="?page=<?= $m['kode_page'] ?>" class="submenu-item <?= $isActive ? 'active' : '' ?>">
                            <i class="<?= e($m['icon']) ?>"></i>
                            <span><?= e($m['judul']) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <?php endif; ?>

        <?php if ($role === ROLE_DOSEN): ?>
        <a href="?page=dosen.bimbingan" class="sidebar-item <?= str_starts_with($currentPage, 'dosen.bimbingan') ? 'active' : '' ?>">
            <i class="bi bi-journal-check"></i>
            <span>Bimbingan Skripsi</span>
        </a>
        <?php endif; ?>

        <div class="sidebar-header mt-3"><span class="sidebar-label">AKUN</span></div>
        <a href="?page=profil"
           class="sidebar-item <?= $currentPage === 'profil' ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i>
            <span>Profil Saya</span>
        </a>
        <div class="sidebar-header mt-3"><span class="sidebar-label">LAINNYA</span></div>
        <?php $unread = countUnreadNotif(); ?>
        <a href="?page=notif"
           class="sidebar-item <?= $currentPage === 'notif' ? 'active' : '' ?>">
            <i class="bi bi-bell"></i>
            <span>Notifikasi</span>
            <?php if ($unread > 0): ?>
            <span class="ms-auto badge bg-danger rounded-pill"><?= $unread ?></span>
            <?php endif; ?>
        </a>

        <a href="?page=logout"
           class="sidebar-item text-danger-hover"
           data-confirm="Apakah Anda yakin ingin keluar dari sistem SITITI?">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>
