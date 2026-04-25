<?php
$pageTitle = 'Profil Saya';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Profil Saya</li>
    </ol></nav>
    <h1><i class="bi bi-person-circle me-2 text-primary"></i>Profil Saya</h1>
</div>

<?php renderFlash(); ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body py-5">
                <div class="user-avatar mx-auto mb-3" style="width:100px; height:100px; font-size:2.5rem;">
                    <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                </div>
                <h4 class="mb-1"><?= e($user['nama']) ?></h4>
                <p class="text-muted mb-2"><?= e($user['email']) ?></p>
                <span class="badge bg-primary"><?= ucfirst(Auth::role()) ?></span>
            </div>
            <div class="card-footer bg-light text-muted small">
                Terdaftar sejak: <?= formatTanggal($user['created_at']) ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-gear me-2"></i>Pengaturan Akun</div>
            <div class="card-body">
                <form action="?page=profil.update" method="POST">
                    <?php csrfField(); ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="<?= e($user['nama']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3">Ubah Password <span class="text-muted fw-normal fs-6">(Kosongkan jika tidak ingin mengubah)</span></h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirm" class="form-control" minlength="6">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
