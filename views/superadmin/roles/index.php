<?php
$pageTitle = 'Manajemen Role';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Manajemen Role</li>
        </ol></nav>
        <h1><i class="bi bi-shield-lock me-2 text-primary"></i>Manajemen Role</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="?page=superadmin.role_menus" class="btn btn-warning text-white">
            <i class="bi bi-ui-checks-grid me-1"></i> Atur Hak Akses
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Role
        </button>
    </div>
</div>

<?php renderFlash(); ?>

<div class="card">
    <div class="card-header"><i class="bi bi-table me-2"></i>Daftar Role Pengguna</div>
    <div class="card-body p-0">
        <table class="table table-sititi mb-0">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Kode Role</th>
                    <th>Nama Role</th>
                    <th style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data role.</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach ($list as $r): ?>
                <tr>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td class="fw-bold text-primary"><?= e($r['kode']) ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td>
                        <?php if (!in_array($r['kode'], ['superadmin'])): ?>
                        <a href="?page=superadmin.roles.delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Yakin ingin menghapus role ini? Ini juga akan menghapus semua hak aksesnya!">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="?page=superadmin.roles.store" method="POST">
        <?php csrfField(); ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Role Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Kode Role <span class="text-danger">*</span></label>
                <input type="text" name="kode" class="form-control" placeholder="cth: staf_keuangan" required pattern="^[a-zA-Z0-9_]+$" title="Hanya huruf, angka, dan underscore">
                <div class="form-text">Digunakan di internal sistem. Tanpa spasi.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama / Label Role <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" placeholder="cth: Staf Keuangan" required>
                <div class="form-text">Nama yang akan ditampilkan di UI.</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Role</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
