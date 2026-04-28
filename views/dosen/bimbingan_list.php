<?php
$pageTitle = 'Daftar Mahasiswa Bimbingan';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Bimbingan</li>
        </ol>
    </nav>
    <h1><i class="bi bi-people me-2 text-primary"></i>Mahasiswa Bimbingan</h1>
    <p>Daftar mahasiswa yang Anda bimbing dalam pengajuan judul skripsi.</p>
</div>

<?php renderFlash(); ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($listBimbingan)): ?>
        <div class="empty-state">
            <i class="bi bi-person-video3"></i>
            <h5>Belum Ada Mahasiswa</h5>
            <p>Anda belum memiliki mahasiswa bimbingan yang terdaftar di sistem.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sititi align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Mahasiswa</th>
                        <th>NIM</th>
                        <th>Tipe</th>
                        <th>Judul Skripsi</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listBimbingan as $b): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold"><?= e($b['nama_mahasiswa']) ?></div>
                        </td>
                        <td><code class="text-primary fw-medium"><?= e($b['nim']) ?></code></td>
                        <td>
                            <?php if ($b['tipe_pembimbing'] === 'Resmi'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2">Pembimbing Resmi</span>
                            <?php else: ?>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2">Calon Pembimbing</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small fw-semibold text-truncate" style="max-width: 300px;" title="<?= e($b['judul']) ?>">
                                <?= e($b['judul']) ?>
                            </div>
                        </td>
                        <td><?= statusBadge($b['status']) ?></td>
                        <td class="pe-4 text-end">
                            <a href="?page=dosen.bimbingan.show&id=<?= $b['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-chat-dots me-1"></i>Bimbingan
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

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
