<?php
$pageTitle = 'Manajemen Periode';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Manajemen Periode</li>
        </ol></nav>
        <h1><i class="bi bi-calendar3 me-2 text-primary"></i>Manajemen Periode</h1>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-1"></i>Tambah Periode
    </button>
</div>

<?php renderFlash(); ?>

<div class="card">
    <div class="card-header"><i class="bi bi-table me-2"></i>Daftar Periode Akademik</div>
    <div class="card-body p-0">
        <table class="table table-sititi mb-0">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Nama Periode</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data periode.</td></tr>
                <?php else: ?>
                <?php $no = ($page - 1) * $perPage + 1; foreach ($list as $r): ?>
                <tr>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td class="fw-semibold"><?= e($r['nama']) ?></td>
                    <td><?= formatTanggal($r['tanggal_mulai']) ?></td>
                    <td><?= formatTanggal($r['tanggal_selesai']) ?></td>
                    <td>
                        <?php if ($r['is_active']): ?>
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$r['is_active']): ?>
                        <a href="?page=admin.periode.set_active&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-success me-1" data-bs-toggle="tooltip" title="Set Aktif" data-confirm="Aktifkan periode ini? Periode lain akan otomatis dinonaktifkan.">
                            <i class="bi bi-check2-circle"></i>
                        </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id'] ?>" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="?page=admin.periode.delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Yakin hapus periode ini?">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $r['id'] ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="?page=admin.periode.update" method="POST">
                        <?php csrfField(); ?>
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Periode</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Periode</label>
                                <input type="text" name="nama" class="form-control" value="<?= e($r['nama']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control" value="<?= $r['tanggal_mulai'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" value="<?= $r['tanggal_selesai'] ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
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
                    <a class="page-link" href="?page=admin.periode&p=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul></nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="?page=admin.periode.store" method="POST">
        <?php csrfField(); ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Periode Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Periode</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Ganjil 2025/2026" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" required>
            </div>
            <div class="form-check form-switch mt-4">
                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1">
                <label class="form-check-label fw-bold" for="is_active">Jadikan Periode Aktif</label>
                <div class="form-text">Jika diaktifkan, periode yang aktif saat ini akan otomatis dinonaktifkan.</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Periode</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
