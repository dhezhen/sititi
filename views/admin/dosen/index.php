<?php
$pageTitle = 'Data Dosen';
$filterParams = array_filter([
    'search' => $search,
    'prodi_id' => $prodiId,
    'jabatan' => $jabatan,
    'status' => $status,
], static fn($value) => $value !== '' && $value !== null);
$canBulkManage = in_array(Auth::role(), [ROLE_STAFF, ROLE_KAPRODI, ROLE_SUPERADMIN], true);
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
        <span><i class="bi bi-table me-2" aria-hidden="true"></i>Daftar Dosen</span>
        <span class="small text-muted"><?= number_format((int)$total) ?> dosen</span>
    </div>
    <div class="card-body border-bottom">
        <form action="?page=admin.dosen" method="GET" aria-label="Filter daftar dosen">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="lecturerSearch" class="form-label">Pencarian</label>
                    <input type="search" id="lecturerSearch" name="search" class="form-control" placeholder="Nama, NIDN, email, atau no. HP" value="<?= e($search) ?>">
                </div>
                <div class="col-12 col-md-2">
                    <label for="lecturerProdi" class="form-label">Program studi</label>
                    <select id="lecturerProdi" name="prodi_id" class="form-select">
                        <option value="">Semua prodi</option>
                        <?php foreach ($prodiList as $prodi): ?>
                        <option value="<?= (int)$prodi['id'] ?>" <?= $prodiId === (int)$prodi['id'] ? 'selected' : '' ?>><?= e($prodi['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="lecturerRank" class="form-label">Jabatan akademik</label>
                    <select id="lecturerRank" name="jabatan" class="form-select">
                        <option value="">Semua jabatan</option>
                        <option value="__none" <?= $jabatan === '__none' ? 'selected' : '' ?>>Belum diisi</option>
                        <?php foreach ($jabatanList as $rank): ?>
                        <option value="<?= e($rank) ?>" <?= $jabatan === $rank ? 'selected' : '' ?>><?= e(ucwords(strtolower($rank))) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="lecturerStatus" class="form-label">Status dosen</label>
                    <select id="lecturerStatus" name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-6 col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary" aria-label="Terapkan filter">
                        <i class="bi bi-search" aria-hidden="true"></i><span class="d-md-none ms-2">Filter</span>
                    </button>
                </div>
            </div>
            <?php if (!empty($filterParams)): ?>
            <div class="mt-2"><a href="?page=admin.dosen">Hapus filter</a></div>
            <?php endif; ?>
        </form>
    </div>
    <?php if ($canBulkManage): ?>
    <form id="bulkDosenForm" action="?page=admin.dosen.bulk" method="POST">
        <?php csrfField(); ?>
    </form>
    <div class="card-body border-bottom d-flex flex-wrap align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-primary" id="bulkEditButton" data-bs-toggle="modal" data-bs-target="#bulkEditModal" form="bulkDosenForm" disabled>
            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Edit massal
        </button>
        <button type="submit" class="btn btn-sm btn-outline-danger" id="bulkDeleteButton" name="bulk_action" value="delete" form="bulkDosenForm" disabled>
            <i class="bi bi-trash me-1" aria-hidden="true"></i>Hapus terpilih
        </button>
        <span id="bulkSelectionCount" class="small text-muted" aria-live="polite">Pilih dosen pada halaman ini.</span>
        <span class="small text-muted">Penghapusan juga menghapus akun terkait dan relasi pembimbing.</span>
    </div>
    <?php endif; ?>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-sititi mb-0">
            <thead>
                <tr>
                    <?php if ($canBulkManage): ?>
                    <th scope="col" style="width:44px"><input type="checkbox" class="form-check-input" id="selectAllDosen" aria-label="Pilih semua dosen pada halaman ini"></th>
                    <?php endif; ?>
                    <th style="width:40px">#</th>
                    <th>NIDN</th>
                    <th>Nama Lengkap</th>
                    <th>Program Studi</th>
                    <th>Jabatan Akademik</th>
                    <th>Bidang Keahlian</th>
                    <th>Status Dosen</th>
                    <th>Role</th>
                    <th style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                <tr>
                    <td colspan="<?= $canBulkManage ? 10 : 9 ?>" class="text-center py-4 text-muted">
                        <?= $total > 0 ? 'Tidak ada dosen yang cocok dengan filter.' : 'Belum ada data dosen.' ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php $no = ($page - 1) * $perPage + 1; foreach ($list as $r): ?>
                <tr>
                    <?php if ($canBulkManage): ?>
                    <td><input type="checkbox" class="form-check-input lecturer-row-select" name="ids[]" value="<?= (int)$r['id'] ?>" form="bulkDosenForm" aria-label="Pilih <?= e($r['nama']) ?>"></td>
                    <?php endif; ?>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td class="fw-semibold"><?= e($r['nidn']) ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($r['nama']) ?></div>
                        <?php if ($r['email']): ?>
                        <div class="small text-muted"><?= e($r['email']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= e($r['nama_prodi'] ?? '-') ?></td>
                    <td><?= e($r['jabatan_akademik'] ? ucwords(strtolower($r['jabatan_akademik'])) : '-') ?></td>
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
                                            $currentRoles = is_array($r['user_roles'] ?? null) ? $r['user_roles'] : [];
                                            if (empty($currentRoles) && !empty($r['user_role'])) $currentRoles = [$r['user_role']];
                                            foreach ($roleList as $roleIndex => $roleOption):
                                                $code = (string)$roleOption['kode'];
                                                $roleId = 'role_' . (int)$r['id'] . '_' . (int)$roleIndex;
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   <?= Auth::role() === ROLE_SUPERADMIN ? 'name="roles[]"' : 'disabled' ?> 
                                                   value="<?= e($code) ?>" id="<?= e($roleId) ?>" 
                                                   <?= in_array($code, $currentRoles) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="<?= e($roleId) ?>"><?= e($roleOption['nama']) ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (Auth::role() !== ROLE_SUPERADMIN): ?>
                                        <!-- Hidden input to retain existing roles if not superadmin -->
                                        <?php foreach ($currentRoles as $cr): ?>
                                            <input type="hidden" name="roles[]" value="<?= e($cr) ?>">
                                        <?php endforeach; ?>
                                        <div class="form-text small text-warning"><i class="bi bi-info-circle me-1"></i> Hanya Super Admin yang dapat mengubah role user.</div>
                                    <?php endif; ?>
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
        </div>

        <?php if ($pages > 1): ?>
        <div class="d-flex justify-content-center py-3">
            <nav><ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=admin.dosen&amp;<?= e(http_build_query(array_merge($filterParams, ['p' => $i]))) ?>" aria-label="Halaman <?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul></nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($canBulkManage): ?>
<div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalTitle" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title h5" id="bulkEditModalTitle">Edit dosen terpilih</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">Centang bidang yang ingin diterapkan ke semua dosen terpilih. Bidang tanpa centang tidak berubah.</p>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="updateBulkRank" name="update_jabatan" value="1" form="bulkDosenForm">
          <label class="form-check-label fw-semibold" for="updateBulkRank">Ubah jabatan akademik</label>
        </div>
        <div class="mb-4">
          <label for="bulkRank" class="form-label">Jabatan akademik baru</label>
          <select class="form-select" id="bulkRank" name="bulk_jabatan" form="bulkDosenForm">
            <option value="">Pilih jabatan</option>
            <option value="__clear">Hapus jabatan akademik</option>
            <?php foreach ($jabatanList as $rank): ?>
            <option value="<?= e($rank) ?>"><?= e(ucwords(strtolower($rank))) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="updateBulkStatus" name="update_status" value="1" form="bulkDosenForm">
          <label class="form-check-label fw-semibold" for="updateBulkStatus">Ubah status dosen</label>
        </div>
        <div>
          <label for="bulkStatus" class="form-label">Status baru</label>
          <select class="form-select" id="bulkStatus" name="bulk_status" form="bulkDosenForm">
            <option value="">Pilih status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" name="bulk_action" value="edit" form="bulkDosenForm">Terapkan perubahan</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

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
                            <input class="form-check-input" type="checkbox" <?= Auth::role() === ROLE_SUPERADMIN ? 'name="roles[]"' : 'disabled' ?> value="dosen" id="role_new_dosen" checked>
                            <label class="form-check-input-label" for="role_new_dosen">Dosen (Pembimbing)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" <?= Auth::role() === ROLE_SUPERADMIN ? 'name="roles[]"' : 'disabled' ?> value="dewan" id="role_new_dewan">
                            <label class="form-check-input-label" for="role_new_dewan">Dewan (Reviewer)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" <?= Auth::role() === ROLE_SUPERADMIN ? 'name="roles[]"' : 'disabled' ?> value="kaprodi" id="role_new_kaprodi">
                            <label class="form-check-input-label" for="role_new_kaprodi">Kaprodi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" <?= Auth::role() === ROLE_SUPERADMIN ? 'name="roles[]"' : 'disabled' ?> value="staff" id="role_new_staff">
                            <label class="form-check-input-label" for="role_new_staff">Staff</label>
                        </div>
                    </div>
                    <?php if (Auth::role() !== ROLE_SUPERADMIN): ?>
                        <input type="hidden" name="roles[]" value="dosen">
                        <div class="form-text small text-warning"><i class="bi bi-info-circle me-1"></i> Hanya Super Admin yang dapat mengubah role. Default role adalah Dosen.</div>
                    <?php else: ?>
                        <div class="form-text small text-info"><i class="bi bi-info-circle me-1"></i> Dosen dapat memiliki lebih dari satu peran.</div>
                    <?php endif; ?>
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

<?php if ($canBulkManage): ?>
<script>
(() => {
    const selectionBoxes = Array.from(document.querySelectorAll('.lecturer-row-select'));
    const selectAll = document.getElementById('selectAllDosen');
    const editButton = document.getElementById('bulkEditButton');
    const deleteButton = document.getElementById('bulkDeleteButton');
    const selectionCount = document.getElementById('bulkSelectionCount');
    const bulkForm = document.getElementById('bulkDosenForm');

    const syncSelection = () => {
        const selected = selectionBoxes.filter((box) => box.checked).length;
        editButton.disabled = selected === 0;
        deleteButton.disabled = selected === 0;
        selectionCount.textContent = selected === 0
            ? 'Pilih dosen pada halaman ini.'
            : `${selected} dosen dipilih pada halaman ini.`;
        selectAll.checked = selected > 0 && selected === selectionBoxes.length;
        selectAll.indeterminate = selected > 0 && selected < selectionBoxes.length;
    };

    selectionBoxes.forEach((box) => box.addEventListener('change', syncSelection));
    selectAll.addEventListener('change', () => {
        selectionBoxes.forEach((box) => { box.checked = selectAll.checked; });
        syncSelection();
    });
    bulkForm.addEventListener('submit', (event) => {
        if (event.submitter?.value === 'delete'
            && !window.confirm(`Hapus ${selectionBoxes.filter((box) => box.checked).length} data dosen, akun terkait, dan relasi pembimbing?`)) {
            event.preventDefault();
        }
    });
    syncSelection();
})();
</script>
<?php endif; ?>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
