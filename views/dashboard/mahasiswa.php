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
    <p>Selamat datang, <strong><?= e(Auth::user()['nama']) ?></strong>- <?= e($profil['email'] ?? '') ?></p>
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
            <div class="stat-value"><?= ($stats['disetujui'] ?? 0) + ($stats['pembimbing_ditentukan'] ?? 0) + ($stats['selesai'] ?? 0) + ($stats['selesai_fakultas'] ?? 0) + ($stats['sk_terbit'] ?? 0) ?></div>
            <div class="stat-label">Disetujui / Selesai</div>
            <i class="bi bi-patch-check-fill stat-icon"></i>
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
        <!-- Pembimbing Saya (Jika Ada) -->
        <?php if (!empty($pembimbing)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-success"></i>Dosen Pembimbing Saya</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($pembimbing as $pb): ?>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light-subtle">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width:44px;height:44px;font-size:1.1rem;flex-shrink:0;overflow:hidden;">
                                    <?php if (!empty($pb['foto'])): ?>
                                        <img src="<?= AVATAR_URL ?>/<?= e($pb['foto']) ?>" alt="Dosen" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                        <?= strtoupper(substr($pb['nama_dosen'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= e($pb['nama_dosen']) ?></div>
                                    <span class="badge bg-primary mt-1" style="font-size: 0.7rem;">Pembimbing <?= $pb['urutan'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <span class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pengajuan Terbaru</span>
                <a href="?page=pengajuan" class="btn btn-sm btn-link text-decoration-none">Lihat Semua</a>
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
                <div class="table-responsive">
                    <table class="table table-sititi align-middle mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4">JUDUL</th>
                                <th>BIDANG</th>
                                <th>STATUS</th>
                                <th>TANGGAL</th>
                                <th class="pe-4 text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark" style="max-width:250px">
                                        <?= e(truncate($r['judul'], 60)) ?>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary border px-2"><?= e($r['bidang']) ?></span></td>
                                <td><?= statusBadge($r['status']) ?></td>
                                <td class="text-muted small"><?= formatTanggal($r['created_at']) ?></td>
                                <td class="pe-4 text-end">
                                    <a href="?page=pengajuan.show&id=<?= $r['id'] ?>"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <span class="fw-bold"><i class="bi bi-people me-2 text-primary"></i>Kuota Dosen Pembimbing</span>
                <a href="?page=pengajuan.dosen" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4 py-2">NAMA DOSEN</th>
                                <th class="py-2">RUMPUN KEAHLIAN</th>
                                <th class="text-center py-2">SISA SLOT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($listDosen, 0, 5) as $d): 
                                $total = (int)($d['total_bimbingan'] ?? 0);
                                $limit = defined('MAX_PEMBIMBING_PER_ANGKATAN') ? MAX_PEMBIMBING_PER_ANGKATAN : 10;
                                $sisa  = max(0, $limit - $total);
                                $color = 'success';
                                if ($sisa <= 3) $color = 'warning';
                                if ($sisa == 0) $color = 'danger';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= e($d['nama']) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;">NIDN: <?= e($d['nidn']) ?></div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;">
                                        <span class="text-muted small"><?= e($d['bidang_keahlian'] ?: '-') ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> px-3">
                                        <?= $sisa ?> Slot
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
