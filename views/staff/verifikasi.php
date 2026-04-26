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
