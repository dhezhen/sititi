<?php
$pageTitle = 'Hak Akses Role (Permission)';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="?page=superadmin.roles">Manajemen Role</a></li>
            <li class="breadcrumb-item active">Hak Akses</li>
        </ol></nav>
        <h1><i class="bi bi-ui-checks-grid me-2 text-primary"></i>Hak Akses Menu per Role</h1>
    </div>
</div>

<?php renderFlash(); ?>

<div class="card">
    <div class="card-body p-0">
        <form action="?page=superadmin.role_menus.update" method="POST">
            <?php csrfField(); ?>
            <div class="table-responsive">
                <table class="table table-sititi table-bordered mb-0 text-center align-middle">
                    <thead style="background:#f5f3ff;">
                        <tr>
                            <th class="text-start" style="min-width:200px">Menu \ Role</th>
                            <?php foreach ($roles as $r): ?>
                                <th style="min-width:120px">
                                    <div class="fw-bold text-primary"><?= e($r['nama']) ?></div>
                                    <div class="small text-muted fw-normal"><?= e($r['kode']) ?></div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menus as $m): ?>
                        <tr>
                            <td class="text-start fw-semibold">
                                <i class="<?= e($m['icon']) ?> me-2 text-muted"></i><?= e($m['judul']) ?>
                                <div class="small text-muted fw-normal ms-4"><?= e($m['kode_page']) ?></div>
                            </td>
                            <?php foreach ($roles as $r): 
                                $isChecked = isset($matrix[$r['id']][$m['id']]);
                                $isSuperadmin = ($r['kode'] === 'superadmin');
                            ?>
                                <td>
                                    <?php if ($isSuperadmin): ?>
                                        <i class="bi bi-check-circle-fill text-success" title="Superadmin memiliki akses tak terbatas."></i>
                                        <input type="hidden" name="access[<?= $r['id'] ?>][<?= $m['id'] ?>]" value="on">
                                    <?php else: ?>
                                        <div class="form-check d-flex justify-content-center m-0 p-0">
                                            <input class="form-check-input" type="checkbox" name="access[<?= $r['id'] ?>][<?= $m['id'] ?>]" value="on" style="float:none; margin:0;" <?= $isChecked ? 'checked' : '' ?>>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-light border-top text-end" style="border-radius: 0 0 var(--card-r) var(--card-r)">
                <a href="?page=superadmin.roles" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan Hak Akses</button>
            </div>
        </form>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
