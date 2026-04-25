<?php
$pageTitle = 'Manajemen Database';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Manajemen Database</li>
        </ol></nav>
        <h1><i class="bi bi-database me-2 text-primary"></i>Manajemen Database</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="?page=superadmin.database.backup" class="btn btn-success shadow-sm">
            <i class="bi bi-cloud-download me-1"></i> Backup Sekarang (.sql)
        </a>
    </div>
</div>

<?php renderFlash(); ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table me-2"></i>Daftar Tabel Database</span>
                <span class="badge bg-primary"><?= count($tables) ?> Tabel</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sititi mb-0">
                        <thead>
                            <tr>
                                <th>Nama Tabel</th>
                                <th>Engine</th>
                                <th class="text-end">Baris</th>
                                <th class="text-end">Ukuran Data</th>
                                <th class="text-end">Ukuran Index</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalSize = 0;
                            foreach ($tables as $t): 
                                $size = $t['Data_length'] + $t['Index_length'];
                                $totalSize += $size;
                            ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= e($t['Name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= e($t['Engine']) ?></span></td>
                                <td class="text-end text-muted"><?= number_format($t['Rows']) ?></td>
                                <td class="text-end"><?= round($t['Data_length'] / 1024, 2) ?> KB</td>
                                <td class="text-end"><?= round($t['Index_length'] / 1024, 2) ?> KB</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3">Total Estimasi Ukuran</th>
                                <th colspan="2" class="text-end text-primary"><?= round($totalSize / (1024 * 1024), 2) ?> MB</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-primary shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Backup</h5>
                <p class="card-text text-muted small">
                    Fitur ini akan menghasilkan file dump SQL yang berisi struktur tabel dan semua data di dalamnya. 
                    Gunakan file ini untuk memulihkan database jika terjadi kerusakan data atau untuk keperluan migrasi.
                </p>
                <hr>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> <strong>Format:</strong> SQL (.sql)</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> <strong>Engine:</strong> MySQL / MariaDB</li>
                    <li><i class="bi bi-check2-circle text-success me-2"></i> <strong>Status:</strong> Siap untuk diunduh</li>
                </ul>
            </div>
        </div>
        
        <div class="alert alert-warning shadow-sm border-0">
            <div class="d-flex">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                <div>
                    <strong>Perhatian:</strong>
                    <p class="mb-0 small">Selalu backup data Anda secara berkala. Simpan file backup di tempat yang aman (off-site).</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
