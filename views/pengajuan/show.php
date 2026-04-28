<?php
$pageTitle = 'Detail Pengajuan';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb" class="d-print-none"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="?page=pengajuan">Pengajuan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol></nav>
        <h1 class="d-print-none"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Detail Pengajuan</h1>
        <h1 class="d-none d-print-block">Bukti Pengajuan Judul Skripsi</h1>
    </div>
    <div class="d-flex gap-2">
        <?php if (Auth::role() === ROLE_MAHASISWA): ?>
        <?php if (in_array($pengajuan['status'], ['draft', 'revisi'])): ?>
        <a href="?page=pengajuan.edit&id=<?= $pengajuan['id'] ?>" class="btn btn-warning text-white">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <?php endif; ?>
        <?php if (in_array($pengajuan['status'], ['draft', 'revisi'])): ?>
        <a href="?page=pengajuan.submit&id=<?= $pengajuan['id'] ?>"
           class="btn btn-primary"
           data-confirm="Yakin ingin mengajukan proposal ini?">
            <i class="bi bi-send me-1"></i>Ajukan Sekarang
        </a>
        <?php endif; ?>
        <?php if ($pengajuan['status'] === 'draft'): ?>
        <a href="?page=pengajuan.delete&id=<?= $pengajuan['id'] ?>"
           class="btn btn-outline-danger btn-sm"
           data-confirm="Hapus pengajuan ini secara permanen?">
            <i class="bi bi-trash"></i>
        </a>
        <?php endif; ?>
        <?php elseif (in_array(Auth::role(), [ROLE_STAFF, ROLE_KAPRODI, ROLE_SUPERADMIN])): ?>
        <a href="?page=pengajuan.edit&id=<?= $pengajuan['id'] ?>" class="btn btn-warning text-dark">
            <i class="bi bi-pencil me-1"></i>Edit Data (Admin)
        </a>
        <?php endif; ?>
        <?php if ($pengajuan['status'] !== 'draft'): ?>
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak Bukti
        </button>
        <?php endif; ?>
    </div>
</div>

<?php renderFlash(); ?>

<div class="row g-4">
    <!-- Detail Utama -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Pengajuan</span>
                <?= statusBadge($pengajuan['status']) ?>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="fw-semibold text-muted" style="width:180px">Mahasiswa</td>
                        <td><?= e($pengajuan['nama_mahasiswa']) ?> (<?= e($pengajuan['nim']) ?>)</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Program Studi</td>
                        <td><?= e($pengajuan['nama_prodi'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Periode</td>
                        <td><?= e($pengajuan['nama_periode'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Bidang/Topik</td>
                        <td><span class="badge bg-secondary"><?= e($pengajuan['bidang']) ?></span></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Tanggal Dibuat</td>
                        <td><?= formatTanggal($pengajuan['created_at'], true) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Terakhir Diperbarui</td>
                        <td><?= formatTanggal($pengajuan['updated_at'], true) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Usulan Pembimbing 1</td>
                        <td><?= e($pengajuan['nama_pembimbing_1'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Usulan Pembimbing 2</td>
                        <td><?= e($pengajuan['nama_pembimbing_2'] ?? '-') ?></td>
                    </tr>
                </table>

                <hr>
                <h6 class="fw-bold mb-2">Judul Skripsi</h6>
                <p class="fs-6 fw-semibold text-primary mb-3"><?= e($pengajuan['judul']) ?></p>

                <h6 class="fw-bold mb-2">Deskripsi / Latar Belakang</h6>
                <div class="p-3 rounded-3" style="background:#f9fafb;font-size:.9rem;line-height:1.7">
                    <?= (strpos($pengajuan['deskripsi'], '<') !== false) ? $pengajuan['deskripsi'] : nl2br(e($pengajuan['deskripsi'])) ?>
                </div>

                <?php if ($pengajuan['catatan_terakhir']): ?>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-chat-left-text me-2"></i>
                    <strong>Catatan:</strong> <?= e($pengajuan['catatan_terakhir']) ?>
                </div>
                <?php endif; ?>

                <?php if ($pengajuan['file_proposal']): ?>
                <div class="mt-3">
                    <h6 class="fw-bold mb-2">File Ringkasan Proposal</h6>
                    <a href="<?= UPLOAD_URL ?>/<?= e($pengajuan['file_proposal']) ?>"
                       target="_blank" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Lihat / Download Proposal PDF
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($pembimbing)): ?>
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-success"></i>Dosen Pembimbing</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($pembimbing as $p): ?>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light-subtle">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width:48px;height:48px;font-size:1.2rem;flex-shrink:0;overflow:hidden;">
                                    <?php if (!empty($p['foto'])): ?>
                                        <img src="<?= AVATAR_URL ?>/<?= e($p['foto']) ?>" alt="Dosen" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                        <?= strtoupper(substr($p['nama_dosen'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?= e($p['nama_dosen']) ?></div>
                                    <div class="text-muted small">NIDN: <?= e($p['nidn']) ?></div>
                                    <span class="badge bg-primary mt-1">Pembimbing <?= $p['urutan'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Keputusan Dewan Reviewer -->
        <?php 
        $dewanLogs = array_filter($logs, function($l) use ($pengajuan) {
            return in_array($l['user_id'], [$pengajuan['dewan_1_id'] ?? 0, $pengajuan['dewan_2_id'] ?? 0]);
        });
        if (!empty($dewanLogs) || !empty($pengajuan['dewan_1_id'])): 
        ?>
        <div class="card mb-4 border-0 shadow-sm border-start border-info border-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-info"></i>Hasil Review Dewan Bimbingan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php 
                    $d1Log = null;
                    $d2Log = null;
                    foreach ($logs as $l) {
                        if ($l['user_id'] == $pengajuan['dewan_1_id']) $d1Log = $l;
                        if ($l['user_id'] == $pengajuan['dewan_2_id']) $d2Log = $l;
                    }
                    ?>
                    <!-- Dewan 1 -->
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="fw-bold text-dark small">Reviewer 1</span>
                                <?php if ($d1Log): ?>
                                    <?= statusBadge($d1Log['status_baru']) ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border">Belum Review</span>
                                <?php endif; ?>
                            </div>
                            <div class="fw-semibold small mb-2 text-primary"><?= e($pengajuan['nama_dewan_1'] ?: '-') ?></div>
                            <?php if ($d1Log && $d1Log['catatan']): ?>
                                <div class="bg-white p-2 rounded border small italic">
                                    <?= (strpos($d1Log['catatan'], '<') !== false) ? $d1Log['catatan'] : nl2br(e($d1Log['catatan'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Dewan 2 -->
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="fw-bold text-dark small">Reviewer 2</span>
                                <?php if ($d2Log): ?>
                                    <?= statusBadge($d2Log['status_baru']) ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border">Belum Review</span>
                                <?php endif; ?>
                            </div>
                            <div class="fw-semibold small mb-2 text-primary"><?= e($pengajuan['nama_dewan_2'] ?: '-') ?></div>
                            <?php if ($d2Log && $d2Log['catatan']): ?>
                                <div class="bg-white p-2 rounded border small italic">
                                    <?= (strpos($d2Log['catatan'], '<') !== false) ? $d2Log['catatan'] : nl2br(e($d2Log['catatan'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Masukan Pembimbing -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-chat-right-dots-fill me-2 text-primary"></i>Masukan & Saran Pembimbing</h6>
            </div>
            <div class="card-body">
                <?php if (empty($catatanBimbingan)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-chat-dots fs-2 d-block mb-2 opacity-25"></i>
                    <p class="small mb-0">Belum ada masukan dari pembimbing.</p>
                </div>
                <?php else: ?>
                <div class="timeline">
                    <?php foreach ($catatanBimbingan as $c): ?>
                    <div class="timeline-item mb-4">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content p-3 border-0 bg-light-subtle border-start border-primary border-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width:24px; height:24px; font-size:0.7rem;">
                                        <?php if (!empty($c['foto_dosen'])): ?>
                                            <img src="<?= AVATAR_URL ?>/<?= e($c['foto_dosen']) ?>" alt="Dosen" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($c['nama_dosen'], 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <span class="fw-bold small text-primary"><?= e($c['nama_dosen']) ?></span>
                                </div>
                                <span class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i><?= formatTanggal($c['created_at'], true) ?></span>
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

    <!-- Timeline Status -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-activity me-2 text-primary"></i>Riwayat Status (Timeline)
            </div>
            <div class="card-body">
                <?php if (empty($logs)): ?>
                <div class="text-center text-muted small py-3">Belum ada riwayat.</div>
                <?php else: ?>
                <div class="timeline">
                    <?php foreach ($logs as $log):
                        $dotClass = match($log['status_baru']) {
                            'disetujui', 'pembimbing_ditentukan' => 'success',
                            'ditolak'  => 'danger',
                            'revisi'   => 'warning',
                            default    => '',
                        };
                    ?>
                    <div class="timeline-item">
                        <div class="timeline-dot <?= $dotClass ?>"></div>
                        <div class="timeline-content">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <?= statusBadge($log['status_baru']) ?>
                            </div>
                            <?php if ($log['catatan']): ?>
                            <div class="small text-secondary mb-1">
                                <i class="bi bi-chat-left-text me-1"></i><?= e($log['catatan']) ?>
                            </div>
                            <?php endif; ?>
                            <div class="timeline-meta">
                                <i class="bi bi-person me-1"></i><?= e($log['nama_user']) ?>
                                (<?= ucfirst($log['role']) ?>)<br>
                                <i class="bi bi-clock me-1"></i><?= formatTanggal($log['created_at'], true) ?>
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
