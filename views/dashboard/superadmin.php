<?php
$pageTitle = 'Dashboard Super Admin';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-end">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol></nav>
        <h1><i class="bi bi-speedometer2 me-2 text-primary"></i>Super Admin Dashboard</h1>
        <p class="mb-0">Selamat datang kembali, <strong><?= e(Auth::user()['nama']) ?></strong>.</p>
    </div>
</div>

<?php renderFlash(); ?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-primary">
            <div class="stat-value"><?= number_format($stats['users']) ?></div>
            <div class="stat-label">Total Pengguna</div>
            <i class="bi bi-people stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-success">
            <div class="stat-value"><?= number_format($stats['roles']) ?></div>
            <div class="stat-label">Total Role</div>
            <i class="bi bi-shield-lock stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-info">
            <div class="stat-value"><?= number_format($stats['menus']) ?></div>
            <div class="stat-label">Total Menu Dinamis</div>
            <i class="bi bi-list-nested stat-icon"></i>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up me-2"></i>Statistik Pengajuan Judul Per Angkatan</span>
                <span class="badge bg-primary">Monitoring Real-time</span>
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
                                <th>Pengajuan Aktif (Proses)</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($angkatanStats)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data mahasiswa.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($angkatanStats as $s): 
                                $total = $s['total_mahasiswa'];
                                $sudah = $s['sudah_mengajukan'];
                                $belum = $total - $sudah;
                                $persen = $total > 0 ? round(($sudah / $total) * 100) : 0;
                            ?>
                            <tr>
                                <td class="fw-bold">Angkatan <?= $s['angkatan'] ?></td>
                                <td><?= number_format($total) ?> Orang</td>
                                <td>
                                    <span class="text-success fw-semibold"><?= number_format($sudah) ?></span>
                                </td>
                                <td>
                                    <span class="text-danger fw-semibold"><?= number_format($belum) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><?= number_format($s['pengajuan_aktif']) ?> Pengajuan</span>
                                </td>
                                <td style="width: 200px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $persen ?>%"></div>
                                        </div>
                                        <span class="small fw-bold"><?= $persen ?>%</span>
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
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-ui-checks-grid me-2"></i>Akses Cepat</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="?page=superadmin.roles" class="text-decoration-none">
                            <div class="p-4 border rounded-3 text-center bg-light hover-shadow" style="transition: all 0.2s ease;">
                                <i class="bi bi-shield-lock text-primary" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3 mb-1 text-dark">Manajemen Role</h5>
                                <p class="text-muted small mb-0">Atur peran dan hak akses pengguna</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="?page=superadmin.menus" class="text-decoration-none">
                            <div class="p-4 border rounded-3 text-center bg-light hover-shadow" style="transition: all 0.2s ease;">
                                <i class="bi bi-list-nested text-success" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3 mb-1 text-dark">Manajemen Menu</h5>
                                <p class="text-muted small mb-0">Daftarkan URL menu ke sidebar</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bell me-2"></i>Notifikasi Terbaru</span>
                <a href="?page=notif" class="btn btn-sm btn-light">Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($notifasi)): ?>
                    <div class="empty-state py-4">
                        <i class="bi bi-bell-slash mb-2" style="font-size:2rem"></i>
                        <p class="mb-0">Tidak ada notifikasi baru.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($notifasi as $n): ?>
                        <a href="?page=notif.read&id=<?= $n['id'] ?>&ref=<?= $n['pengajuan_id'] ?>" 
                           class="list-group-item list-group-item-action p-3 <?= !$n['is_read'] ? 'bg-light' : '' ?>">
                            <div class="d-flex gap-3">
                                <div class="notif-dot <?= !$n['is_read'] ? 'active' : '' ?>"></div>
                                <div>
                                    <h6 class="mb-1 text-dark"><?= e($n['judul']) ?></h6>
                                    <p class="mb-1 small text-muted"><?= e($n['pesan']) ?></p>
                                    <small class="text-secondary"><?= formatTanggal($n['created_at'], true) ?></small>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Rekap Beban Bimbingan -->
<?php require BASE_PATH . '/views/components/workload_table.php'; ?>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
