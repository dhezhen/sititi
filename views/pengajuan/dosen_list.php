<?php
$pageTitle = 'Daftar Dosen Pembimbing';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Dosen Pembimbing</li>
        </ol>
    </nav>
    <h1><i class="bi bi-people me-2 text-primary"></i>Daftar Dosen Pembimbing</h1>
    <p>Informasi beban bimbingan dan rumpun keilmuan dosen untuk referensi pengajuan judul skripsi.</p>
</div>

<?php renderFlash(); ?>

<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Data Dosen & Kapasitas Bimbingan</h5>
                    <div class="badge bg-primary px-3 py-2">Total: <?= count($listDosen) ?> Dosen</div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Dosen</th>
                                <th>NIDN</th>
                                <th>Rumpun Keahlian</th>
                                <th class="text-center">Sisa Slot</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listDosen)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-info-circle fs-2 text-muted d-block mb-2"></i>
                                    <p class="text-muted">Data dosen tidak ditemukan.</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($listDosen as $d): 
                                $total = (int)$d['total_bimbingan'];
                                $limit = defined('MAX_PEMBIMBING_PER_ANGKATAN') ? MAX_PEMBIMBING_PER_ANGKATAN : 10;
                                $sisa  = $limit - $total;
                                if ($sisa < 0) $sisa = 0;
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; background-color: #eef2ff; color: #4f46e5;">
                                            <?= strtoupper(substr($d['nama'] ?? 'D', 0, 1)) ?>
                                        </div>
                                        <div class="fw-bold text-dark"><?= e($d['nama']) ?></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border fw-normal"><?= e($d['nidn']) ?></span></td>
                                <td>
                                    <?php if ($d['bidang_keahlian']): ?>
                                        <?php 
                                        $bidang = explode(',', $d['bidang_keahlian']);
                                        foreach ($bidang as $b):
                                        ?>
                                        <span class="badge bg-soft-info text-info border border-info-subtle me-1" style="background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;">
                                            <?= e(trim($b)) ?>
                                        </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>Belum ditentukan</em></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $color = 'success';
                                    if ($sisa <= 3) $color = 'warning';
                                    if ($sisa == 0) $color = 'danger';
                                    ?>
                                    <span class="badge rounded-pill bg-<?= $color ?> px-3">
                                        <?= $sisa ?> Slot
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <?php if ($sisa > 0): ?>
                                    <a href="?page=pengajuan.create&p1=<?= $d['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="bi bi-plus-lg me-1"></i>Pilih
                                    </a>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary rounded-pill px-3" disabled>Penuh</button>
                                    <?php endif; ?>
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

<style>
.bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
.bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
.bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
</style>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
