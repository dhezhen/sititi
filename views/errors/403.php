<?php
Auth::startSession();
require BASE_PATH . '/views/layouts/header.php';
?>
<div class="d-flex align-items-center justify-content-center" style="min-height:60vh">
    <div class="text-center">
        <div style="font-size:6rem;color:#e0d7ff">&#x26D4;</div>
        <h2 class="fw-bold mt-3" style="color:#1e1b4b">403 — Akses Ditolak</h2>
        <p class="text-muted">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="?page=dashboard" class="btn btn-primary mt-2">
            <i class="bi bi-house me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
