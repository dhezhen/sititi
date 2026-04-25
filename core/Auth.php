<?php
/**
 * Kelas Auth - Manajemen Autentikasi & Sesi
 * File: core/Auth.php
 */

class Auth {

    /**
     * Mulai sesi jika belum dimulai
     */
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set user ke sesi setelah login berhasil
     */
    public static function login(array $user): void {
        self::startSession();
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_email']= $user['email'];
        $_SESSION['user_role'] = $user['role'];
        session_regenerate_id(true); // Cegah session fixation
    }

    /**
     * Hapus sesi dan logout
     */
    public static function logout(): void {
        self::startSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Cek apakah user sudah login
     */
    public static function check(): bool {
        self::startSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Ambil data user dari sesi
     */
    public static function user(): ?array {
        if (!self::check()) return null;
        return [
            'id'    => $_SESSION['user_id'],
            'nama'  => $_SESSION['user_nama'],
            'email' => $_SESSION['user_email'],
            'role'  => $_SESSION['user_role'],
        ];
    }

    /**
     * Ambil ID user yang sedang login
     */
    public static function id(): ?int {
        return self::check() ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * Ambil role user yang sedang login
     */
    public static function role(): ?string {
        return self::check() ? $_SESSION['user_role'] : null;
    }

    /**
     * Cek apakah user memiliki role tertentu
     * @param string|array $roles
     */
    public static function hasRole(string|array $roles): bool {
        if (!self::check()) return false;
        $roles = (array)$roles;
        return in_array($_SESSION['user_role'], $roles);
    }

    /**
     * Paksa user untuk login jika belum
     */
    public static function requireLogin(): void {
        if (!self::check()) {
            redirect('?page=login');
        }
    }

    /**
     * Paksa user memiliki role tertentu
     * @param string|array $roles
     */
    public static function requireRole(string|array $roles): void {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            redirect('?page=403');
        }
    }

    /**
     * Redirect ke dashboard sesuai role
     */
    public static function redirectToDashboard(): void {
        $role = self::role();
        $pages = [
            ROLE_MAHASISWA => '?page=dashboard',
            ROLE_STAFF     => '?page=dashboard',
            ROLE_KAPRODI   => '?page=dashboard',
            ROLE_DEWAN     => '?page=dashboard',
        ];
        redirect($pages[$role] ?? '?page=dashboard');
    }
}
