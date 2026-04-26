<?php
$pageTitle = 'Dashboard Kaprodi';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    <h1><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Kaprodi / Sekprodi</h1>
    <p>Selamat datang, <strong><?= e(Auth::user()['nama']) ?></strong></p>
</div>

<?php renderFlash(); ?>

<?php if ($diverifikasi > 0): ?>
<div class="alert alert-warning d-flex align-items-center shadow-sm border-warning mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
    <div>
        <strong>Perhatian!</strong> Terdapat <b><?= $diverifikasi ?> pengajuan</b> yang baru diverifikasi oleh Staff dan menunggu review pertama dari Anda. 
        <a href="?page=kaprodi" class="alert-link ms-2">Lihat Antrean <i class="bi bi-arrow-right"></i></a>
    </div>
</div>
<?php endif; ?>

<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="?page=kaprodi" class="btn btn-primary shadow-sm"><i class="bi bi-journal-check me-2"></i>Mulai Review</a>
    <a href="?page=staff.siap_fakultas" class="btn btn-outline-primary bg-white shadow-sm"><i class="bi bi-send me-2"></i>Siap Ajukan Fakultas</a>
    <a href="?page=admin.dosen" class="btn btn-outline-secondary bg-white shadow-sm"><i class="bi bi-people me-2"></i>Kelola Dosen</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-primary">
            <div class="stat-value"><?= $diverifikasi ?></div>
            <div class="stat-label">Menunggu Review</div>
            <i class="bi bi-eye stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-warning">
            <div class="stat-value"><?= $direview ?></div>
            <div class="stat-label">Sedang Direview</div>
            <i class="bi bi-hourglass stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-success">
            <div class="stat-value"><?= $disetujui ?></div>
            <div class="stat-label">Disetujui</div>
            <i class="bi bi-patch-check stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-danger">
            <div class="stat-value"><?= $ditolak ?></div>
            <div class="stat-label">Ditolak</div>
            <i class="bi bi-x-circle stat-icon"></i>
        </div>
    </div>
</div>

<!-- Statistik per Angkatan -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-graph-up me-2 text-primary"></i>Monitoring Pengajuan Per Angkatan (Prodi Anda)</span>
        <span class="badge bg-light text-dark">Data Program Studi</span>
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
                    <tr><td colspan="6" class="text-center py-4 text-muted small">Belum ada data mahasiswa untuk prodi ini.</td></tr>
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

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-eye me-2 text-primary"></i>Pengajuan Perlu Direview</span>
                <a href="?page=kaprodi" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-check2-all"></i><h5>Semua Sudah Direview</h5>
                </div>
                <?php else: ?>
                <table class="table table-sititi mb-0">
                    <thead><tr><th>Mahasiswa</th><th>Judul</th><th>Prodi</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($recent as $r): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= e($r['nama_mahasiswa']) ?></div>
                                <small class="text-muted"><?= e($r['nim']) ?></small>
                            </td>
                            <td><?= e(truncate($r['judul'], 45)) ?></td>
                            <td><span class="badge bg-secondary"><?= e($r['nama_prodi'] ?? '-') ?></span></td>
                            <td>
                                <?php 
                                    $diff = strtotime(date('Y-m-d H:i:s')) - strtotime($r['created_at']);
                                    $days = floor($diff / (60 * 60 * 24));
                                ?>
                                <?php if ($days > 3): ?>
                                    <span class="badge bg-danger text-white mb-2 d-inline-block"><i class="bi bi-clock-history me-1"></i>Menunggu <?= $days ?> Hari</span><br>
                                <?php elseif ($days > 0): ?>
                                    <span class="badge bg-warning text-dark mb-2 d-inline-block"><i class="bi bi-clock-history me-1"></i>Menunggu <?= $days ?> Hari</span><br>
                                <?php else: ?>
                                    <span class="badge bg-success text-white mb-2 d-inline-block"><i class="bi bi-clock-history me-1"></i>Hari Ini</span><br>
                                <?php endif; ?>
                                
                                <a href="?page=kaprodi.review&id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i>Review
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
                <div class="text-center py-4 text-muted small"><i class="bi bi-bell-slash d-block fs-3 mb-2"></i>Tidak ada notifikasi</div>
                <?php else: ?>
                <?php foreach ($notifasi as $n): ?>
                <a href="?page=notif.read&id=<?= $n['id'] ?>" class="list-group-item list-group-item-action <?= !$n['is_read'] ? 'bg-light' : '' ?> px-3 py-2">
                    <p class="mb-0 small"><?= e(truncate($n['pesan'], 80)) ?></p>
                    <span class="text-muted" style="font-size:.72rem"><?= formatTanggal($n['created_at'], true) ?></span>
                </a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Rekap Beban Bimbingan -->
<?php require BASE_PATH . '/views/components/workload_table.php'; ?>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
