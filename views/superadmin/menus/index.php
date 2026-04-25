<?php
$pageTitle = 'Manajemen Menu Akses';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Manajemen Menu</li>
        </ol></nav>
        <h1><i class="bi bi-list-nested me-2 text-primary"></i>Manajemen Menu Sidebar</h1>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-1"></i> Tambah Menu
    </button>
</div>

<?php renderFlash(); ?>

<div class="alert alert-info small">
    <i class="bi bi-info-circle me-1"></i> Halaman ini mengatur daftar menu (Page Route) yang akan digunakan di dalam sistem. Hak akses (siapa yang bisa melihat menu ini) diatur di halaman <a href="?page=superadmin.roles" class="alert-link">Manajemen Role</a>.
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-sititi mb-0">
            <thead>
                <tr>
                    <th style="width:80px">Urutan</th>
                    <th>Judul Menu</th>
                    <th>Kode Page / URL</th>
                    <th>Icon Class</th>
                    <th style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data menu.</td></tr>
                <?php else: ?>
                <?php foreach ($list as $m): ?>
                <tr>
                    <td class="text-center fw-bold text-muted"><?= $m['urutan'] ?></td>
                    <td class="fw-semibold">
                        <i class="<?= e($m['icon']) ?> me-2 text-primary"></i><?= e($m['judul']) ?>
                    </td>
                    <td><code><?= e($m['kode_page']) ?></code></td>
                    <td class="small text-muted"><?= e($m['icon']) ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $m['id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="?page=superadmin.menus.delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Yakin ingin menghapus menu ini? Ini dapat menyebabkan halaman tidak bisa diakses via sidebar.">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $m['id'] ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="?page=superadmin.menus.store" method="POST">
                        <?php csrfField(); ?>
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Menu</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Kode Page (Routing) <span class="text-danger">*</span></label>
                                <input type="text" name="kode_page" class="form-control" value="<?= e($m['kode_page']) ?>" required readonly style="background:#f3f4f6">
                                <div class="form-text">Kode page tidak dapat diubah karena menjadi Primary Key referensi. Hapus dan buat baru jika ingin mengganti.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Judul Tampilan <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" value="<?= e($m['judul']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Icon Class (Bootstrap Icons)</label>
                                <input type="text" name="icon" class="form-control" value="<?= e($m['icon']) ?>" placeholder="bi bi-star">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Urutan Tampil (Sort Order)</label>
                                <input type="number" name="urutan" class="form-control" value="<?= $m['urutan'] ?>">
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
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="?page=superadmin.menus.store" method="POST">
        <?php csrfField(); ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Menu Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Kode Page (Routing) <span class="text-danger">*</span></label>
                <input type="text" name="kode_page" class="form-control" placeholder="cth: admin.mahasiswa" required>
                <div class="form-text">Nilai variabel <code>?page=</code> yang akan diakses.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Judul Tampilan <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control" placeholder="cth: Data Mahasiswa" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Icon Class (Bootstrap Icons)</label>
                <input type="text" name="icon" class="form-control" placeholder="cth: bi bi-people">
                <div class="form-text">Referensi icon: <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Urutan Tampil (Sort Order)</label>
                <input type="number" name="urutan" class="form-control" value="99">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Menu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
