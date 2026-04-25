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
                    <th>Role</th>
                    <th style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data dosen.</td>
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
                    <td>
                        <?php if (!empty($r['user_roles'])): ?>
                            <?php foreach ($r['user_roles'] as $role): ?>
                                <span class="badge border text-dark bg-light"><?= e(ucfirst($role)) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="badge border text-dark bg-light"><?= e(ucfirst($r['user_role'] ?? 'No Acc')) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id'] ?>" title="Edit Data">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="?page=admin.dosen.delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Yakin ingin menghapus data dosen ini beserta akun loginnya?" title="Hapus Data">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal Edit per Row -->
                <div class="modal fade" id="editModal<?= $r['id'] ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form action="?page=admin.dosen.update" method="POST">
                        <?php csrfField(); ?>
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Dosen: <?= e($r['nama']) ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" value="<?= e($r['nama']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NIDN <span class="text-danger">*</span></label>
                                    <input type="text" name="nidn" class="form-control" value="<?= e($r['nidn']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email (@uniku.ac.id) <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="<?= e($r['email'] ?? '') ?>" required pattern=".+@uniku\.ac\.id$" title="Email harus menggunakan domain @uniku.ac.id">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password Login <span class="text-muted fw-normal">(Opsional - isi untuk mengubah)</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jabatan Akademik</label>
                                    <select name="jabatan_akademik" class="form-select">
                                        <option value="">Pilih Jabatan...</option>
                                        <?php foreach ($dosenModel->getJabatanAkademikOptions() as $key => $val): ?>
                                            <option value="<?= $key ?>" <?= ($r['jabatan_akademik'] ?? '') == $key ? 'selected' : '' ?>><?= $val ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Bidang Keahlian</label>
                                    <select name="bidang_keahlian" class="form-select">
                                        <option value="">Pilih Bidang Keahlian...</option>
                                        <?php foreach ($dosenModel->getBidangKeahlianOptions() as $key => $val): ?>
                                            <option value="<?= $key ?>" <?= ($r['bidang_keahlian'] ?? '') == $key ? 'selected' : '' ?>><?= $val ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. Handphone</label>
                                    <input type="text" name="no_hp" class="form-control" value="<?= e($r['no_hp'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pendidikan Terakhir</label>
                                    <select name="pendidikan" class="form-select">
                                        <option value="">Pilih...</option>
                                        <option value="S2" <?= ($r['pendidikan'] ?? '') === 'S2' ? 'selected' : '' ?>>S2</option>
                                        <option value="S3" <?= ($r['pendidikan'] ?? '') === 'S3' ? 'selected' : '' ?>>S3</option>
                                    </select>
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
                                <div class="col-md-12">
                                    <label class="form-label">Role User <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <?php 
                                            $currentRoles = $r['user_roles'] ?? [$r['user_role']];
                                            $availableRoles = [
                                                'dosen'   => 'Dosen (Pembimbing)',
                                                'dewan'   => 'Dewan (Reviewer)',
                                                'kaprodi' => 'Kaprodi',
                                                'staff'   => 'Staff'
                                            ];
                                            foreach ($availableRoles as $code => $label):
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $code ?>" id="role_<?= $code ?>_<?= $r['id'] ?>" <?= in_array($code, $currentRoles) ? 'checked' : '' ?>>
                                            <label class="form-check-input-label" for="role_<?= $code ?>_<?= $r['id'] ?>"><?= $label ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
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
      <form action="?page=admin.dosen.import" method="POST" enctype="multipart/form-data">
        <?php csrfField(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Data Dosen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info small">
                <i class="bi bi-info-circle me-1"></i> Upload banyak data dosen sekaligus menggunakan file **Excel (.xlsx)** atau **CSV**. 
                <strong>Format:</strong> nama, email, nidn, kode_prodi, bidang_keahlian, no_hp, jabatan_akademik, pendidikan, role, password.
                <br><small>*Jika password dikosongkan, otomatis menggunakan NIDN.</small>
            </div>
            <div class="mb-3">
                <a href="<?= APP_URL ?>/assets/templates/template_dosen.xlsx" class="btn btn-outline-success btn-sm me-2" download>
                    <i class="bi bi-file-earmark-excel me-1"></i> Download Template Excel (.xlsx)
                </a>
                <a href="<?= APP_URL ?>/assets/templates/template_dosen.csv" class="btn btn-outline-secondary btn-sm" download>
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download Template CSV
                </a>
            </div>
            <div class="mb-3">
                <label for="file_csv_dosen" class="form-label fw-bold">Upload File Excel/CSV</label>
                <input class="form-control" type="file" id="file_csv_dosen" name="file_csv" accept=".csv, .xlsx, .xls" required>
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
      <form action="?page=admin.dosen.store" method="POST">
        <?php csrfField(); ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Dosen Manual</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap (beserta Gelar) <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIDN <span class="text-danger">*</span></label>
                    <input type="text" name="nidn" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email (@uniku.ac.id) <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required pattern=".+@uniku\.ac\.id$" title="Email harus menggunakan domain @uniku.ac.id">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password Login <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan Akademik</label>
                    <select name="jabatan_akademik" class="form-select">
                        <option value="">Pilih Jabatan...</option>
                        <?php foreach ($dosenModel->getJabatanAkademikOptions() as $key => $val): ?>
                            <option value="<?= $key ?>"><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bidang Keahlian</label>
                    <select name="bidang_keahlian" class="form-select">
                        <option value="">Pilih Bidang Keahlian...</option>
                        <?php foreach ($dosenModel->getBidangKeahlianOptions() as $key => $val): ?>
                            <option value="<?= $key ?>"><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Handphone</label>
                    <input type="text" name="no_hp" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pendidikan Terakhir</label>
                    <select name="pendidikan" class="form-select">
                        <option value="">Pilih...</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
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
                <div class="col-md-12">
                    <label class="form-label">Role User <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="dosen" id="role_new_dosen" checked>
                            <label class="form-check-input-label" for="role_new_dosen">Dosen (Pembimbing)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="dewan" id="role_new_dewan">
                            <label class="form-check-input-label" for="role_new_dewan">Dewan (Reviewer)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="kaprodi" id="role_new_kaprodi">
                            <label class="form-check-input-label" for="role_new_kaprodi">Kaprodi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="staff" id="role_new_staff">
                            <label class="form-check-input-label" for="role_new_staff">Staff</label>
                        </div>
                    </div>
                    <div class="form-text small text-info">
                        <i class="bi bi-info-circle me-1"></i> Dosen dapat memiliki lebih dari satu peran.
                    </div>
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

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
