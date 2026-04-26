<?php
$pageTitle = 'Buat Pengajuan Judul';
require BASE_PATH . '/views/layouts/header.php';

$bidangList = ['Kecerdasan Buatan', 'Jaringan Komputer', 'Rekayasa Perangkat Lunak',
               'Sistem Informasi', 'Basis Data', 'Keamanan Sistem', 'Mobile Computing',
               'Computer Vision', 'Internet of Things', 'Lainnya'];
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="?page=pengajuan">Pengajuan</a></li>
        <li class="breadcrumb-item active">Buat Baru</li>
    </ol></nav>
    <h1><i class="bi bi-plus-circle me-2 text-primary"></i>Buat Pengajuan Judul Skripsi</h1>
    <p>Isi formulir di bawah dengan lengkap dan benar.</p>
</div>

<?php renderFlash(); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Formulir Pengajuan
            </div>
            <div class="card-body p-4">
                <form action="?page=pengajuan.store" method="POST" enctype="multipart/form-data"
                      novalidate id="formPengajuan">
                    <?php csrfField(); ?>

                    <!-- Judul -->
                    <div class="mb-4">
                        <label for="judul" class="form-label">
                            Judul Skripsi <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="judul" name="judul"
                               value="<?= e(post('judul')) ?>"
                               placeholder="Contoh: Sistem Rekomendasi Buku Berbasis Collaborative Filtering"
                               required minlength="10" maxlength="300">
                        <div class="form-text">Minimal 10 karakter, maksimal 300 karakter.</div>
                    </div>

                    <!-- Bidang -->
                    <div class="mb-4">
                        <label for="bidang" class="form-label">
                            Bidang / Topik <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="bidang" name="bidang" required>
                            <option value="">-- Pilih Bidang --</option>
                            <?php foreach ($bidangList as $b): ?>
                            <option value="<?= e($b) ?>" <?= post('bidang') === $b ? 'selected' : '' ?>>
                                <?= e($b) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label class="form-label">
                            Deskripsi / Latar Belakang <span class="text-danger">*</span>
                        </label>
                        <div class="editor-container shadow-sm rounded-3">
                            <div class="editor" id="quill_editor" data-input="deskripsi" style="min-height: 300px;">
                                <?= post('deskripsi', "<h3>1. LATAR BELAKANG</h3><p>(Jelaskan fenomena/masalah yang mendasari penelitian Anda)</p><h3>2. RUMUSAN MASALAH</h3><p>(Apa masalah utama yang ingin diselesaikan?)</p><h3>3. TUJUAN PENELITIAN</h3><p>(Apa yang ingin dicapai dari penelitian ini?)</p><h3>4. METODOLOGI</h3><p>(Metode/Algoritma apa yang digunakan? Bagaimana tahapannya?)</p>") ?>
                            </div>
                        </div>
                        <input type="hidden" name="deskripsi" id="deskripsi" value="<?= e(post('deskripsi')) ?>">
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Gunakan formatting di atas untuk memperjelas poin-poin proposal Anda.
                        </div>
                    </div>

                    <!-- Periode -->
                    <?php if ($periode): ?>
                    <div class="mb-4">
                        <label class="form-label">Periode Pengajuan</label>
                        <input type="hidden" name="periode_id" value="<?= $periode['id'] ?>">
                        <div class="form-control bg-light">
                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                            <strong><?= e($periode['nama']) ?></strong>
                            <span class="text-muted ms-2 small">
                                (<?= formatTanggal($periode['tanggal_mulai']) ?> –
                                <?= formatTanggal($periode['tanggal_selesai']) ?>)
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Usulan Pembimbing -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="pembimbing_1_id" class="form-label">Usulan Pembimbing Utama</label>
                            <select class="form-select" id="pembimbing_1_id" name="pembimbing_1_id">
                                <option value="">-- Pilih Pembimbing 1 --</option>
                                <?php foreach ($listDosen as $d): 
                                    if (strcasecmp($d['jabatan_akademik'] ?? '', 'Asisten Ahli') === 0) continue; 
                                ?>
                                <option value="<?= $d['id'] ?>" <?= post('pembimbing_1_id') == $d['id'] ? 'selected' : '' ?>>
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
                                <option value="<?= $d['id'] ?>" <?= post('pembimbing_2_id') == $d['id'] ? 'selected' : '' ?>>
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

                    <!-- Upload Proposal -->
                    <div class="mb-4">
                        <label class="form-label">
                            File Ringkasan Proposal (PDF)
                            <span class="text-muted small fw-normal">— opsional saat draft</span>
                        </label>
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-label">
                                <i class="bi bi-cloud-arrow-up d-block mb-2"></i>
                                <strong>Klik atau seret file PDF ke sini</strong>
                                <br>
                                <small class="text-muted">Maksimal 5MB, hanya format PDF</small>
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
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Simpan sebagai Draft
                        </button>
                        <a href="?page=pengajuan" class="btn btn-outline-secondary">
                            <i class="bi bi-x me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Box -->
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="fw-semibold"><i class="bi bi-info-circle text-primary me-2"></i>Alur Pengajuan</h6>
                <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                    <?php
                    $steps = [
                        ['Draft', 'secondary'], ['Diajukan', 'info'], ['Verifikasi Staff', 'primary'],
                        ['Review Judul', 'warning'], ['Review Proposal', 'danger'], ['Selesai', 'success']
                    ];
                    foreach ($steps as $i => $s): ?>
                    <span class="badge bg-<?= $s[1] ?> px-3 py-2"><?= $s[0] ?></span>
                    <?php if ($i < count($steps)-1): ?><i class="bi bi-arrow-right text-muted"></i><?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
