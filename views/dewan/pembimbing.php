<?php
$pageTitle = 'Tentukan Pembimbing';
require BASE_PATH . '/views/layouts/header.php';
?>
<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="?page=dewan">Pembimbing</a></li>
        <li class="breadcrumb-item active">Tentukan Pembimbing</li>
    </ol></nav>
    <h1><i class="bi bi-person-plus me-2 text-primary"></i>Tentukan Dosen Pembimbing</h1>
</div>
<?php renderFlash(); ?>
<div class="row g-4">
    <div class="col-lg-7">
        <!-- Info Pengajuan -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-file-earmark me-2 text-primary"></i>Data Pengajuan</span>
                <?= statusBadge($pengajuan['status']) ?>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-2">
                    <tr><td class="fw-semibold text-muted" style="width:130px">Mahasiswa</td>
                        <td><?= e($pengajuan['nama_mahasiswa']) ?> (<code><?= e($pengajuan['nim']) ?></code>)</td></tr>
                    <tr><td class="fw-semibold text-muted">Prodi</td>
                        <td><?= e($pengajuan['nama_prodi'] ?? '-') ?></td></tr>
                    <tr><td class="fw-semibold text-muted">Bidang</td>
                        <td><span class="badge bg-secondary"><?= e($pengajuan['bidang']) ?></span></td></tr>
                </table>
                <h6 class="fw-bold text-muted small mb-1">JUDUL</h6>
                <p class="fw-semibold text-primary mb-3"><?= e($pengajuan['judul']) ?></p>
                <h6 class="fw-bold text-muted small mb-1">RINGKASAN PROPOSAL</h6>
                <div class="p-3 rounded-3 bg-light small mb-3" style="white-space:pre-wrap"><?= e($pengajuan['deskripsi']) ?></div>
                <?php if ($pengajuan['file_proposal']): ?>
                <a href="<?= UPLOAD_URL ?>/<?= e($pengajuan['file_proposal']) ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Proposal Lengkap
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Pembimbing -->
        <div class="card border-primary">
            <div class="card-header bg-primary text-white"><i class="bi bi-people-fill me-2"></i>Review & Penetapan Pembimbing</div>
            <div class="card-body">
                <?php
                // Ambil usulan dari Kaprodi/Mahasiswa sebagai default
                $def1 = $pengajuan['pembimbing_1_id'];
                $def2 = $pengajuan['pembimbing_2_id'];
                ?>
                <form action="?page=dewan.proses" method="POST">
                    <?php csrfField(); ?>
                    <input type="hidden" name="pengajuan_id" value="<?= $pengajuan['id'] ?>">

                    <div class="mb-4">
                        <label for="dosen1_id" class="form-label fw-semibold">
                            <span class="badge bg-primary me-1">Pembimbing 1</span> (Wajib)
                        </label>
                        <select class="form-select" id="dosen1_id" name="dosen1_id" required>
                            <option value="">-- Pilih Dosen Pembimbing 1 --</option>
                            <?php foreach ($listDosen as $d): 
                                $isFull = ($d['workload'] ?? 0) >= MAX_PEMBIMBING_PER_ANGKATAN;
                                $workloadText = ($d['workload'] ?? 0) . "/" . MAX_PEMBIMBING_PER_ANGKATAN;
                            ?>
                            <option value="<?= $d['id'] ?>"
                                    <?= $def1 == $d['id'] ? 'selected' : '' ?>
                                    <?= $isFull ? 'class="text-danger"' : '' ?>>
                                <?= e($d['nama']) ?> 
                                [<?= $workloadText ?>]
                                — <?= e($d['jabatan_akademik'] ?? '-') ?>
                                <?= $isFull ? ' (PENUH)' : '' ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="dosen2_id" class="form-label fw-semibold">
                            <span class="badge bg-secondary me-1">Pembimbing 2</span> (Opsional)
                        </label>
                        <select class="form-select" id="dosen2_id" name="dosen2_id">
                            <option value="">-- Tidak Ada Pembimbing 2 --</option>
                            <?php foreach ($listDosen as $d): 
                                $isFull = ($d['workload'] ?? 0) >= MAX_PEMBIMBING_PER_ANGKATAN;
                                $workloadText = ($d['workload'] ?? 0) . "/" . MAX_PEMBIMBING_PER_ANGKATAN;
                            ?>
                            <option value="<?= $d['id'] ?>"
                                    <?= $def2 == $d['id'] ? 'selected' : '' ?>
                                    <?= $isFull ? 'class="text-danger"' : '' ?>>
                                <?= e($d['nama']) ?> 
                                [<?= $workloadText ?>]
                                — <?= e($d['jabatan_akademik'] ?? '-') ?>
                                <?= $isFull ? ' (PENUH)' : '' ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text mt-2 text-info small">
                            <i class="bi bi-info-circle me-1"></i>
                            Dosen <strong>Asisten Ahli</strong> hanya dapat menjadi Pembimbing 2.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-semibold">Catatan / Rekomendasi</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"
                                  placeholder="Catatan untuk mahasiswa terkait pembimbing..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary px-4"
                            data-confirm="Simpan penentuan pembimbing ini?">
                        <i class="bi bi-save me-2"></i>Simpan Pembimbing
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <!-- Pembimbing Aktif Saat Ini -->
        <?php if (!empty($pembimbingAktif)): ?>
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-check me-2 text-success"></i>Pembimbing Saat Ini</div>
            <div class="card-body">
                <?php foreach ($pembimbingAktif as $pb): ?>
                <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3 border">
                    <div class="user-avatar" style="width:44px;height:44px;font-size:1.1rem;flex-shrink:0">
                        <?= strtoupper(substr($pb['nama_dosen'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-semibold"><?= e($pb['nama_dosen']) ?></div>
                        <div class="text-muted small">NIDN: <?= e($pb['nidn']) ?></div>
                        <span class="badge bg-primary mt-1">Pembimbing <?= $pb['urutan'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header"><i class="bi bi-activity me-2 text-primary"></i>Riwayat Status</div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($logs as $log):
                        $dc=match($log['status_baru']){'disetujui','pembimbing_ditentukan'=>'success','ditolak'=>'danger','revisi'=>'warning',default=>''}; ?>
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
