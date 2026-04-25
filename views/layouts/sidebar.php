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

        <?php if ($role === ROLE_MAHASISWA): ?>
        <!-- ===== MENU MAHASISWA ===== -->
        <div class="sidebar-header mt-3"><span class="sidebar-label">PENGAJUAN</span></div>

        <a href="?page=pengajuan"
           class="sidebar-item <?= $currentPage === 'pengajuan' ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i>
            <span>Daftar Pengajuan</span>
        </a>
        <a href="?page=pengajuan.create"
           class="sidebar-item <?= $currentPage === 'pengajuan.create' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle"></i>
            <span>Buat Pengajuan</span>
        </a>

        <div class="sidebar-header mt-3"><span class="sidebar-label">AKUN</span></div>
        <a href="?page=profil"
           class="sidebar-item <?= $currentPage === 'profil' ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i>
            <span>Profil Saya</span>
        </a>

        <?php elseif ($role === ROLE_STAFF): ?>
        <!-- ===== MENU STAFF ===== -->
        <div class="sidebar-header mt-3"><span class="sidebar-label">VERIFIKASI</span></div>

        <a href="?page=staff"
           class="sidebar-item <?= str_starts_with($currentPage, 'staff') ? 'active' : '' ?>">
            <i class="bi bi-clipboard-check"></i>
            <span>Verifikasi Berkas</span>
        </a>
        <a href="?page=staff&status=diverifikasi"
           class="sidebar-item">
            <i class="bi bi-check-circle"></i>
            <span>Sudah Diverifikasi</span>
        </a>

        <div class="sidebar-header mt-3"><span class="sidebar-label">DATA MASTER</span></div>
        <a href="?page=admin.mahasiswa"
           class="sidebar-item <?= $currentPage === 'admin.mahasiswa' ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span>Data Mahasiswa</span>
        </a>
        <a href="?page=admin.dosen"
           class="sidebar-item <?= $currentPage === 'admin.dosen' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i>
            <span>Data Dosen</span>
        </a>

        <?php elseif ($role === ROLE_KAPRODI): ?>
        <!-- ===== MENU KAPRODI ===== -->
        <div class="sidebar-header mt-3"><span class="sidebar-label">REVIEW</span></div>

        <a href="?page=kaprodi"
           class="sidebar-item <?= str_starts_with($currentPage, 'kaprodi') ? 'active' : '' ?>">
            <i class="bi bi-eye"></i>
            <span>Review Pengajuan</span>
        </a>
        <a href="?page=kaprodi&status=disetujui"
           class="sidebar-item">
            <i class="bi bi-patch-check"></i>
            <span>Telah Disetujui</span>
        </a>
        <a href="?page=kaprodi&status=ditolak"
           class="sidebar-item">
            <i class="bi bi-x-circle"></i>
            <span>Ditolak</span>
        </a>

        <div class="sidebar-header mt-3"><span class="sidebar-label">DATA MASTER</span></div>
        <a href="?page=admin.dosen"
           class="sidebar-item <?= $currentPage === 'admin.dosen' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i>
            <span>Data Dosen</span>
        </a>
        <a href="?page=admin.periode"
           class="sidebar-item <?= $currentPage === 'admin.periode' ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i>
            <span>Periode</span>
        </a>

        <?php elseif ($role === ROLE_DEWAN): ?>
        <!-- ===== MENU DEWAN ===== -->
        <div class="sidebar-header mt-3"><span class="sidebar-label">BIMBINGAN</span></div>

        <a href="?page=dewan"
           class="sidebar-item <?= str_starts_with($currentPage, 'dewan') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i>
            <span>Tentukan Pembimbing</span>
        </a>
        <a href="?page=dewan&status=pembimbing_ditentukan"
           class="sidebar-item">
            <i class="bi bi-check2-all"></i>
            <span>Sudah Ada Pembimbing</span>
        </a>

        <div class="sidebar-header mt-3"><span class="sidebar-label">DATA MASTER</span></div>
        <a href="?page=admin.dosen"
           class="sidebar-item <?= $currentPage === 'admin.dosen' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i>
            <span>Data Dosen</span>
        </a>
        <?php endif; ?>

        <!-- Notifikasi selalu tampil -->
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
