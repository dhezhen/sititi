<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= APP_FULL_NAME ?> - Sistem manajemen pengajuan judul skripsi mahasiswa">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sititi-navbar sticky-top">
    <div class="container-fluid px-4">
        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle Navigation">
            <i class="bi bi-list fs-4"></i>
        </button>

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="?page=dashboard">
            <img src="assets/img/logo_fkom.png" alt="Logo FKOM" style="height: 35px; width: auto;">
            <span class="ms-1"><?= APP_NAME ?></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <i class="bi bi-three-dots-vertical"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1">

                <?php
                $unreadCount = countUnreadNotif();
                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative p-2" href="#" id="notifDropdown"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($unreadCount > 0): ?>
                        <span class="badge-notif"><?= $unreadCount > 9 ? '9+' : $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end notif-dropdown shadow" aria-labelledby="notifDropdown">
                        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                            <strong>Notifikasi</strong>
                            <?php if ($unreadCount > 0): ?>
                            <a href="?page=notif.readall" class="text-primary small">Tandai Semua Dibaca</a>
                            <?php endif; ?>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <?php
                        $notifList = (new Notifikasi())->getByUserId(Auth::id(), 6);
                        if (empty($notifList)): ?>
                        <li class="px-3 py-3 text-muted small text-center">
                            <i class="bi bi-bell-slash mb-1 d-block fs-4"></i>
                            Tidak ada notifikasi
                        </li>
                        <?php else: ?>
                        <?php foreach ($notifList as $n): ?>
                        <li>
                            <a class="dropdown-item notif-item <?= !$n['is_read'] ? 'unread' : '' ?>"
                               href="?page=notif.read&id=<?= $n['id'] ?><?= $n['pengajuan_id'] ? '&ref=' . $n['pengajuan_id'] : '' ?>">
                                <div class="notif-dot <?= !$n['is_read'] ? 'active' : '' ?>"></div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 small"><?= e(truncate($n['pesan'], 70)) ?></p>
                                    <span class="text-muted" style="font-size:.75rem"><?= formatTanggal($n['created_at'], true) ?></span>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li class="text-center py-2">
                            <a href="?page=notif" class="small text-primary">Lihat Semua Notifikasi</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2 user-menu" href="#"
                       id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar" style="overflow:hidden;">
                            <?php if (!empty(Auth::user()['foto'])): ?>
                                <img src="<?= AVATAR_URL ?>/<?= e(Auth::user()['foto']) ?>" alt="Profil" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <?= strtoupper(substr(Auth::user()['nama'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="d-none d-lg-block text-start">
                            <div class="fw-semibold lh-1" style="font-size:.9rem">
                                <?= e(truncate(Auth::user()['nama'], 20)) ?>
                            </div>
                            <div class="text-white-50" style="font-size:.75rem">
                                <?= ucfirst(Auth::role()) ?>
                            </div>
                        </div>
                        <i class="bi bi-chevron-down small"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <?php if (count(Auth::roles()) > 1): ?>
                        <li class="dropdown-header text-uppercase small fw-bold text-primary">Ganti Peran Aktif</li>
                        <?php foreach (Auth::roles() as $r): ?>
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center <?= Auth::role() === $r ? 'active' : '' ?>" 
                               href="?page=switch-role&role=<?= $r ?>">
                                <?= ucfirst($r) ?>
                                <?php if (Auth::role() === $r): ?><i class="bi bi-check2 ms-2"></i><?php endif; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>

                        <li class="px-3 py-2">
                            <div class="fw-semibold"><?= e(Auth::user()['nama']) ?></div>
                            <div class="text-muted small"><?= e(Auth::user()['email']) ?></div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?page=dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="?page=profil"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="?page=logout"
                               data-confirm="Yakin ingin logout dari sistem?">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="d-flex">
    <?php require BASE_PATH . '/views/layouts/sidebar.php'; ?>

    <main class="main-content flex-grow-1">
        <div class="content-wrapper">

<script>
    // Sidebar Toggle for Mobile
    const body = document.body;
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            body.classList.toggle('sidebar-open');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            body.classList.remove('sidebar-open');
        });
    }

    // Close sidebar on window resize if open
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            body.classList.remove('sidebar-open');
        }
    });
</script>
