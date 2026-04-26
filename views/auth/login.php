<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary-color: #0d47a1;
            --primary-light: #e3f2fd;
            --secondary-color: #1e1b4b;
            --accent-color: #3b82f6;
            --bg-gradient: linear-gradient(135deg, #0d47a1 0%, #1e1b4b 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            overflow-x: hidden;
        }

        /* Abstract Background Elements */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            animation: move 25s infinite linear;
        }

        @keyframes move {
            from { transform: translate(0, 0) rotate(0deg); }
            to { transform: translate(100vw, 100vh) rotate(360deg); }
        }

        .login-container {
            width: 100%;
            max-width: 1100px;
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            animation: fadeInDown 0.8s ease-out;
        }

        .login-side-info {
            flex: 1;
            background: var(--primary-color);
            background-image: url('https://www.transparenttextures.com/patterns/cubes.png'), var(--bg-gradient);
            padding: 60px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-side-info h2 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .login-side-info p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .login-form-side {
            width: 450px;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .login-logo-box {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo-box img {
            max-width: 280px;
            height: auto;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--primary-color);
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(13, 71, 161, 0.1);
        }

        .btn-login {
            background: var(--primary-color);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 6px -1px rgba(13, 71, 161, 0.2);
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(13, 71, 161, 0.3);
            color: white;
        }

        .demo-accounts {
            margin-top: 40px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
        }

        .demo-accounts h6 {
            font-weight: 700;
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .demo-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            margin-bottom: 5px;
            color: #475569;
        }

        .demo-item code {
            color: var(--primary-color);
            background: transparent;
            padding: 0;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .login-side-info { display: none; }
            .login-container { max-width: 480px; }
            .login-form-side { width: 100%; padding: 40px 30px; }
        }

        @media (max-width: 576px) {
            .login-container { border-radius: 0; position: absolute; top:0; left:0; min-height: 100vh; }
            body { padding: 0; }
        }

        /* Animation */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="bg-shapes">
    <div class="shape" style="width: 200px; height: 200px; top: 10%; left: 10%;"></div>
    <div class="shape" style="width: 300px; height: 300px; bottom: 20%; right: 10%; animation-delay: -5s;"></div>
    <div class="shape" style="width: 150px; height: 150px; top: 40%; right: 30%; animation-delay: -10s;"></div>
</div>

<div class="login-container">
    <div class="login-side-info">
        <div class="animate__animated animate__fadeInLeft">
            <h2>Portal Akademik Skripsi</h2>
            <p>Selamat datang di sistem manajemen pengajuan judul skripsi Fakultas Ilmu Komputer Universitas Kuningan. Kelola progres tugas akhir Anda dengan lebih efisien dan terstruktur.</p>
            
            <div class="d-flex gap-4 mt-5">
                <div class="text-center">
                    <h4 class="fw-bold mb-0">100%</h4>
                    <small class="opacity-75">Digital</small>
                </div>
                <div class="vr"></div>
                <div class="text-center">
                    <h4 class="fw-bold mb-0">Real-time</h4>
                    <small class="opacity-75">Tracking</small>
                </div>
                <div class="vr"></div>
                <div class="text-center">
                    <h4 class="fw-bold mb-0">Secured</h4>
                    <small class="opacity-75">Platform</small>
                </div>
            </div>
        </div>
    </div>

    <div class="login-form-side">
        <div class="login-logo-box animate__animated animate__zoomIn">
            <img src="assets/img/logo_fkom.png" alt="Logo FKOM">
        </div>

        <h4 class="fw-800 text-center mb-4" style="font-weight: 800; color: var(--secondary-color);">Sign In</h4>

        <?php renderFlash(); ?>

        <form action="?page=login.post" method="POST" id="loginForm">
            <?php csrfField(); ?>

            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" value="<?= e(post('email')) ?>" required>
                <label for="email"><i class="bi bi-envelope me-2"></i>Alamat Email</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="bi bi-lock me-2"></i>Kata Sandi</label>
                <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y me-2" type="button" id="togglePass" style="z-index: 10;">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>

            <button type="submit" class="btn btn-login w-100" id="btnLogin">
                Masuk Sekarang
            </button>
        </form>

        <div class="demo-accounts">
            <h6>Akun Simulasi Testing</h6>
            <div class="demo-item"><span>Mahasiswa</span> <code>mahasiswa@demo.com</code></div>
            <div class="demo-item"><span>Staff Akademik</span> <code>staff@demo.com</code></div>
            <div class="demo-item"><span>Kaprodi</span> <code>kaprodi@demo.com</code></div>
            <div class="demo-item"><span>Dewan Pembimbing</span> <code>dewan@demo.com</code></div>
            <div class="text-center mt-3 small text-muted">
                Password: <code>password</code>
            </div>
        </div>
        
        <div class="mt-5 text-center small text-muted">
            &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
        </div>
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
