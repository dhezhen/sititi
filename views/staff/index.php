<?php
$pageTitle = 'Verifikasi Berkas Pengajuan';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Verifikasi Berkas</li>
    </ol></nav>
    <h1><i class="bi bi-clipboard-check me-2 text-primary"></i>Verifikasi Berkas Pengajuan</h1>
</div>

<?php renderFlash(); ?>

<ul class="nav nav-pills gap-2 mb-4">
    <li class="nav-item">
        <a class="nav-link <?= $status === '' ? 'active' : 'bg-white border' ?>" href="?page=staff">
            <i class="bi bi-grid-fill me-1"></i> Semua Pengajuan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'diajukan' ? 'active' : 'bg-white border' ?>" href="?page=staff&status=diajukan">
            <i class="bi bi-plus-circle-fill me-1"></i> Belum Diproses
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'diverifikasi' ? 'active' : 'bg-white border' ?>" href="?page=staff&status=diverifikasi">
            <i class="bi bi-check-circle-fill me-1 text-success"></i> Sudah Terverifikasi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'revisi' ? 'active' : 'bg-white border' ?>" href="?page=staff&status=revisi">
            <i class="bi bi-arrow-counterclockwise me-1 text-warning"></i> Perlu Revisi
        </a>
    </li>
</ul>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-center">
            <input type="hidden" name="page" value="staff">
            <input type="hidden" name="status" value="<?= e($status) ?>">
            <div class="col-md-9">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" name="search"
                           value="<?= e($search) ?>" placeholder="Cari nama mahasiswa, NIM, atau judul skripsi...">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Terapkan Pencarian
                </button>
            </div>
        </form>
    </div>
</div>


<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-list-ul me-2 text-primary"></i>Daftar Pengajuan
        <span class="badge bg-primary ms-2"><?= $total ?></span></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($list)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i><h5>Tidak Ada Data</h5>
            <p>Tidak ada pengajuan yang sesuai filter.</p>
        </div>
        <?php else: ?>
        <table class="table table-sititi mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Mahasiswa</th><th>Judul</th>
                    <th>Bidang</th><th>Status</th><th>Diajukan</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = ($page - 1) * $perPage + 1; foreach ($list as $r): ?>
                <tr>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($r['nama_mahasiswa']) ?></div>
                        <small class="text-muted"><?= e($r['nim']) ?></small>
                    </td>
                    <td><?= e(truncate($r['judul'], 50)) ?></td>
                    <td><span class="badge bg-secondary"><?= e($r['bidang']) ?></span></td>
                    <td><?= statusBadge($r['status']) ?></td>
                    <td class="small text-muted"><?= formatTanggal($r['created_at']) ?></td>
                    <td>
                        <?php if ($r['status'] === 'diajukan'): ?>
                        <a href="?page=staff.verifikasi&id=<?= $r['id'] ?>"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-clipboard-check me-1"></i>Verifikasi
                        </a>
                        <?php else: ?>
                        <a href="?page=pengajuan.show&id=<?= $r['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Detail
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($pages > 1): ?>
        <div class="d-flex justify-content-center py-3">
            <nav><ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?page=staff&p=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul></nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
