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

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom">
                    <div class="user-avatar" style="width:80px; height:80px; font-size:2rem; flex-shrink:0;">
                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold"><?= e($user['nama']) ?></h4>
                        <div class="text-muted mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-envelope"></i> <?= e($user['email']) ?>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary px-2 py-1"><?= ucfirst(Auth::role()) ?></span>
                            <span class="text-muted" style="font-size: 0.8rem;"><i class="bi bi-clock me-1"></i>Terdaftar: <?= formatTanggal($user['created_at']) ?></span>
                        </div>
                    </div>
                </div>

                <form action="?page=profil.update" method="POST">
                    <?php csrfField(); ?>
                    
                    <h6 class="mb-3 text-primary fw-semibold"><i class="bi bi-person-lines-fill me-2"></i>Informasi Pribadi</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="<?= e($user['nama']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-medium">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                        </div>
                    </div>
                    
                    <h6 class="mb-2 text-primary fw-semibold"><i class="bi bi-shield-lock me-2"></i>Keamanan Akun</h6>
                    <p class="small text-muted mb-3">Kosongkan kolom di bawah ini jika tidak ingin mengubah password saat ini.</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-medium">Password Baru</label>
                            <input type="password" name="password" class="form-control" minlength="6" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-medium">Konfirmasi Password</label>
                            <input type="password" name="password_confirm" class="form-control" minlength="6" placeholder="••••••••">
                        </div>
                    </div>
                    
                    <div class="text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4 mt-3">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
