<?php
$pageTitle = 'Verifikasi Berkas';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="?page=staff">Verifikasi</a></li>
        <li class="breadcrumb-item active">Detail Verifikasi</li>
    </ol></nav>
    <h1><i class="bi bi-clipboard-check me-2 text-primary"></i>Verifikasi Berkas Pengajuan</h1>
</div>

<?php renderFlash(); ?>

<div class="row g-4">
    <!-- Info Pengajuan -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-person me-2 text-primary"></i>Data Mahasiswa</span>
                <?= statusBadge($pengajuan['status']) ?>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="fw-semibold text-muted" style="width:160px">Nama</td>
                        <td><?= e($pengajuan['nama_mahasiswa']) ?></td></tr>
                    <tr><td class="fw-semibold text-muted">NIM</td>
                        <td><code><?= e($pengajuan['nim']) ?></code></td></tr>
                    <tr><td class="fw-semibold text-muted">Program Studi</td>
                        <td><?= e($pengajuan['nama_prodi'] ?? '-') ?></td></tr>
                    <tr><td class="fw-semibold text-muted">Angkatan</td>
                        <td><?= e($pengajuan['angkatan'] ?? '-') ?></td></tr>
                    <tr><td class="fw-semibold text-muted">Semester</td>
                        <td><?= e($pengajuan['semester'] ?? '-') ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-file-earmark me-2 text-primary"></i>Detail Pengajuan</div>
            <div class="card-body">
                <h6 class="fw-bold mb-1 text-muted small">JUDUL</h6>
                <p class="fw-semibold fs-6 text-primary mb-3"><?= e($pengajuan['judul']) ?></p>

                <h6 class="fw-bold mb-1 text-muted small">BIDANG</h6>
                <p class="mb-3"><span class="badge bg-secondary"><?= e($pengajuan['bidang']) ?></span></p>

                <h6 class="fw-bold mb-1 text-muted small">DESKRIPSI</h6>
                <div class="p-3 rounded-3 mb-3" style="background:#f9fafb;font-size:.9rem;line-height:1.7">
                    <?= (strpos($pengajuan['deskripsi'], '<') !== false) ? $pengajuan['deskripsi'] : nl2br(e($pengajuan['deskripsi'])) ?>
                </div>

                <h6 class="fw-bold mb-1 text-muted small">FILE PROPOSAL</h6>
                <?php if ($pengajuan['file_proposal']): ?>
                <a href="<?= UPLOAD_URL ?>/<?= e($pengajuan['file_proposal']) ?>"
                   target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Buka File Proposal PDF
                </a>
                <?php else: ?>
                <div class="alert alert-danger py-2 mb-0">
                    <i class="bi bi-x-circle me-2"></i>File proposal belum diunggah!
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Verifikasi -->
        <?php if ($pengajuan['status'] === 'diajukan'): ?>
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-check2-circle me-2"></i>Tindakan Verifikasi
            </div>
            <div class="card-body">
                <form action="?page=staff.proses" method="POST">
                    <?php csrfField(); ?>
                    <input type="hidden" name="pengajuan_id" value="<?= $pengajuan['id'] ?>">

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (opsional untuk lengkap, wajib untuk revisi)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"
                                  placeholder="Catatan verifikasi..."></textarea>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" name="aksi" value="lengkap"
                                class="btn btn-success"
                                onclick="return confirm('Tandai berkas LENGKAP dan kirim ke Kaprodi?')">
                            <i class="bi bi-check-circle me-2"></i>Berkas Lengkap → Kirim ke Kaprodi
                        </button>
                        <button type="submit" name="aksi" value="tidak_lengkap"
                                class="btn btn-warning text-white"
                                data-confirm="Kembalikan ke mahasiswa untuk revisi?">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Tidak Lengkap → Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-secondary">
            <i class="bi bi-info-circle me-2"></i>
            Pengajuan ini sudah diproses dengan status <strong><?= statusBadge($pengajuan['status']) ?></strong>.
        </div>
        <?php endif; ?>

        <!-- Hasil Review Dewan (untuk transparansi Prodi) -->
        <?php 
        $d1Log = null; $d2Log = null;
        foreach ($logs as $l) {
            if (isset($pengajuan['dewan_1_id']) && $l['user_id'] == $pengajuan['dewan_1_id']) $d1Log = $l;
            if (isset($pengajuan['dewan_2_id']) && $l['user_id'] == $pengajuan['dewan_2_id']) $d2Log = $l;
        }
        if ($d1Log || $d2Log):
        ?>
        <div class="card mb-4 border-0 shadow-sm border-start border-info border-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-info"></i>Hasil Review Dewan Bimbingan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded bg-light h-100">
                            <div class="fw-bold small mb-1">Reviewer 1</div>
                            <div class="fw-semibold small mb-2 text-primary"><?= e($pengajuan['nama_dewan_1'] ?? '-') ?></div>
                            <?php if ($d1Log): ?>
                                <div class="mb-2"><?= statusBadge($d1Log['status_baru']) ?></div>
                                <div class="bg-white p-2 rounded border small italic">
                                    <?= (strpos($d1Log['catatan'], '<') !== false) ? $d1Log['catatan'] : nl2br(e($d1Log['catatan'] ?: 'Tidak ada catatan.')) ?>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border">Belum Review</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded bg-light h-100">
                            <div class="fw-bold small mb-1">Reviewer 2</div>
                            <div class="fw-semibold small mb-2 text-primary"><?= e($pengajuan['nama_dewan_2'] ?? '-') ?></div>
                            <?php if ($d2Log): ?>
                                <div class="mb-2"><?= statusBadge($d2Log['status_baru']) ?></div>
                                <div class="bg-white p-2 rounded border small italic">
                                    <?= (strpos($d2Log['catatan'], '<') !== false) ? $d2Log['catatan'] : nl2br(e($d2Log['catatan'] ?: 'Tidak ada catatan.')) ?>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border">Belum Review</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Timeline -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-activity me-2 text-primary"></i>Riwayat Status</div>
            <div class="card-body">
                <?php if (empty($logs)): ?>
                <p class="text-muted text-center small py-3">Belum ada riwayat.</p>
                <?php else: ?>
                <div class="timeline">
                    <?php foreach ($logs as $log):
                        $dc = match($log['status_baru']) {
                            'disetujui','pembimbing_ditentukan' => 'success',
                            'ditolak' => 'danger', 'revisi' => 'warning', default => ''
                        }; ?>
                    <div class="timeline-item">
                        <div class="timeline-dot <?= $dc ?>"></div>
                        <div class="timeline-content">
                            <?= statusBadge($log['status_baru']) ?>
                            <?php if ($log['catatan']): ?>
                            <div class="small text-secondary mt-1"><?= e($log['catatan']) ?></div>
                            <?php endif; ?>
                            <div class="timeline-meta mt-1">
                                <?= e($log['nama_user']) ?> · <?= formatTanggal($log['created_at'], true) ?>
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
