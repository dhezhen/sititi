<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body class="login-page">

<div class="login-card animate__animated">
    <!-- Logo -->
    <div class="login-logo">
        <i class="bi bi-journal-bookmark-fill"></i>
    </div>

    <h4 class="text-center fw-800 mb-1" style="color:#1e1b4b;font-weight:800">
        <?= APP_NAME ?>
    </h4>
    <p class="text-center text-muted mb-4" style="font-size:.85rem">
        <?= APP_FULL_NAME ?>
    </p>

    <!-- Flash -->
    <?php renderFlash(); ?>

    <!-- Form Login -->
    <form action="?page=login.post" method="POST" novalidate id="loginForm">
        <?php csrfField(); ?>

        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope me-1"></i>Email
            </label>
            <input type="email" class="form-control" id="email" name="email"
                   value="<?= e(post('email')) ?>"
                   placeholder="nama@email.com" required autocomplete="email">
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">
                <i class="bi bi-lock me-1"></i>Password
            </label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Masukkan password" required autocomplete="current-password">
                <button class="btn btn-outline-secondary" type="button" id="togglePass"
                        tabindex="-1" aria-label="Tampilkan password">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" id="btnLogin">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
        </button>
    </form>

    <!-- Info Akun Demo -->
    <div class="mt-4 p-3 rounded-3" style="background:#f5f3ff;font-size:.8rem">
        <div class="fw-semibold mb-2 text-primary">
            <i class="bi bi-info-circle me-1"></i>Akun Demo
        </div>
        <table class="table table-sm mb-0" style="font-size:.78rem">
            <tr><td class="text-muted">Mahasiswa</td><td><code>mahasiswa@demo.com</code></td><td><code>password</code></td></tr>
            <tr><td class="text-muted">Staff</td><td><code>staff@demo.com</code></td><td><code>password</code></td></tr>
            <tr><td class="text-muted">Kaprodi</td><td><code>kaprodi@demo.com</code></td><td><code>password</code></td></tr>
            <tr><td class="text-muted">Dewan</td><td><code>dewan@demo.com</code></td><td><code>password</code></td></tr>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password visibility
    document.getElementById('togglePass').addEventListener('click', function () {
        var inp  = document.getElementById('password');
        var icon = document.getElementById('eyeIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            inp.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });

    // Loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        var btn = document.getElementById('btnLogin');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    });
</script>
</body>
</html>
