<?php
/**
 * Auth Controller - Login & Logout
 * File: controllers/AuthController.php
 */

class AuthController {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Tampilkan halaman login
     */
    public function showLogin(): void {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            Auth::redirectToDashboard();
        }
        require BASE_PATH . '/views/auth/login.php';
    }

    /**
     * Proses login
     */
    public function login(): void {
        if (!isPost()) {
            redirect('?page=login');
        }

        verifyCsrf();

        $email    = trim(post('email'));
        $password = post('password');
        $errors   = [];

        // Validasi input
        if (empty($email)) {
            $errors[] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if (empty($password)) {
            $errors[] = 'Password wajib diisi.';
        }

        if (!empty($errors)) {
            setFlash('danger', implode(' ', $errors));
            redirect('?page=login');
        }

        // Cari user di database
        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            setFlash('danger', 'Email atau password salah.');
            redirect('?page=login');
        }

        if (!$user['is_active']) {
            setFlash('warning', 'Akun Anda tidak aktif. Hubungi administrator.');
            redirect('?page=login');
        }

        // Login berhasil
        Auth::login($user);
        Auth::redirectToDashboard();
    }

    public function showForgotPassword(): void {
        $pageTitle = 'Lupa Kata Sandi';
        require BASE_PATH . '/views/auth/forgot_password.php';
    }

    public function sendPasswordReset(): void {
        if (!isPost()) redirect('?page=password.forgot');
        verifyCsrf();

        $email = mb_strtolower(trim((string)post('email')));
        $genericMessage = 'Jika email tersebut terdaftar, tautan untuk mengatur ulang kata sandi akan dikirim.';

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $resetModel = new PasswordReset();
            $user = $resetModel->findActiveUserByEmail($email);
            $token = $user ? bin2hex(random_bytes(32)) : null;
            $requestId = $resetModel->recordRequest(
                hash('sha256', $email),
                (string)($_SERVER['REMOTE_ADDR'] ?? ''),
                $user ? (int)$user['id'] : null,
                $token ? hash('sha256', $token) : null
            );

            if ($requestId !== null && $user && $token) {
                $resetUrl = rtrim(APP_URL, '/') . '/?page=password.reset&token=' . rawurlencode($token);
                $body = "Halo {$user['nama']},\n\nGunakan tautan berikut untuk mengatur ulang kata sandi akun SiTiTi. Tautan berlaku selama 30 menit dan hanya dapat digunakan sekali.\n\n{$resetUrl}\n\nJika Anda tidak meminta pengaturan ulang kata sandi, abaikan email ini.";
                if (!Mailer::send($email, APP_NAME . ': Atur ulang kata sandi', $body)) {
                    error_log('Email reset kata sandi gagal diserahkan ke mail transport.');
                }
            }
        }

        setFlash('success', $genericMessage);
        redirect('?page=password.forgot');
    }

    public function showResetPassword(): void {
        header('Referrer-Policy: no-referrer');
        header('Cache-Control: no-store, private');
        $token = trim((string)get('token'));
        $validToken = preg_match('/^[a-f0-9]{64}$/', $token) === 1
            && (new PasswordReset())->isTokenValid(hash('sha256', $token));
        $pageTitle = 'Atur Ulang Kata Sandi';
        require BASE_PATH . '/views/auth/reset_password.php';
    }

    public function resetPassword(): void {
        if (!isPost()) redirect('?page=password.forgot');
        verifyCsrf();

        $token = trim((string)post('token'));
        if (preg_match('/^[a-f0-9]{64}$/', $token) !== 1) {
            setFlash('danger', 'Tautan tidak valid atau sudah kedaluwarsa. Minta tautan baru untuk melanjutkan.');
            redirect('?page=password.forgot');
        }

        $password = (string)post('password');
        $passwordConfirm = (string)post('password_confirm');
        $passwordLength = function_exists('mb_strlen') ? mb_strlen($password) : strlen($password);
        if ($passwordLength < 8 || strlen($password) > 72) {
            setFlash('danger', 'Kata sandi harus terdiri dari minimal 8 karakter dan maksimal 72 byte.');
            redirect('?page=password.reset&token=' . rawurlencode($token));
        }
        if (!hash_equals($password, $passwordConfirm)) {
            setFlash('danger', 'Konfirmasi kata sandi tidak sama.');
            redirect('?page=password.reset&token=' . rawurlencode($token));
        }

        $updated = (new PasswordReset())->resetPassword(hash('sha256', $token), $password);
        if (!$updated) {
            setFlash('danger', 'Tautan tidak valid atau sudah kedaluwarsa. Minta tautan baru untuk melanjutkan.');
            redirect('?page=password.forgot');
        }

        setFlash('success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru.');
        redirect('?page=login');
    }

    /**
     * Proses logout
     */
    public function logout(): void {
        Auth::logout();
        setFlash('success', 'Anda berhasil logout.');
        redirect('?page=login');
    }
}
