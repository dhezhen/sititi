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

<!-- Statistik per Angkatan -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-graph-up me-2 text-primary"></i>Monitoring Pengajuan Per Angkatan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sititi mb-0">
                <thead>
                    <tr>
                        <th>Angkatan</th>
                        <th>Total Mahasiswa</th>
                        <th>Sudah Mengajukan</th>
                        <th>Belum Mengajukan</th>
                        <th>Proses Aktif</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($angkatanStats)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted small">Belum ada data mahasiswa.</td></tr>
                    <?php else: ?>
                    <?php foreach ($angkatanStats as $s): 
                        $total = $s['total_mahasiswa'];
                        $sudah = $s['sudah_mengajukan'];
                        $belum = $total - $sudah;
                        $persen = $total > 0 ? round(($sudah / $total) * 100) : 0;
                    ?>
                    <tr>
                        <td class="fw-bold">Angkatan <?= $s['angkatan'] ?></td>
                        <td><?= number_format($total) ?></td>
                        <td class="text-success fw-bold"><?= number_format($sudah) ?></td>
                        <td class="text-danger fw-bold"><?= number_format($belum) ?></td>
                        <td><span class="badge bg-info text-dark"><?= number_format($s['pengajuan_aktif']) ?></span></td>
                        <td style="width: 150px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $persen ?>%"></div>
                                </div>
                                <span class="small fw-bold" style="font-size: 0.75rem;"><?= $persen ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Monitoring Beban Dosen -->
<div class="card mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i>Monitoring Beban Bimbingan Dosen</span>
        <span class="badge bg-light text-dark border">Limit: <?= MAX_PEMBIMBING_PER_ANGKATAN ?> Mhs / Angkatan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Dosen</th>
                        <th>Beban per Angkatan (Progress)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $groupedWorkload = [];
                    foreach ($dosenWorkload as $dw) {
                        $groupedWorkload[$dw['id']]['nama'] = $dw['nama'];
                        $groupedWorkload[$dw['id']]['nidn'] = $dw['nidn'];
                        if ($dw['angkatan']) {
                            $groupedWorkload[$dw['id']]['loads'][$dw['angkatan']] = $dw['total'];
                        }
                    }
                    if (empty($groupedWorkload)): ?>
                    <tr><td colspan="2" class="text-center py-4 text-muted">Belum ada data dosen.</td></tr>
                    <?php else: ?>
                    <?php foreach ($groupedWorkload as $id => $d): ?>
                    <tr>
                        <td class="ps-4" style="width: 280px;">
                            <div class="fw-bold"><?= e($d['nama']) ?></div>
                            <div class="small text-muted">NIDN: <?= e($d['nidn']) ?></div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-2 py-1">
                                <?php if (empty($d['loads'])): ?>
                                    <span class="text-muted small italic">Belum ada bimbingan aktif</span>
                                <?php else: ?>
                                    <?php 
                                    ksort($d['loads']); // Urutkan angkatan
                                    foreach ($d['loads'] as $angkatan => $total): 
                                        $percent = ($total / MAX_PEMBIMBING_PER_ANGKATAN) * 100;
                                        $bg = 'bg-primary';
                                        if ($percent >= 90) $bg = 'bg-danger';
                                        elseif ($percent >= 70) $bg = 'bg-warning';
                                    ?>
                                    <div class="p-2 border rounded bg-white shadow-sm" style="min-width: 110px;">
                                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.7rem;">
                                            <span class="fw-bold text-muted"><?= $angkatan ?></span>
                                            <span class="fw-bold <?= $percent >= 90 ? 'text-danger' : '' ?>"><?= $total ?>/<?= MAX_PEMBIMBING_PER_ANGKATAN ?></span>
                                        </div>
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar <?= $bg ?>" style="width: <?= $percent ?>%"></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
