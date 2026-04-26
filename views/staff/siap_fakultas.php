<?php
$pageTitle = 'Pengajuan Siap Diajukan ke Fakultas';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <div class="page-header-title">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="?page=staff">Verifikasi Berkas</a></li>
            <li class="breadcrumb-item active">Siap Diajukan ke Fakultas</li>
        </ol></nav>
        <h1 class="mb-0"><i class="bi bi-send-check me-2 text-success"></i>Pengajuan Siap Diajukan ke Fakultas</h1>
        <p class="text-muted mt-1 mb-0">Daftar pengajuan judul skripsi yang telah disetujui dan siap diajukan ke Fakultas.</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=export.excel" class="btn btn-success shadow-sm px-4 py-2">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </a>
    </div>
</div>

<?php renderFlash(); ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" 
                     style="width:56px;height:56px;background:linear-gradient(135deg,#198754,#20c997);">
                    <i class="bi bi-check-circle-fill text-white fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Total Disetujui</div>
                    <div class="fs-3 fw-bold text-success"><?= $total ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" 
                     style="width:56px;height:56px;background:linear-gradient(135deg,#0d6efd,#6610f2);">
                    <i class="bi bi-building text-white fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Status</div>
                    <div class="fs-6 fw-semibold text-primary">Siap Diajukan ke Fakultas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" 
                     style="width:56px;height:56px;background:linear-gradient(135deg,#ffc107,#fd7e14);">
                    <i class="bi bi-file-earmark-excel text-white fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Aksi Cepat</div>
                    <a href="?page=export.excel" class="btn btn-sm btn-outline-success mt-1">
                        <i class="bi bi-download me-1"></i>Unduh Rekap
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-3 px-4">
        <form method="GET" class="row g-3 align-items-center">
            <input type="hidden" name="page" value="staff.siap_fakultas">
            <div class="col-md-9">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" name="search"
                           value="<?= e($search) ?>" placeholder="Cari nama mahasiswa, NIM, atau judul skripsi...">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Cari
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-list-ul me-2 text-primary"></i>Daftar Pengajuan Disetujui
            <span class="badge bg-success ms-2"><?= $total ?></span>
        </span>
        <?php if ($total > 0): ?>
        <span class="text-muted small">
            Halaman <?= $page ?> dari <?= max(1, $pages) ?>
        </span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (empty($list)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox" style="font-size:3rem;color:#adb5bd;"></i>
            <h5 class="mt-3">Belum Ada Pengajuan yang Disetujui</h5>
            <p class="text-muted">Pengajuan yang telah melalui seluruh proses persetujuan akan muncul di sini.</p>
            <a href="?page=staff" class="btn btn-outline-primary mt-2">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Verifikasi
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-sititi mb-0">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Mahasiswa</th>
                    <th>Judul Skripsi</th>
                    <th>Program Studi</th>
                    <th>Pembimbing</th>
                    <th>Status</th>
                    <th>Tgl Diajukan</th>
                    <th style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = ($page - 1) * $perPage + 1;
                foreach ($list as $r):
                    // Fetch pembimbing names
                    $p1Name = '-';
                    $p2Name = '-';
                    if (!empty($r['pembimbing_1_id'])) {
                        $dosen = (new Dosen())->findById($r['pembimbing_1_id']);
                        $p1Name = $dosen ? $dosen['nama'] : '-';
                    }
                    if (!empty($r['pembimbing_2_id'])) {
                        $dosen2 = (new Dosen())->findById($r['pembimbing_2_id']);
                        $p2Name = $dosen2 ? $dosen2['nama'] : '-';
                    }
                ?>
                <tr>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($r['nama_mahasiswa']) ?></div>
                        <small class="text-muted"><?= e($r['nim']) ?></small>
                    </td>
                    <td>
                        <span title="<?= e($r['judul']) ?>"><?= e(truncate($r['judul'], 45)) ?></span>
                        <?php if (!empty($r['bidang'])): ?>
                        <div><span class="badge bg-secondary-subtle text-secondary mt-1" style="font-size:.7rem;"><?= e($r['bidang']) ?></span></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-primary-subtle text-primary"><?= e($r['nama_prodi'] ?? '-') ?></span></td>
                    <td>
                        <div class="small">
                            <div><i class="bi bi-1-circle me-1 text-primary"></i><?= e($p1Name) ?></div>
                            <?php if ($p2Name !== '-'): ?>
                            <div class="mt-1"><i class="bi bi-2-circle me-1 text-secondary"></i><?= e($p2Name) ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?= statusBadge($r['status']) ?></td>
                    <td class="small text-muted"><?= formatTanggal($r['created_at']) ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="?page=pengajuan.show&id=<?= $r['id'] ?>" 
                               class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if ($r['status'] === STATUS_PEMBIMBING): ?>
                            <form method="POST" action="?page=staff.mark_selesai" class="d-inline"
                                  onsubmit="return confirm('Tandai pengajuan ini sebagai Siap Diajukan ke Fakultas?');">
                                <?= csrfField() ?>
                                <input type="hidden" name="pengajuan_id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-primary" title="Tandai Siap Fakultas">
                                    <i class="bi bi-check2-all"></i>
                                </button>
                            </form>
                            <?php elseif ($r['status'] === STATUS_SELESAI): ?>
                            <form method="POST" action="?page=staff.mark_selesai_fakultas" class="d-inline"
                                  onsubmit="return confirm('Tandai pengajuan ini sebagai Selesai Diajukan ke Fakultas?');">
                                <?= csrfField() ?>
                                <input type="hidden" name="pengajuan_id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-success" title="Selesai Diajukan">
                                    <i class="bi bi-check-circle-fill"></i>
                                </button>
                            </form>
                            <form method="POST" action="?page=staff.undo_selesai" class="d-inline"
                                  onsubmit="return confirm('Batalkan status Siap Diajukan ke Fakultas? Pengajuan akan kembali ke status Selesai di Tingkat Prodi.');">
                                <?= csrfField() ?>
                                <input type="hidden" name="pengajuan_id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-warning text-dark" title="Batalkan Siap Fakultas">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>
                            <?php elseif ($r['status'] === STATUS_SELESAI_FAKULTAS): ?>
                            <form method="POST" action="?page=staff.undo_selesai_fakultas" class="d-inline"
                                  onsubmit="return confirm('Batalkan status Selesai Diajukan ke Fakultas? Pengajuan akan kembali ke status Siap Diajukan ke Fakultas.');">
                                <?= csrfField() ?>
                                <input type="hidden" name="pengajuan_id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-warning text-dark" title="Batalkan Selesai Diajukan">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <?php if ($pages > 1): ?>
        <div class="d-flex justify-content-center py-3">
            <nav><ul class="pagination pagination-sm mb-0">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=staff.siap_fakultas&p=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?page=staff.siap_fakultas&p=<?= $i ?>&search=<?= urlencode($search) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=staff.siap_fakultas&p=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul></nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
