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

<?php if ($siapAccCount > 0): ?>
<div class="alert alert-success d-flex align-items-center shadow-sm border-success border-2 mb-4" role="alert">
    <i class="bi bi-patch-check-fill fs-3 me-3 text-success"></i>
    <div class="flex-grow-1">
        <h6 class="mb-0 fw-bold text-dark">Antrean ACC Tahap Akhir</h6>
        <div class="small text-muted">Terdapat <b><?= $siapAccCount ?> pengajuan</b> yang telah selesai direview Dewan & siap Anda setujui akhir.</div>
    </div>
    <a href="#section-acc" class="btn btn-success btn-sm px-3 shadow-sm ms-3">
        <i class="bi bi-arrow-down-circle me-1"></i>Lihat Daftar
    </a>
</div>
<?php endif; ?>

<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="?page=kaprodi" class="btn btn-primary shadow-sm"><i class="bi bi-journal-check me-2"></i>Antrean Review</a>
    <a href="?page=staff.siap_fakultas" class="btn btn-outline-primary bg-white shadow-sm"><i class="bi bi-send me-2"></i>Siap Ajukan Fakultas</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-6 col-md-2">
        <div class="stat-card stat-primary">
            <div class="stat-value"><?= $diverifikasi ?></div>
            <div class="stat-label">Baru</div>
            <i class="bi bi-eye stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card stat-warning">
            <div class="stat-value"><?= $direview ?></div>
            <div class="stat-label">Di Dewan</div>
            <i class="bi bi-hourglass stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card border-2 border-success shadow-sm" style="background: #f0fff4;">
            <div class="stat-value text-success"><?= $siapAccCount ?></div>
            <div class="stat-label fw-bold text-success">Siap ACC</div>
            <i class="bi bi-patch-check-fill stat-icon text-success opacity-25"></i>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card stat-success opacity-75">
            <div class="stat-value"><?= $disetujui ?></div>
            <div class="stat-label">Selesai</div>
            <i class="bi bi-check-all stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-danger">
            <div class="stat-value"><?= $ditolak ?></div>
            <div class="stat-label">Tolak/Revisi</div>
            <i class="bi bi-x-circle stat-icon"></i>
        </div>
    </div>
</div>

<!-- Antrean ACC Tahap Akhir (Setelah Review Dewan) -->
<?php if (!empty($siapAcc)): ?>
<div class="card mb-4 border-0 shadow-sm overflow-hidden" id="section-acc">
    <div class="card-header bg-success bg-opacity-10 border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-success"><i class="bi bi-patch-check-fill me-2"></i>Antrean ACC Tahap Akhir (Siap Diajukan ke Fakultas)</h6>
        <span class="badge bg-success"><?= count($siapAcc) ?> Pengajuan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3" style="width: 250px;">Mahasiswa</th>
                        <th>Judul Proposal</th>
                        <th class="text-center">Status Terakhir</th>
                        <th class="pe-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siapAcc as $acc): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark"><?= e($acc['nama_mahasiswa']) ?></div>
                            <div class="text-muted small"><?= e($acc['nim']) ?></div>
                        </td>
                        <td class="text-wrap" style="max-width: 400px;">
                            <div class="fw-semibold text-primary mb-1"><?= e(truncate($acc['judul'], 80)) ?></div>
                            <div class="small text-muted italic">Reviewer: <?= e($acc['nama_dewan_1'] ?? '-') ?> & <?= e($acc['nama_dewan_2'] ?? '-') ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2">Selesai Review Dewan</span>
                        </td>
                        <td class="pe-3 text-end">
                            <a href="?page=kaprodi.review&id=<?= $acc['id'] ?>" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i>ACC & Ajukan
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-eye me-2 text-primary"></i>Pengajuan Baru (Belum Direview)</span>
                <a href="?page=kaprodi" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent)): ?>
                <div class="empty-state py-5 text-center">
                    <i class="bi bi-check2-all fs-1 text-muted d-block mb-2"></i>
                    <h6 class="text-muted">Semua pengajuan baru sudah diproses.</h6>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th class="ps-3">Mahasiswa</th><th>Judul</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach ($recent as $r): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold small"><?= e($r['nama_mahasiswa']) ?></div>
                                    <small class="text-muted"><?= e($r['nim']) ?></small>
                                </td>
                                <td><div class="small text-dark"><?= e(truncate($r['judul'], 60)) ?></div></td>
                                <td>
                                    <a href="?page=kaprodi.review&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                        Review
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
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-bell me-2 text-primary"></i>Notifikasi</div>
            <div class="list-group list-group-flush">
                <?php if (empty($notifasi)): ?>
                <div class="text-center py-5 text-muted small"><i class="bi bi-bell-slash d-block fs-3 mb-2 opacity-25"></i>Tidak ada notifikasi</div>
                <?php else: ?>
                <?php foreach ($notifasi as $n): ?>
                <a href="?page=notif.read&id=<?= $n['id'] ?>" class="list-group-item list-group-item-action <?= !$n['is_read'] ? 'bg-light' : '' ?> px-3 py-2 border-0">
                    <p class="mb-0 small" style="font-size: 0.8rem;"><?= e(truncate($n['pesan'], 80)) ?></p>
                    <span class="text-muted" style="font-size:.65rem"><?= formatTanggal($n['created_at'], true) ?></span>
                </a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Tren Bidang / Topik Skripsi
            </div>
            <div class="card-body">
                <canvas id="bidangChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill me-2 text-success"></i>Statistik Pengajuan (6 Bulan Terakhir)
            </div>
            <div class="card-body">
                <canvas id="trendChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4 border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i>Monitoring Pengajuan Per Angkatan (Prodi Anda)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Angkatan</th>
                        <th>Total Mahasiswa</th>
                        <th>Sudah Mengajukan</th>
                        <th>Proses Aktif</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($angkatanStats)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted small">Belum ada data mahasiswa untuk prodi ini.</td></tr>
                    <?php else: ?>
                    <?php foreach ($angkatanStats as $s): 
                        $total = $s['total_mahasiswa'];
                        $sudah = $s['sudah_mengajukan'];
                        $persen = $total > 0 ? round(($sudah / $total) * 100) : 0;
                    ?>
                    <tr>
                        <td class="ps-3 fw-bold">Angkatan <?= $s['angkatan'] ?></td>
                        <td><?= number_format($total) ?></td>
                        <td class="text-success fw-bold"><?= number_format($sudah) ?></td>
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

<?php require BASE_PATH . '/views/components/workload_table.php'; ?>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data Bidang
    const bidangLabels = <?= json_encode(array_column($bidangStats ?? [], 'bidang')) ?>;
    const bidangData = <?= json_encode(array_column($bidangStats ?? [], 'jumlah')) ?>;
    
    // Data Trend
    const trendLabels = <?= json_encode(array_column($trendStats ?? [], 'bulan')) ?>;
    const trendData = <?= json_encode(array_column($trendStats ?? [], 'jumlah')) ?>;

    const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#0ea5e9', '#64748b'];

    if (document.getElementById('bidangChart') && bidangLabels.length > 0) {
        new Chart(document.getElementById('bidangChart'), {
            type: 'doughnut',
            data: {
                labels: bidangLabels,
                datasets: [{ data: bidangData, backgroundColor: colors, borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        });
    }

    if (document.getElementById('trendChart') && trendLabels.length > 0) {
        new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: trendLabels,
                datasets: [{ label: 'Jumlah Pengajuan', data: trendData, backgroundColor: '#4f46e5', borderRadius: 6 }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false } } }
        });
    }
});
</script>
