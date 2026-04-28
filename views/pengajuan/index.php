<?php
$pageTitle = 'Daftar Pengajuan Saya';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Pengajuan Saya</li>
        </ol></nav>
        <h1 class="mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Pengajuan Saya</h1>
    </div>
    <?php if (!$hasActive): ?>
    <div class="flex-shrink-0">
        <a href="?page=pengajuan.create" class="btn btn-primary w-100 w-sm-auto">
            <i class="bi bi-plus-circle me-1"></i>Buat Pengajuan Baru
        </a>
    </div>
    <?php endif; ?>
</div>

<?php renderFlash(); ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($list)): ?>
        <div class="empty-state">
            <i class="bi bi-folder2-open"></i>
            <h5>Belum Ada Pengajuan</h5>
            <p>Mulai dengan membuat pengajuan judul skripsi pertama Anda.</p>
            <?php if (!$hasActive): ?>
            <a href="?page=pengajuan.create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Buat Sekarang
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sititi align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:40px" class="ps-4">#</th>
                        <th>Judul Skripsi</th>
                        <th>Bidang</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="width:120px" class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = ($page - 1) * 10 + 1; foreach ($list as $r): ?>
                    <tr>
                        <td class="text-muted ps-4"><?= $no++ ?></td>
                        <td>
                            <div class="fw-semibold text-dark"><?= e(truncate($r['judul'], 80)) ?></div>
                            <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                                <span><i class="bi bi-calendar-event me-1"></i><?= e($r['nama_periode'] ?? '-') ?></span>
                                <?php if ($r['catatan_terakhir']): ?>
                                <span class="text-danger"><i class="bi bi-chat-left-dots me-1"></i>Revisi</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= e($r['bidang']) ?></span></td>
                        <td><?= statusBadge($r['status']) ?></td>
                        <td class="small text-muted"><?= formatTanggal($r['created_at']) ?></td>
                        <td class="pe-4 text-end">
                            <div class="btn-group">
                                <a href="?page=pengajuan.show&id=<?= $r['id'] ?>"
                                   class="btn btn-sm btn-outline-primary"
                                   data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if (in_array($r['status'], ['draft', 'revisi'])): ?>
                                <a href="?page=pengajuan.edit&id=<?= $r['id'] ?>"
                                   class="btn btn-sm btn-outline-warning"
                                   data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="d-flex justify-content-center py-3">
            <nav><ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=pengajuan&p=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul></nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
