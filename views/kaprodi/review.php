<?php
$pageTitle = 'Review Pengajuan — Kaprodi';
require BASE_PATH . '/views/layouts/header.php';
?>
<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="?page=kaprodi">Review</a></li>
        <li class="breadcrumb-item active">Detail Review</li>
    </ol></nav>
    <h1><i class="bi bi-eye me-2 text-primary"></i>Review Pengajuan Judul</h1>
</div>
<?php renderFlash(); ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-person me-2 text-primary"></i>Data Pengajuan</span>
                <?= statusBadge($pengajuan['status']) ?>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-3">
                    <tr><td class="fw-semibold text-muted" style="width:150px">Mahasiswa</td>
                        <td><?= e($pengajuan['nama_mahasiswa']) ?> — <code><?= e($pengajuan['nim']) ?></code></td></tr>
                    <tr><td class="fw-semibold text-muted">Prodi</td>
                        <td><?= e($pengajuan['nama_prodi'] ?? '-') ?></td></tr>
                    <tr><td class="fw-semibold text-muted">Bidang</td>
                        <td><span class="badge bg-secondary"><?= e($pengajuan['bidang']) ?></span></td></tr>
                </table>
                <h6 class="fw-bold mb-2 text-muted small">JUDUL</h6>
                <p class="fw-semibold fs-6 text-primary mb-3"><?= e($pengajuan['judul']) ?></p>
                <h6 class="fw-bold mb-2 text-muted small">DESKRIPSI</h6>
                <div class="p-3 rounded-3 mb-3" style="background:#f9fafb;white-space:pre-wrap;font-size:.9rem;line-height:1.7"><?= e($pengajuan['deskripsi']) ?></div>
                <?php if ($pengajuan['file_proposal']): ?>
                <a href="<?= UPLOAD_URL ?>/<?= e($pengajuan['file_proposal']) ?>" target="_blank"
                   class="btn btn-outline-danger btn-sm mb-3">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Proposal PDF
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php if (in_array($pengajuan['status'], ['diverifikasi', 'direview'])): ?>
        <div class="card border-primary">
            <div class="card-header bg-primary text-white"><i class="bi bi-check2-circle me-2"></i>Keputusan Kaprodi</div>
            <div class="card-body">
                <form action="?page=kaprodi.proses" method="POST">
                    <?php csrfField(); ?>
                    <input type="hidden" name="pengajuan_id" value="<?= $pengajuan['id'] ?>">
                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-semibold">Catatan / Komentar</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="4"
                                  placeholder="Wajib diisi jika Tolak atau Revisi..."></textarea>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" name="aksi" value="setujui" class="btn btn-success px-4"
                                data-confirm="Setujui dan teruskan ke Dewan?">
                            <i class="bi bi-patch-check me-2"></i>Setujui
                        </button>
                        <button type="submit" name="aksi" value="revisi" class="btn btn-warning text-white"
                                data-confirm="Kembalikan untuk revisi?">
                            <i class="bi bi-pencil me-2"></i>Revisi
                        </button>
                        <button type="submit" name="aksi" value="tolak" class="btn btn-danger"
                                data-confirm="Tolak pengajuan ini?">
                            <i class="bi bi-x-circle me-2"></i>Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-secondary">
            <i class="bi bi-info-circle me-2"></i>Sudah diputuskan: <?= statusBadge($pengajuan['status']) ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-activity me-2 text-primary"></i>Riwayat Status</div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($logs as $log):
                        $dc = match($log['status_baru']){
                            'disetujui','pembimbing_ditentukan'=>'success',
                            'ditolak'=>'danger','revisi'=>'warning',default=>''
                        }; ?>
                    <div class="timeline-item">
                        <div class="timeline-dot <?= $dc ?>"></div>
                        <div class="timeline-content">
                            <?= statusBadge($log['status_baru']) ?>
                            <?php if ($log['catatan']): ?><div class="small text-secondary mt-1"><?= e($log['catatan']) ?></div><?php endif; ?>
                            <div class="timeline-meta mt-1"><?= e($log['nama_user']) ?> · <?= formatTanggal($log['created_at'], true) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
