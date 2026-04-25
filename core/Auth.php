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
     * Mengecek apakah user yang sedang login memiliki akses ke suatu page (menu)
     */
    public static function hasMenuAccess(string $page): bool {
        if (!self::check()) return false;
        
        // Superadmin bypass
        if (self::role() === ROLE_SUPERADMIN) return true;

        $db = getDB();
        
        // Cek apakah $page ini ada di tabel menus
        // Karena ada sub-page seperti 'admin.mahasiswa.store', kita perlu mengambil prefix utamanya.
        // Asumsi: jika prefix utamanya ada di database, kita cek izinnya.
        // Misal: $page = 'admin.mahasiswa.store', kita split '.' menjadi 'admin.mahasiswa'.
        // Namun, beberapa $page memang 3 segment. Lebih aman kita cek exact $page dulu.
        // Jika tidak ada, kita coba cek 2 segment pertamanya, lalu 1 segment.

        $segments = explode('.', $page);
        $checkPages = [$page];
        if (count($segments) == 3) {
            $checkPages[] = $segments[0] . '.' . $segments[1];
        }
        $checkPages[] = $segments[0];

        // Cari menu_id dari database yang paling spesifik
        $menuId = null;
        foreach ($checkPages as $cp) {
            $stmt = $db->prepare("SELECT id FROM menus WHERE kode_page = ?");
            $stmt->execute([$cp]);
            $menuId = $stmt->fetchColumn();
            if ($menuId) break;
        }

        // Jika page ini tidak diregistrasi di tabel menus, defaultnya kita izinkan? 
        // Lebih baik kita block jika tidak di-handle di tabel menus?
        // Tapi ada page seperti 'profil' yang tidak ada di menus.
        // Sebaiknya, jika TIDAK ADA di menus, artinya bukan menu dinamis, akses dilanjutkan (atau di-handle oleh Controller masing-masing).
        if (!$menuId) {
            return true;
        }

        // Cek tabel role_menus
        $stmt = $db->prepare("
            SELECT 1 FROM role_menus rm 
            JOIN roles r ON rm.role_id = r.id 
            WHERE r.kode = ? AND rm.menu_id = ?
        ");
        $stmt->execute([self::role(), $menuId]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Paksa akses berdasarkan menu dinamis
     */
    public static function requireMenuAccess(string $page): void {
        self::requireLogin();
        if (!self::hasMenuAccess($page)) {
            redirect('?page=403');
        }
    }

    /**
     * Redirect ke dashboard sesuai role
     */
    public static function redirectToDashboard(): void {
        $role = self::role();
        $pages = [
            ROLE_MAHASISWA  => '?page=dashboard',
            ROLE_STAFF      => '?page=dashboard',
            ROLE_KAPRODI    => '?page=dashboard',
            ROLE_DEWAN      => '?page=dashboard',
            ROLE_SUPERADMIN => '?page=dashboard',
        ];
        redirect($pages[$role] ?? '?page=dashboard');
    }
}
