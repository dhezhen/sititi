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

    /**
     * Proses logout
     */
    public function logout(): void {
        Auth::logout();
        setFlash('success', 'Anda berhasil logout.');
        redirect('?page=login');
    }
}
