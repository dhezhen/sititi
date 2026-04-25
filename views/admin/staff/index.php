<?php
$pageTitle = 'Data Staff Akademik';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Data Staff</li>
        </ol></nav>
        <h1><i class="bi bi-person-workspace me-2 text-primary"></i>Data Staff Akademik</h1>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Staff
        </button>
    </div>
</div>

<?php renderFlash(); ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-table me-2 text-primary"></i>Daftar Akun Staff</span>
        <form action="" method="GET" class="d-flex gap-2">
            <input type="hidden" name="page" value="admin.staff">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama/Email..." value="<?= e($search) ?>">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width:50px" class="ps-4">#</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th style="width:120px" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data staff akademik.</td>
                    </tr>
                    <?php else: ?>
                    <?php $no = ($page - 1) * $perPage + 1; foreach ($list as $r): ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $no++ ?></td>
                        <td class="fw-semibold text-dark"><?= e($r['nama']) ?></td>
                        <td><code class="text-primary small"><?= e($r['email']) ?></code></td>
                        <td>
                            <?php if ($r['is_active']): ?>
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Aktif</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id'] ?>" title="Edit Data">
                                <i class="bi bi-pencil text-warning"></i>
                            </button>
                            <a href="?page=admin.staff.delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-light border" data-confirm="Yakin ingin menghapus akun staff ini?" title="Hapus Data">
                                <i class="bi bi-trash text-danger"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit per Row -->
                    <div class="modal fade" id="editModal<?= $r['id'] ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content border-0 shadow">
                          <form action="?page=admin.staff.update" method="POST">
                            <?php csrfField(); ?>
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <div class="modal-header border-0">
                              <h5 class="modal-title fw-bold">Edit Akun Staff</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" value="<?= e($r['nama']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= e($r['email']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Password Baru <span class="text-muted fw-normal fs-7">(Kosongkan jika tidak diubah)</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Status Akun</label>
                                    <select name="is_active" class="form-select">
                                        <option value="1" <?= $r['is_active'] ? 'selected' : '' ?>>Aktif</option>
                                        <option value="0" <?= !$r['is_active'] ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pages > 1): ?>
        <div class="d-flex justify-content-center py-4 border-top">
            <nav><ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=admin.staff&p=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
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
    <div class="modal-content border-0 shadow">
      <form action="?page=admin.staff.store" method="POST">
        <?php csrfField(); ?>
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Tambah Staff Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama staff..." required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="nama@uniku.ac.id" required>
            </div>
            <div class="mb-0">
                <label class="form-label fw-semibold">Password Default</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>
        </div>
        <div class="modal-footer border-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
