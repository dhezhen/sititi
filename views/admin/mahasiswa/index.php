<?php
$pageTitle = 'Data Mahasiswa';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Data Mahasiswa</li>
        </ol></nav>
        <h1><i class="bi bi-people me-2 text-primary"></i>Data Mahasiswa</h1>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Manual
        </button>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel me-1"></i> Import Data
        </button>
    </div>
</div>

<?php renderFlash(); ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table me-2"></i>Daftar Mahasiswa</span>
        <form action="" method="GET" class="d-flex gap-2">
            <input type="hidden" name="page" value="admin.mahasiswa">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIM / Nama..." value="<?= e($search) ?>">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-sititi mb-0">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Program Studi</th>
                    <th>Angkatan</th>
                    <th>Status Akun</th>
                    <th style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data mahasiswa.</td>
                </tr>
                <?php else: ?>
                <?php $no = ($page - 1) * $perPage + 1; foreach ($list as $r): ?>
                <tr>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td class="fw-semibold"><?= e($r['nim']) ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($r['nama']) ?></div>
                        <div class="small text-muted"><?= e($r['email']) ?></div>
                    </td>
                    <td><?= e($r['nama_prodi'] ?? '-') ?></td>
                    <td><?= e($r['angkatan']) ?></td>
                    <td>
                        <?php if ($r['is_active']): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id'] ?>" title="Edit Data">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="?page=admin.mahasiswa.delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Yakin ingin menghapus data mahasiswa ini secara permanen beserta akun loginnya?" title="Hapus Data">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $r['id'] ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form action="?page=admin.mahasiswa.update" method="POST">
                        <?php csrfField(); ?>
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Mahasiswa</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" value="<?= e($r['nama']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NIM <span class="text-danger">*</span></label>
                                    <input type="text" name="nim" class="form-control" value="<?= e($r['nim']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email (@uniku.ac.id) <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="<?= e($r['email']) ?>" required pattern=".+@uniku\.ac\.id$" title="Email harus menggunakan domain @uniku.ac.id">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password Login <span class="text-muted fw-normal">(Opsional)</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="Isi untuk mengubah password">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                                    <input type="number" name="angkatan" class="form-control" value="<?= e($r['angkatan']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                                    <input type="number" name="semester" class="form-control" value="<?= e($r['semester']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. Handphone</label>
                                    <input type="text" name="no_hp" class="form-control" value="<?= e($r['no_hp'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Program Studi</label>
                                    <select name="prodi_id" class="form-select">
                                        <option value="">-- Pilih Program Studi --</option>
                                        <?php foreach ($prodiList as $prodi): ?>
                                        <option value="<?= $prodi['id'] ?>" <?= ($r['prodi_id'] ?? '') == $prodi['id'] ? 'selected' : '' ?>>
                                            <?= e($prodi['nama']) ?> (<?= e($prodi['kode']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="2"><?= e($r['alamat'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
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
                    <a class="page-link" href="?page=admin.mahasiswa&p=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
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
      <form action="?page=admin.mahasiswa.import" method="POST" enctype="multipart/form-data">
        <?php csrfField(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Data Mahasiswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info small">
                <i class="bi bi-info-circle me-1"></i> Upload banyak data mahasiswa sekaligus menggunakan file **Excel (.xlsx)** atau **CSV**. 
                Pastikan format sesuai dengan template yang disediakan.
            </div>
            <div class="mb-3">
                <a href="<?= APP_URL ?>/assets/templates/template_mahasiswa.xlsx" class="btn btn-outline-success btn-sm me-2" download>
                    <i class="bi bi-file-earmark-excel me-1"></i> Download Template Excel (.xlsx)
                </a>
                <a href="<?= APP_URL ?>/assets/templates/template_mahasiswa.csv" class="btn btn-outline-secondary btn-sm" download>
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download Template CSV
                </a>
            </div>
            <div class="mb-3">
                <label for="file_csv" class="form-label fw-bold">Upload File Excel/CSV</label>
                <input class="form-control" type="file" id="file_csv" name="file_csv" accept=".csv, .xlsx, .xls" required>
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

<!-- Modal Create Manual -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="?page=admin.mahasiswa.store" method="POST">
        <?php csrfField(); ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Mahasiswa Manual</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" id="create_nim" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email (@uniku.ac.id)</label>
                    <input type="email" name="email" class="form-control" required pattern=".+@uniku\.ac\.id$" title="Email harus menggunakan domain @uniku.ac.id">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password Login</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Angkatan</label>
                    <input type="number" name="angkatan" id="create_angkatan" class="form-control" value="<?= date('Y') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Semester</label>
                    <input type="number" name="semester" class="form-control" value="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">No. Handphone</label>
                    <input type="text" name="no_hp" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Program Studi</label>
                    <select name="prodi_id" class="form-select">
                        <option value="">-- Pilih Program Studi --</option>
                        <?php foreach ($prodiList as $prodi): ?>
                        <option value="<?= $prodi['id'] ?>"><?= e($prodi['nama']) ?> (<?= e($prodi['kode']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nimInput = document.getElementById('create_nim');
    const angkatanInput = document.getElementById('create_angkatan');

    if (nimInput && angkatanInput) {
        nimInput.addEventListener('input', function() {
            const nim = this.value;
            if (nim.length >= 4) {
                const year = nim.substring(0, 4);
                if (!isNaN(year)) {
                    angkatanInput.value = year;
                }
            }
        });
    }
});
</script>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
