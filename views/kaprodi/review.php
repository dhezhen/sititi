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
                    <tr><td class="fw-semibold text-muted">Usulan Pembimbing</td>
                        <td>
                            <div class="small">
                                <div>1. <?= e($pengajuan['nama_pembimbing_1'] ?: '-') ?></div>
                                <div>2. <?= e($pengajuan['nama_pembimbing_2'] ?: '-') ?></div>
                            </div>
                        </td></tr>
                    <?php if ($pengajuan['nama_dewan_1']): ?>
                    <tr><td class="fw-semibold text-muted">Dewan Reviewer</td>
                        <td>
                            <div class="small">
                                <div>1. <?= e($pengajuan['nama_dewan_1']) ?></div>
                                <div>2. <?= e($pengajuan['nama_dewan_2'] ?: '-') ?></div>
                            </div>
                        </td></tr>
                    <?php endif; ?>
                </table>
                <h6 class="fw-bold mb-2 text-muted small">JUDUL</h6>
                <p class="fw-semibold fs-6 text-primary mb-3"><?= e($pengajuan['judul']) ?></p>
                <h6 class="fw-bold mb-2 text-muted small">RINGKASAN PROPOSAL</h6>
                <div class="p-3 rounded-3 mb-3" style="background:#f9fafb;font-size:.9rem;line-height:1.7">
                    <?= (strpos($pengajuan['deskripsi'], '<') !== false) ? $pengajuan['deskripsi'] : nl2br(e($pengajuan['deskripsi'])) ?>
                </div>
                <?php if ($pengajuan['file_proposal']): ?>
                <a href="<?= UPLOAD_URL ?>/<?= e($pengajuan['file_proposal']) ?>" target="_blank"
                   class="btn btn-outline-danger btn-sm mb-3">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Ringkasan Proposal PDF
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (in_array($pengajuan['status'], [STATUS_DIVERIFIKASI, STATUS_DIREVIEW_DEWAN])): ?>
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-check2-circle me-2"></i>
                <?= $pengajuan['status'] === STATUS_DIREVIEW_DEWAN ? 'Persetujuan Akhir Kaprodi' : 'Keputusan Kaprodi' ?>
            </div>
            <div class="card-body">
                <form action="?page=kaprodi.proses" method="POST">
                    <?php csrfField(); ?>
                    <input type="hidden" name="pengajuan_id" value="<?= $pengajuan['id'] ?>">
                    
                    <?php if ($pengajuan['status'] === STATUS_DIVERIFIKASI): ?>
                    <!-- Masukan Pembimbing -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-primary"><i class="bi bi-chat-left-text me-2"></i>Masukan Pembimbing Awal</label>
                        <?php 
                        $catatanPembimbing = (new CatatanBimbingan())->getByPengajuanId($pengajuan['id']);
                        if (empty($catatanPembimbing)): ?>
                            <div class="alert alert-light border small py-2">Belum ada masukan dari pembimbing.</div>
                        <?php else: ?>
                            <?php foreach ($catatanPembimbing as $cp): ?>
                            <div class="p-2 rounded bg-light border-start border-primary border-3 mb-2 small">
                                <div class="fw-bold"><?= e($cp['nama_dosen']) ?>:</div>
                                <div><?= nl2br(e($cp['catatan'])) ?></div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-primary"><i class="bi bi-people me-2"></i>Penentuan Pembimbing & Dewan</label>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="pembimbing_1_id" class="form-label small text-muted">Pembimbing Utama</label>
                                <select class="form-select" id="pembimbing_1_id" name="pembimbing_1_id">
                                    <option value="">-- Pilih Pembimbing 1 --</option>
                                    <?php foreach ($listDosen as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= ($pengajuan['pembimbing_1_id'] ?? 0) == $d['id'] ? 'selected' : '' ?>>
                                        <?= e($d['nama']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="pembimbing_2_id" class="form-label small text-muted">Pembimbing Pendamping</label>
                                <select class="form-select" id="pembimbing_2_id" name="pembimbing_2_id">
                                    <option value="">-- Pilih Pembimbing 2 --</option>
                                    <?php foreach ($listDosen as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= ($pengajuan['pembimbing_2_id'] ?? 0) == $d['id'] ? 'selected' : '' ?>>
                                        <?= e($d['nama']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="dewan_1_id" class="form-label small text-muted">Dewan Reviewer 1</label>
                                <select class="form-select" id="dewan_1_id" name="dewan_1_id" required>
                                    <option value="">-- Pilih Dewan 1 --</option>
                                    <?php foreach ($listDewan as $dw): ?>
                                    <option value="<?= $dw['id'] ?>"><?= e($dw['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dewan_2_id" class="form-label small text-muted">Dewan Reviewer 2</label>
                                <select class="form-select" id="dewan_2_id" name="dewan_2_id" required>
                                    <option value="">-- Pilih Dewan 2 --</option>
                                    <?php foreach ($listDewan as $dw): ?>
                                    <option value="<?= $dw['id'] ?>"><?= e($dw['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        <!-- Status Direview Dewan: Sudah ada pembimbing, tinggal Approve Akhir -->
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Proposal telah direview oleh Dewan. Silakan berikan keputusan akhir untuk diajukan ke Fakultas.
                        </div>
                    <?php endif; ?>

                    <!-- Hasil Review Dewan -->
                    <?php 
                    $d1Log = null; $d2Log = null;
                    foreach ($logs as $l) {
                        if (isset($pengajuan['dewan_1_id']) && $l['user_id'] == $pengajuan['dewan_1_id']) $d1Log = $l;
                        if (isset($pengajuan['dewan_2_id']) && $l['user_id'] == $pengajuan['dewan_2_id']) $d2Log = $l;
                    }
                    if (($d1Log || $d2Log) || (!empty($pengajuan['dewan_1_id']))):
                    ?>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-info"><i class="bi bi-shield-check me-2"></i>Hasil Review Dewan</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded bg-light border-start border-info border-3 h-100">
                                    <div class="fw-bold small">Reviewer 1</div>
                                    <div class="small mb-1 text-primary"><?= e($pengajuan['nama_dewan_1'] ?? '-') ?></div>
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
                                <div class="p-3 rounded bg-light border-start border-info border-3 h-100">
                                    <div class="fw-bold small">Reviewer 2</div>
                                    <div class="small mb-1 text-primary"><?= e($pengajuan['nama_dewan_2'] ?? '-') ?></div>
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
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-semibold">Catatan / Komentar Akhir</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="4"
                                  placeholder="Wajib diisi jika Tolak atau Revisi..."></textarea>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" name="aksi" value="setujui" class="btn btn-success px-4"
                                onclick="return confirm('<?= $pengajuan['status'] === STATUS_DIREVIEW_DEWAN ? 'Setujui Akhir dan Ajukan ke Fakultas?' : 'Setujui dan teruskan ke Dewan?' ?>')">
                            <i class="bi bi-patch-check me-2"></i>
                            <?= $pengajuan['status'] === STATUS_DIREVIEW_DEWAN ? 'Approve & Ajukan' : 'Setujui & Teruskan' ?>
                        </button>
                        <button type="submit" name="aksi" value="revisi" class="btn btn-warning text-white"
                                onclick="return confirm('Kembalikan untuk revisi?')">
                            <i class="bi bi-pencil me-2"></i>Revisi
                        </button>
                        <button type="submit" name="aksi" value="tolak" class="btn btn-danger"
                                onclick="return confirm('Tolak pengajuan ini?')">
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
