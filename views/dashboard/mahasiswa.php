<?php
$pageTitle = 'Dashboard Mahasiswa';
require BASE_PATH . '/views/layouts/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
    <h1><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Mahasiswa</h1>
    <p>Selamat datang, <strong><?= e(Auth::user()['nama']) ?></strong> — <?= e($profil['nim'] ?? '') ?></p>
</div>

<?php renderFlash(); ?>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-primary">
            <div class="stat-value"><?= $stats['draft'] ?? 0 ?></div>
            <div class="stat-label">Draft</div>
            <i class="bi bi-file-earmark stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-info">
            <div class="stat-value"><?= ($stats['diajukan'] ?? 0) + ($stats['diverifikasi'] ?? 0) + ($stats['direview'] ?? 0) ?></div>
            <div class="stat-label">Sedang Diproses</div>
            <i class="bi bi-hourglass-split stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-success">
            <div class="stat-value"><?= ($stats['disetujui'] ?? 0) + ($stats['pembimbing_ditentukan'] ?? 0) ?></div>
            <div class="stat-label">Disetujui</div>
            <i class="bi bi-patch-check stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-warning">
            <div class="stat-value"><?= $stats['revisi'] ?? 0 ?></div>
            <div class="stat-label">Perlu Revisi</div>
            <i class="bi bi-pencil-square stat-icon"></i>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Riwayat Pengajuan -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pengajuan</span>
                <a href="?page=pengajuan" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($riwayat)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-folder2-open"></i>
                    <h5>Belum Ada Pengajuan</h5>
                    <p class="mb-3">Mulai dengan membuat pengajuan judul skripsi.</p>
                    <?php if (!$hasActive): ?>
                    <a href="?page=pengajuan.create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Buat Pengajuan
                    </a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <table class="table table-sititi mb-0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Bidang</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayat as $r): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold" style="max-width:220px">
                                    <?= e(truncate($r['judul'], 50)) ?>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary"><?= e($r['bidang']) ?></span></td>
                            <td><?= statusBadge($r['status']) ?></td>
                            <td class="text-muted small"><?= formatTanggal($r['created_at']) ?></td>
                            <td>
                                <a href="?page=pengajuan.show&id=<?= $r['id'] ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Notifikasi & Quick Action -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-lightning-charge me-2 text-warning"></i>Aksi Cepat
            </div>
            <div class="card-body d-grid gap-2">
                <?php if (!$hasActive): ?>
                <a href="?page=pengajuan.create" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Buat Pengajuan Baru
                </a>
                <?php endif; ?>
                <a href="?page=pengajuan" class="btn btn-outline-secondary">
                    <i class="bi bi-list-ul me-2"></i>Lihat Semua Pengajuan
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-bell me-2 text-primary"></i>Notifikasi Terbaru
            </div>
            <div class="list-group list-group-flush">
                <?php if (empty($notifasi)): ?>
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-bell-slash d-block fs-3 mb-2"></i>Tidak ada notifikasi
                </div>
                <?php else: ?>
                <?php foreach ($notifasi as $n): ?>
                <a href="?page=notif.read&id=<?= $n['id'] ?><?= $n['pengajuan_id'] ? '&ref='.$n['pengajuan_id'] : '' ?>"
                   class="list-group-item list-group-item-action <?= !$n['is_read'] ? 'bg-light' : '' ?> px-3 py-2">
                    <div class="d-flex align-items-start gap-2">
                        <div class="notif-dot <?= !$n['is_read'] ? 'active' : '' ?> mt-1"></div>
                        <div>
                            <p class="mb-0 small"><?= e(truncate($n['pesan'], 80)) ?></p>
                            <span class="text-muted" style="font-size:.72rem"><?= formatTanggal($n['created_at'], true) ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
