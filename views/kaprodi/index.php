<?php
$pageTitle = 'Review Pengajuan — Kaprodi';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Review Pengajuan</li>
    </ol></nav>
    <h1><i class="bi bi-eye me-2 text-primary"></i>Review Pengajuan Judul Skripsi</h1>
</div>

<?php renderFlash(); ?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="kaprodi">
            <div class="col-md-5">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" name="search"
                           value="<?= e($search) ?>" placeholder="Cari nama, NIM, atau judul...">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="diverifikasi"  <?= $status === 'diverifikasi'  ? 'selected' : '' ?>>Diverifikasi</option>
                    <option value="direview"      <?= $status === 'direview'      ? 'selected' : '' ?>>Sedang Direview</option>
                    <option value="disetujui"     <?= $status === 'disetujui'     ? 'selected' : '' ?>>Disetujui</option>
                    <option value="ditolak"       <?= $status === 'ditolak'       ? 'selected' : '' ?>>Ditolak</option>
                    <option value="revisi"        <?= $status === 'revisi'        ? 'selected' : '' ?>>Revisi</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul me-2 text-primary"></i>Daftar Pengajuan
        <span class="badge bg-primary ms-2"><?= $total ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($list)): ?>
        <div class="empty-state"><i class="bi bi-check2-all"></i><h5>Tidak Ada Data</h5></div>
        <?php else: ?>
        <table class="table table-sititi mb-0">
            <thead>
                <tr><th>#</th><th>Mahasiswa</th><th>Judul</th><th>Prodi</th>
                    <th>Status</th><th>Diajukan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php $no = ($page-1)*$perPage+1; foreach ($list as $r): ?>
                <tr>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($r['nama_mahasiswa']) ?></div>
                        <small class="text-muted"><?= e($r['nim']) ?></small>
                    </td>
                    <td><?= e(truncate($r['judul'], 50)) ?></td>
                    <td><span class="badge bg-secondary"><?= e($r['nama_prodi'] ?? '-') ?></span></td>
                    <td><?= statusBadge($r['status']) ?></td>
                    <td class="small text-muted"><?= formatTanggal($r['created_at']) ?></td>
                    <td>
                        <?php if (in_array($r['status'], ['diverifikasi', 'direview'])): ?>
                        <a href="?page=kaprodi.review&id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye me-1"></i>Review
                        </a>
                        <?php else: ?>
                        <a href="?page=pengajuan.show&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">
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
                <?php for ($i=1; $i<=$pages; $i++): ?>
                <li class="page-item <?= $i===$page?'active':'' ?>">
                    <a class="page-link"
                       href="?page=kaprodi&p=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">
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
