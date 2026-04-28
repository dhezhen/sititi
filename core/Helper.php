<?php
/**
 * Fungsi-fungsi pembantu global
 * File: core/Helper.php
 */

/**
 * Redirect ke URL tertentu
 */
function redirect(string $url): never {
    header("Location: $url");
    exit;
}

/**
 * Sanitasi input dari pengguna (mencegah XSS)
 */
function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Ambil nilai POST dengan aman
 */
function post(string $key, mixed $default = ''): mixed {
    return $_POST[$key] ?? $default;
}

/**
 * Ambil nilai GET dengan aman
 */
function get(string $key, mixed $default = ''): mixed {
    return $_GET[$key] ?? $default;
}

/**
 * Set flash message ke sesi
 */
function setFlash(string $type, string $message): void {
    Auth::startSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Ambil dan hapus flash message dari sesi
 */
function getFlash(): ?array {
    Auth::startSession();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render flash message sebagai SweetAlert Popup
 */
function renderFlash(): void {
    $flash = getFlash();
    if ($flash) {
        $type    = e($flash['type']);
        $message = e($flash['message']);
        
        // Map bootstrap types to SweetAlert types
        $swalType = $type;
        if ($type === 'danger') $swalType = 'error';
        
        // Map type to title
        $titles = [
            'success' => 'Berhasil!',
            'error'   => 'Gagal!',
            'warning' => 'Perhatian!',
            'info'    => 'Informasi',
        ];
        $title = $titles[$swalType] ?? 'Notifikasi';
        
        echo <<<HTML
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: '{$swalType}',
                        title: '{$title}',
                        text: '{$message}',
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: 'OK',
                        timer: 4000,
                        timerProgressBar: true
                    });
                }
            });
        </script>
        HTML;
    }
}


/**
 * Generate badge Bootstrap berdasarkan status pengajuan
 */
function statusBadge(string $status): string {
    $labels = STATUS_LABELS;
    $info   = $labels[$status] ?? ['label' => ucfirst($status), 'class' => 'secondary'];
    return sprintf(
        '<span class="badge bg-%s">%s</span>',
        e($info['class']),
        e($info['label'])
    );
}

/**
 * Format tanggal ke format Indonesia
 */
function formatTanggal(string $date, bool $withTime = false): string {
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $ts    = strtotime($date);
    $d     = date('j', $ts);
    $m     = (int)date('n', $ts);
    $y     = date('Y', $ts);
    $str   = "$d {$bulan[$m]} $y";
    if ($withTime) {
        $str .= ' ' . date('H:i', $ts) . ' WIB';
    }
    return $str;
}

/**
 * Upload file PDF proposal
 * @return array ['success'=>bool, 'filename'=>string, 'error'=>string]
 */
function uploadProposal(array $file): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Terjadi kesalahan saat upload file.'];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'Ukuran file melebihi batas 5MB.'];
    }

    // Validasi tipe MIME secara nyata (bukan hanya ekstensi)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_UPLOAD_TYPES)) {
        return ['success' => false, 'error' => 'Hanya file PDF yang diizinkan.'];
    }

    // Generate nama file unik
    $ext      = 'pdf';
    $filename = 'proposal_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destPath = UPLOAD_PATH . '/' . $filename;

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['success' => false, 'error' => 'Gagal menyimpan file. Periksa izin folder.'];
    }

    return ['success' => true, 'filename' => $filename];
}

/**
 * Upload dan Kompres Gambar Profil
 */
function uploadAndCompressImage(array $file, string $targetDir, int $maxWidth = 300): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Terjadi kesalahan saat upload file.'];
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'Hanya file JPG, JPEG, dan PNG yang diizinkan.'];
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destPath = $targetDir . '/' . $filename;

    // Proses Kompresi
    list($width, $height) = getimagesize($file['tmp_name']);
    $ratio = $width / $height;
    
    if ($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $ratio;
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }

    $src = ($file['type'] == 'image/png') ? imagecreatefrompng($file['tmp_name']) : imagecreatefromjpeg($file['tmp_name']);
    $dst = imagecreatetruecolor($newWidth, $newHeight);

    // Handle transparency for PNG
    if ($file['type'] == 'image/png') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if ($file['type'] == 'image/png') {
        imagepng($dst, $destPath, 6); // Scale 0-9
    } else {
        imagejpeg($dst, $destPath, 80); // Quality 0-100
    }

    imagedestroy($src);
    imagedestroy($dst);

    return ['success' => true, 'filename' => $filename];
}

/**
 * Hitung jumlah notifikasi yang belum dibaca untuk user aktif
 */
function countUnreadNotif(): int {
    if (!Auth::check()) return 0;
    $db   = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_read = 0");
    $stmt->execute([Auth::id()]);
    return (int)$stmt->fetchColumn();
}

/**
 * CSRF Token generator & validator
 */
function csrfToken(): string {
    Auth::startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): void {
    echo '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(): void {
    if (!hash_equals(csrfToken(), post('csrf_token', ''))) {
        http_response_code(403);
        die('CSRF token tidak valid.');
    }
}

/**
 * Truncate string dengan ellipsis
 */
function truncate(string $text, int $limit = 100): string {
    if (mb_strlen($text) <= $limit) return $text;
    return mb_substr($text, 0, $limit) . '...';
}

/**
 * Cek apakah request adalah POST
 */
function isPost(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
