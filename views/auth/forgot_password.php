<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary-color: #0d47a1; --secondary-color: #1e1b4b; }
        body { min-height: 100vh; margin: 0; padding: 24px; display: grid; place-items: center; background: #eef3f9; font-family: 'Outfit', sans-serif; }
        .recovery-panel { width: min(100%, 460px); padding: clamp(28px, 7vw, 48px); background: #fff; border-top: 5px solid var(--primary-color); border-radius: 8px; box-shadow: 0 18px 48px rgba(20, 40, 70, .12); }
        .recovery-logo { display: block; width: min(100%, 220px); height: auto; margin: 0 auto 30px; }
        .form-control { min-height: 48px; border-color: #64748b; }
        .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(13, 71, 161, .16); }
        .btn-primary { min-height: 48px; background: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background: var(--secondary-color); border-color: var(--secondary-color); }
        :focus-visible { outline: 3px solid var(--primary-color); outline-offset: 3px; }
        a { color: var(--primary-color); }
    </style>
</head>
<body>
    <main class="recovery-panel">
        <img class="recovery-logo" src="assets/img/logo_fkom.png" alt="Logo FKOM">
        <h1 class="h4 mb-2" style="color: var(--secondary-color)">Lupa kata sandi?</h1>
        <p class="text-muted mb-4">Masukkan email akun Anda. Jika terdaftar, kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>

        <?php renderFlash(); ?>

        <form action="?page=password.forgot.post" method="POST">
            <?php csrfField(); ?>
            <div class="mb-4">
                <label for="resetEmail" class="form-label">Alamat email</label>
                <input type="email" id="resetEmail" name="email" class="form-control" autocomplete="email" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-100">Kirim tautan reset</button>
        </form>

        <div class="text-center mt-4">
            <a href="?page=login" class="text-decoration-none"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke halaman masuk</a>
        </div>
    </main>
</body>
</html>
