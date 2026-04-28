<?php
$pageTitle = 'Detail Bimbingan — ' . e($pengajuan['nama_mahasiswa'] ?? 'Mahasiswa');
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="?page=dosen.bimbingan">Bimbingan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
    <h1><i class="bi bi-chat-right-text me-2 text-primary"></i>Detail Bimbingan</h1>
</div>

<?php renderFlash(); ?>

<div class="row g-4">
    <!-- Info Mahasiswa & Judul -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="user-avatar" style="width:60px; height:60px; font-size:1.5rem; overflow:hidden;">
                        <?php if (!empty($pengajuan['foto_mahasiswa'])): ?>
                            <img src="<?= AVATAR_URL ?>/<?= e($pengajuan['foto_mahasiswa']) ?>" alt="Mhs" style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <?= strtoupper(substr($pengajuan['nama_mahasiswa'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold"><?= e($pengajuan['nama_mahasiswa']) ?></h5>
                        <div class="text-muted small"><?= e($pengajuan['nim']) ?> — <?= e($pengajuan['nama_prodi']) ?></div>
                    </div>
                </div>
                
                <hr>
                
                <label class="small text-muted fw-bold text-uppercase mb-1">Judul Skripsi</label>
                <p class="fw-semibold text-dark mb-3"><?= e($pengajuan['judul']) ?></p>

                <label class="small text-muted fw-bold text-uppercase mb-1">Bidang / Konsentrasi</label>
                <div class="mb-3">
                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><?= e($pengajuan['bidang']) ?></span>
                </div>

                <label class="small text-muted fw-bold text-uppercase mb-1">Deskripsi Singkat</label>
                <div class="bg-light p-3 rounded small text-muted border-start border-primary border-4 mb-4">
                    <?= nl2br(e($pengajuan['deskripsi'])) ?>
                </div>

                <label class="small text-muted fw-bold text-uppercase mb-1">Berkas Proposal</label>
                <div class="mb-4">
                    <?php if (!empty($pengajuan['file_proposal'])): ?>
                        <a href="<?= UPLOAD_URL ?>/<?= e($pengajuan['file_proposal']) ?>" class="btn btn-outline-danger w-100 py-2" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Lihat / Download Proposal
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning py-2 mb-0 small">
                            <i class="bi bi-exclamation-triangle me-1"></i> File proposal tidak tersedia.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Status Saat Ini:</span>
                    <?= statusBadge($pengajuan['status']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Catatan Bimbingan -->
    <div class="col-lg-7">
        <!-- Form Tambah Catatan -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-plus-lg me-2 text-primary"></i>Tambah Masukan / Saran</h6>
                <form action="?page=dosen.bimbingan.catatan" method="POST">
                    <?php csrfField(); ?>
                    <input type="hidden" name="pengajuan_id" value="<?= $pengajuan['id'] ?>">
                    
                    <div class="mb-3">
                        <textarea name="catatan" class="form-control" rows="4" placeholder="Tuliskan masukan atau saran perbaikan untuk mahasiswa..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send-fill me-2"></i>Kirim Masukan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Riwayat Masukan -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Masukan</h6>
            </div>
            <div class="card-body">
                <?php if (empty($catatan)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-chat-square-dots fs-1 d-block mb-2 opacity-25"></i>
                    <p class="small mb-0">Belum ada masukan yang diberikan.</p>
                </div>
                <?php else: ?>
                <div class="timeline">
                    <?php foreach ($catatan as $c): ?>
                    <div class="timeline-item mb-4">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content p-3 border-0 bg-light-subtle shadow-none border-start border-primary border-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-primary"><?= e($c['nama_dosen']) ?></span>
                                <span class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-calendar3 me-1"></i><?= formatTanggal($c['created_at'], true) ?></span>
                            </div>
                            <div class="text-dark small">
                                <?= nl2br(e($c['catatan'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
