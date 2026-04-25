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
        <!-- Dashboard -->
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
        <?php foreach ($menus as $m): 
            // Cek active state
            // Jika $currentPage sama persis dengan $m['kode_page'], active
            // Atau jika startswith (untuk sub-page seperti pengajuan.create vs pengajuan)
            $isActive = ($currentPage === $m['kode_page']) || (str_starts_with($currentPage, $m['kode_page'].'.'));
        ?>
        <a href="?page=<?= $m['kode_page'] ?>"
           class="sidebar-item <?= $isActive ? 'active' : '' ?>">
            <i class="<?= e($m['icon']) ?>"></i>
            <span><?= e($m['judul']) ?></span>
        </a>
        <?php endforeach; ?>
        <?php endif; ?>

        <!-- Profil Saya (selalu ada, bukan dinamis) -->
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
           onclick="return confirm('Yakin logout?')">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>
