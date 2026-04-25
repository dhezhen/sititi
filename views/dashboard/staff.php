<?php
$pageTitle = 'Dashboard Staff Akademik';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    <h1><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Staff Akademik</h1>
    <p>Selamat datang, <strong><?= e(Auth::user()['nama']) ?></strong></p>
</div>

<?php renderFlash(); ?>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-info">
            <div class="stat-value"><?= $diajukan ?></div>
            <div class="stat-label">Menunggu Verifikasi</div>
            <i class="bi bi-hourglass stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-success">
            <div class="stat-value"><?= $diverifikasi ?></div>
            <div class="stat-label">Sudah Diverifikasi</div>
            <i class="bi bi-check-circle stat-icon"></i>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-inbox me-2 text-primary"></i>Pengajuan Masuk (Terbaru)</span>
                <a href="?page=staff" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-inbox"></i><h5>Tidak Ada Pengajuan Masuk</h5>
                </div>
                <?php else: ?>
                <table class="table table-sititi mb-0">
                    <thead><tr><th>Mahasiswa</th><th>Judul</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($recent as $r): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= e($r['nama_mahasiswa']) ?></div>
                                <small class="text-muted"><?= e($r['nim']) ?></small>
                            </td>
                            <td><?= e(truncate($r['judul'], 50)) ?></td>
                            <td><?= statusBadge($r['status']) ?></td>
                            <td>
                                <a href="?page=staff.verifikasi&id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-clipboard-check me-1"></i>Verifikasi
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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-bell me-2 text-primary"></i>Notifikasi</div>
            <div class="list-group list-group-flush">
                <?php if (empty($notifasi)): ?>
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-bell-slash d-block fs-3 mb-2"></i>Tidak ada notifikasi
                </div>
                <?php else: ?>
                <?php foreach ($notifasi as $n): ?>
                <a href="?page=notif.read&id=<?= $n['id'] ?>"
                   class="list-group-item list-group-item-action <?= !$n['is_read'] ? 'bg-light' : '' ?> px-3 py-2">
                    <p class="mb-0 small"><?= e(truncate($n['pesan'], 80)) ?></p>
                    <span class="text-muted" style="font-size:.72rem"><?= formatTanggal($n['created_at'], true) ?></span>
                </a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
