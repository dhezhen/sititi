<?php
$pageTitle = 'Edit Pengajuan';
require BASE_PATH . '/views/layouts/header.php';

$bidangList = ['Kecerdasan Buatan', 'Jaringan Komputer', 'Rekayasa Perangkat Lunak',
               'Sistem Informasi', 'Basis Data', 'Keamanan Sistem', 'Mobile Computing',
               'Computer Vision', 'Internet of Things', 'Lainnya'];
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="?page=pengajuan">Pengajuan</a></li>
        <li class="breadcrumb-item active">Edit Pengajuan</li>
    </ol></nav>
    <h1><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Pengajuan</h1>
    <p>Status saat ini: <?= statusBadge($pengajuan['status']) ?></p>
</div>

<?php renderFlash(); ?>

<?php if ($pengajuan['status'] === 'revisi' && $pengajuan['catatan_terakhir']): ?>
<div class="alert alert-warning d-flex gap-3 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
    <div>
        <strong>Catatan Revisi:</strong><br>
        <?= e($pengajuan['catatan_terakhir']) ?>
    </div>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2 text-warning"></i>Formulir Edit Pengajuan
            </div>
            <div class="card-body p-4">
                <form action="?page=pengajuan.update" method="POST" enctype="multipart/form-data" novalidate>
                    <?php csrfField(); ?>
                    <input type="hidden" name="id" value="<?= $pengajuan['id'] ?>">

                    <div class="mb-4">
                        <label for="judul" class="form-label">Judul Skripsi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="judul" name="judul"
                               value="<?= e($pengajuan['judul']) ?>"
                               required minlength="10" maxlength="300">
                    </div>

                    <div class="mb-4">
                        <label for="bidang" class="form-label">Bidang / Topik <span class="text-danger">*</span></label>
                        <select class="form-select" id="bidang" name="bidang" required>
                            <option value="">-- Pilih Bidang --</option>
                            <?php foreach ($bidangList as $b): ?>
                            <option value="<?= e($b) ?>" <?= $pengajuan['bidang'] === $b ? 'selected' : '' ?>>
                                <?= e($b) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Deskripsi / Latar Belakang <span class="text-danger">*</span></label>
                        <div class="editor-container shadow-sm rounded-3">
                            <div class="editor" id="quill_editor" data-input="deskripsi" style="min-height: 300px;">
                                <?= $pengajuan['deskripsi'] ?>
                            </div>
                        </div>
                        <input type="hidden" name="deskripsi" id="deskripsi" value="<?= e($pengajuan['deskripsi']) ?>">
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Gunakan formatting di atas untuk memperjelas poin-poin proposal Anda.
                        </div>
                    </div>

                    <!-- Usulan Pembimbing -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="pembimbing_1_id" class="form-label">Usulan Pembimbing Utama</label>
                            <select class="form-select" id="pembimbing_1_id" name="pembimbing_1_id">
                                <option value="">-- Pilih Pembimbing 1 --</option>
                                <?php foreach ($listDosen as $d): 
                                    if (strcasecmp($d['jabatan_akademik'] ?? '', 'Asisten Ahli') === 0) continue; 
                                ?>
                                <option value="<?= $d['id'] ?>" <?= $pengajuan['pembimbing_1_id'] == $d['id'] ? 'selected' : '' ?>>
                                    <?= e($d['nama']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pembimbing_2_id" class="form-label">Usulan Pembimbing Pendamping</label>
                            <select class="form-select" id="pembimbing_2_id" name="pembimbing_2_id">
                                <option value="">-- Pilih Pembimbing 2 --</option>
                                <?php foreach ($listDosen as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $pengajuan['pembimbing_2_id'] == $d['id'] ? 'selected' : '' ?>>
                                    <?= e($d['nama']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="form-text text-info">
                                <i class="bi bi-info-circle me-1"></i> 
                                Dosen dengan jabatan <strong>Asisten Ahli</strong> hanya dapat menjadi Pembimbing 2.
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">File Ringkasan Proposal (PDF)</label>
                        <?php if ($pengajuan['file_proposal']): ?>
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-2"
                             style="background:#f5f3ff;border:1px solid #ede9fe">
                            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small"><?= e($pengajuan['file_proposal']) ?></div>
                                <a href="<?= UPLOAD_URL ?>/<?= e($pengajuan['file_proposal']) ?>"
                                   target="_blank" class="small text-primary">
                                    <i class="bi bi-eye me-1"></i>Lihat File Saat Ini
                                </a>
                            </div>
                        </div>
                        <div class="form-text mb-2">Upload baru jika ingin mengganti file lama.</div>
                        <?php endif; ?>
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-label">
                                <i class="bi bi-cloud-arrow-up d-block mb-2"></i>
                                <strong>Klik atau seret file PDF</strong>
                                <br><small class="text-muted">Maks 5MB</small>
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="<?= APP_URL ?>/assets/templates/template_ringkasan_proposal.docx" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-file-earmark-word me-1"></i> Unduh Template Ringkasan Proposal (.docx)
                            </a>
                        </div>
                        <input type="file" id="file_proposal" name="file_proposal"
                               class="d-none" accept=".pdf,application/pdf">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4 text-white fw-semibold">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                        <a href="?page=pengajuan.show&id=<?= $pengajuan['id'] ?>"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-x me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
