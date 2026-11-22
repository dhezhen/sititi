<?php
$pageTitle = 'Dashboard Komisi Tugas Akhir';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    <h1><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Komisi Tugas Akhir</h1>
    <p>Selamat datang, <strong><?= e(Auth::user()['nama']) ?></strong></p>
</div>

<?php renderFlash(); ?>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card stat-warning shadow-sm border-0">
            <div class="stat-value"><?= $perluReview ?></div>
            <div class="stat-label text-uppercase fw-bold small">Perlu Review</div>
            <div class="stat-sublabel small text-muted">Menunggu keputusan Dewan</div>
            <i class="bi bi-clock-history stat-icon opacity-25"></i>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card stat-info shadow-sm border-0">
            <div class="stat-value"><?= $telahReview ?></div>
            <div class="stat-label text-uppercase fw-bold small">Telah Direview</div>
            <div class="stat-sublabel small text-muted">Menunggu approval akhir Kaprodi</div>
            <i class="bi bi-clipboard-check stat-icon opacity-25"></i>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card stat-success shadow-sm border-0">
            <div class="stat-value"><?= $totalProses ?></div>
            <div class="stat-label text-uppercase fw-bold small">Selesai / Terbit SK</div>
            <div class="stat-sublabel small text-muted">Total pengajuan tuntas</div>
            <i class="bi bi-patch-check stat-icon opacity-25"></i>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clipboard2-data me-2 text-primary"></i>Antrian Review Proposal</h6>
                <a href="?page=dewan" class="btn btn-xs btn-primary rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent)): ?>
                <div class="empty-state py-5 text-center">
                    <div class="mb-3 text-muted opacity-25"><i class="bi bi-check2-all display-1"></i></div>
                    <h5 class="text-muted fw-light">Antrian Kosong</h5>
                    <p class="text-muted small mb-0">Belum ada pengajuan baru yang diteruskan ke Dewan.</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 text-uppercase small fw-bold text-muted" style="font-size: .75rem">Mahasiswa</th>
                                <th class="text-uppercase small fw-bold text-muted" style="font-size: .75rem">Judul Skripsi</th>
                                <th class="text-uppercase small fw-bold text-muted" style="font-size: .75rem">Status</th>
                                <th class="text-end pe-4 text-uppercase small fw-bold text-muted" style="font-size: .75rem">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent as $r): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width:35px;height:35px;font-size:.8rem">
                                            <?= strtoupper(substr($r['nama_mahasiswa'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size:.9rem"><?= e($r['nama_mahasiswa']) ?></div>
                                            <small class="text-muted"><?= e($r['nim']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium mb-1" style="font-size:.85rem"><?= e(truncate($r['judul'], 45)) ?></div>
                                    <span class="badge bg-soft-secondary text-secondary small" style="font-size:.65rem"><?= e($r['bidang'] ?? 'Umum') ?></span>
                                </td>
                                <td><?= statusBadge($r['status']) ?></td>
                                <td class="text-end pe-4">
                                    <a href="?page=dewan.pembimbing&id=<?= $r['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                        Review <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-bell me-2 text-primary"></i>Pemberitahuan</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($notifasi)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-chat-dots text-muted opacity-25 display-6 d-block mb-2"></i>
                        <span class="text-muted small">Belum ada notifikasi</span>
                    </div>
                    <?php else: ?>
                    <?php foreach ($notifasi as $n): ?>
                    <div class="list-group-item list-group-item-action border-0 px-4 py-3 position-relative <?= !$n['is_read'] ? 'bg-soft-primary' : '' ?>">
                        <?php if(!$n['is_read']): ?>
                            <span class="position-absolute top-50 start-0 translate-middle-y bg-primary rounded-end" style="width:3px;height:60%"></span>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold small text-dark"><?= !$n['is_read'] ? 'Baru' : 'Info' ?></span>
                            <small class="text-muted" style="font-size:.7rem"><?= formatTanggal($n['created_at'], true) ?></small>
                        </div>
                        <p class="mb-0 small text-muted" style="line-height:1.4"><?= e(truncate($n['pesan'], 100)) ?></p>
                        <a href="?page=notif.read&id=<?= $n['id'] ?>" class="stretched-link"></a>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($notifasi)): ?>
            <div class="card-footer bg-white border-0 text-center py-3">
                <a href="#" class="small text-decoration-none fw-bold">Tandai semua dibaca</a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden">
            <div class="card-body position-relative p-4">
                <div class="position-relative z-1">
                    <h6 class="fw-bold mb-3">Tugas Komisi Tugas Akhir</h6>
                    <p class="small opacity-75 mb-0" style="line-height:1.6">
                        Silakan review proposal yang masuk dan pastikan kesesuaian pembimbing yang telah diajukan oleh Kaprodi sebelum diteruskan untuk persetujuan akhir.
                    </p>
                </div>
                <i class="bi bi-shield-check position-absolute end-0 bottom-0 mb-n3 me-n2 opacity-25 display-1"></i>
            </div>
        </div>
    </div>
</div>


<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
