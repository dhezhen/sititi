<?php
$pageTitle = 'Profil Saya';
require BASE_PATH . '/views/layouts/header.php';

// Ambil data profil berdasarkan role
$user = Auth::user();
$db = getDB();

if ($user['role'] === ROLE_MAHASISWA) {
    $stmt = $db->prepare("SELECT m.*, p.nama as nama_prodi FROM mahasiswa m LEFT JOIN prodi p ON m.prodi_id = p.id WHERE m.user_id = ?");
    $stmt->execute([$user['id']]);
    $detail = $stmt->fetch();
}
?>

<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Profil</li>
        </ol>
    </nav>
    <h1><i class="bi bi-person-circle me-2 text-primary"></i>Profil Saya</h1>
</div>

<?php renderFlash(); ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 3rem;">
                <?= strtoupper(substr($user['nama'], 0, 1)) ?>
            </div>
            <h4 class="fw-bold mb-1"><?= e($user['nama']) ?></h4>
            <p class="text-muted mb-3"><?= ucfirst($user['role']) ?></p>
            <span class="badge bg-success mb-3">Akun Aktif</span>
            <hr>
            <div class="text-start small">
                <p class="mb-1 text-muted">Email:</p>
                <p class="fw-semibold mb-3"><?= e($user['email']) ?></p>
                <p class="mb-1 text-muted">Terdaftar sejak:</p>
                <p class="fw-semibold mb-0"><?= formatTanggal($user['created_at'] ?? date('Y-m-d')) ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2 text-primary"></i>Detail Informasi
            </div>
            <div class="card-body">
                <?php if ($user['role'] === ROLE_MAHASISWA && $detail): ?>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">NIM:</div>
                    <div class="col-sm-8 fw-semibold"><?= e($detail['nim']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Program Studi:</div>
                    <div class="col-sm-8 fw-semibold"><?= e($detail['nama_prodi'] ?? '-') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Angkatan:</div>
                    <div class="col-sm-8 fw-semibold"><?= e($detail['angkatan']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">No. HP:</div>
                    <div class="col-sm-8 fw-semibold"><?= e($detail['no_hp'] ?? '-') ?></div>
                </div>
                <div class="row mb-0">
                    <div class="col-sm-4 text-muted">Alamat:</div>
                    <div class="col-sm-8 fw-semibold"><?= e($detail['alamat'] ?? '-') ?></div>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    Informasi detail untuk role <strong><?= ucfirst($user['role']) ?></strong> tidak tersedia atau belum diatur.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-shield-lock me-2 text-warning"></i>Keamanan
            </div>
            <div class="card-body">
                <p class="text-muted small mb-4">Fitur ubah password dapat diimplementasikan di sini untuk meningkatkan keamanan akun Anda.</p>
                <button class="btn btn-outline-warning btn-sm" disabled>
                    <i class="bi bi-key me-1"></i>Ubah Password (Coming Soon)
                </button>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
