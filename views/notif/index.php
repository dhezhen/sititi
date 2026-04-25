<?php
$pageTitle = 'Notifikasi';
require BASE_PATH . '/views/layouts/header.php';
?>
<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Notifikasi</li>
        </ol></nav>
        <h1><i class="bi bi-bell me-2 text-primary"></i>Notifikasi</h1>
    </div>
    <a href="?page=notif.readall" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca
    </a>
</div>
<?php renderFlash(); ?>
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($notifList)): ?>
        <div class="empty-state">
            <i class="bi bi-bell-slash"></i>
            <h5>Tidak Ada Notifikasi</h5>
            <p>Notifikasi akan muncul di sini saat ada aktivitas.</p>
        </div>
        <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($notifList as $n): ?>
            <a href="?page=notif.read&id=<?= $n['id'] ?><?= $n['pengajuan_id'] ? '&ref='.$n['pengajuan_id'] : '' ?>"
               class="list-group-item list-group-item-action py-3 px-4 <?= !$n['is_read'] ? 'bg-light' : '' ?>">
                <div class="d-flex align-items-start gap-3">
                    <div class="notif-dot <?= !$n['is_read'] ? 'active' : '' ?>" style="margin-top:6px;width:10px;height:10px;flex-shrink:0"></div>
                    <div class="flex-grow-1">
                        <div class="<?= !$n['is_read'] ? 'fw-semibold' : '' ?>"><?= e($n['pesan']) ?></div>
                        <?php if ($n['judul_pengajuan']): ?>
                        <div class="small text-muted mt-1">
                            <i class="bi bi-file-earmark-text me-1"></i><?= e(truncate($n['judul_pengajuan'], 60)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small text-nowrap"><?= formatTanggal($n['created_at'], true) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
