<?php
$pageTitle = 'Data Dosen';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Data Dosen</li>
        </ol></nav>
        <h1><i class="bi bi-person-badge me-2 text-primary"></i>Data Dosen</h1>
    </div>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-file-earmark-excel me-1"></i> Import Data
    </button>
</div>

<?php renderFlash(); ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table me-2"></i>Daftar Dosen</span>
        <form action="" method="GET" class="d-flex gap-2">
            <input type="hidden" name="page" value="admin.dosen">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIDN / Nama..." value="<?= e($search) ?>">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-sititi mb-0">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>NIDN</th>
                    <th>Nama Lengkap</th>
                    <th>Program Studi</th>
                    <th>Bidang Keahlian</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data dosen.</td>
                </tr>
                <?php else: ?>
                <?php $no = ($page - 1) * $perPage + 1; foreach ($list as $r): ?>
                <tr>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td class="fw-semibold"><?= e($r['nidn']) ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($r['nama']) ?></div>
                        <?php if ($r['email']): ?>
                        <div class="small text-muted"><?= e($r['email']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= e($r['nama_prodi'] ?? '-') ?></td>
                    <td><span class="badge bg-secondary"><?= e($r['bidang_keahlian'] ?? '-') ?></span></td>
                    <td>
                        <?php if ($r['is_active']): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="d-flex justify-content-center py-3">
            <nav><ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=admin.dosen&p=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul></nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Import CSV -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="?page=admin.import_dosen" method="POST" enctype="multipart/form-data">
        <?php csrfField(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Data Dosen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info small">
                <i class="bi bi-info-circle me-1"></i> Anda dapat mengupload banyak data dosen sekaligus menggunakan file CSV. 
                Pastikan format sesuai dengan template yang disediakan.
            </div>
            <div class="mb-3">
                <a href="<?= APP_URL ?>/assets/templates/template_dosen.csv" class="btn btn-outline-secondary btn-sm" download>
                    <i class="bi bi-download me-1"></i> Download Template CSV
                </a>
            </div>
            <div class="mb-3">
                <label for="file_csv" class="form-label fw-bold">Upload File CSV</label>
                <input class="form-control" type="file" id="file_csv" name="file_csv" accept=".csv" required>
            </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i> Import Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
