<?php
$pageTitle = 'Dashboard Dosen';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <h1>Selamat Datang, <?= e(Auth::user()['nama']) ?></h1>
    <p class="text-muted">Anda login sebagai Dosen Pembimbing.</p>
</div>

<div class="row g-4">
    <!-- Statistik & Kuota -->
    <div class="col-md-4">
        <div class="card bg-primary text-white border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <h6 class="text-white-50 mb-1">Total Mahasiswa Bimbingan</h6>
                <h2 class="mb-0 fw-bold"><?= count($bimbingan) ?></h2>
                <i class="bi bi-people fs-1 text-white-50 position-absolute end-0 bottom-0 mb-3 me-3"></i>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <span class="fw-bold"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Kapasitas Bimbingan per Angkatan</span>
                <small class="text-muted ms-2">(Max <?= MAX_PEMBIMBING_PER_ANGKATAN ?> mhs / angkatan)</small>
            </div>
            <div class="card-body">
                <?php if (empty($workload)): ?>
                    <p class="text-muted small mb-0">Belum ada data bimbingan per angkatan.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($workload as $w): 
                            $percent = ($w['total'] / MAX_PEMBIMBING_PER_ANGKATAN) * 100;
                            $color = 'bg-primary';
                            if ($percent >= 90) $color = 'bg-danger';
                            elseif ($percent >= 70) $color = 'bg-warning';
                        ?>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="fw-semibold">Angkatan <?= e($w['angkatan']) ?></span>
                                <span><?= $w['total'] ?> / <?= MAX_PEMBIMBING_PER_ANGKATAN ?></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar <?= $color ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Daftar Mahasiswa Bimbingan -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-mortarboard me-2"></i>Mahasiswa Bimbingan Anda</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Judul Skripsi</th>
                                <th>Posisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bimbingan)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-info-circle d-block fs-2 mb-2"></i>
                                    Belum ada mahasiswa bimbingan yang ditetapkan.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($bimbingan as $b): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= e($b['nama_mahasiswa']) ?></div>
                                    <div class="small text-muted"><?= e($b['nim']) ?></div>
                                </td>
                                <td>
                                    <div class="small text-truncate" style="max-width: 300px;" title="<?= e($b['judul']) ?>">
                                        <?= e($b['judul']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ((int)$b['urutan'] === 1): ?>
                                        <span class="badge bg-primary">Pembimbing Utama</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Pembimbing Pendamping</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?page=pengajuan.show&id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
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

    <!-- Notifikasi Terakhir -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bell me-2"></i>Notifikasi Terbaru
            </div>
            <div class="card-body p-0">
                <?php if (empty($notifasi)): ?>
                <div class="text-center py-5 text-muted small">Tidak ada notifikasi.</div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($notifasi as $n): ?>
                    <div class="list-group-item p-3">
                        <div class="small mb-1"><?= e($n['pesan']) ?></div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-clock me-1"></i><?= formatTanggal($n['created_at'], true) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
