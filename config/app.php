<?php
/**
 * Konfigurasi Aplikasi
 * File: config/app.php
 */

define('APP_NAME', 'Sistem Informasi Pengajuan Judul Skripsi');
define('APP_FULL_NAME', 'Sistem Informasi Pengajuan Judul Skripsi');
define('APP_VERSION', '1.0.0');
$configuredAppUrl = getenv('APP_URL');
if ($configuredAppUrl === false || trim($configuredAppUrl) === '') {
    $configuredAppUrl = $_SERVER['APP_URL'] ?? 'http://localhost/sititi';
}
define('APP_URL', rtrim(trim($configuredAppUrl), '/'));

// Path absolut ke root aplikasi
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/uploads/proposals');
define('UPLOAD_URL', APP_URL . '/uploads/proposals');
define('UPLOAD_PATH_AVATAR', BASE_PATH . '/uploads/avatars');
define('AVATAR_URL', APP_URL . '/uploads/avatars');

// Ukuran maksimal upload (5MB)
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Tipe file yang diizinkan
define('ALLOWED_UPLOAD_TYPES', ['application/pdf']);

// Definisi role
define('ROLE_MAHASISWA',  'mahasiswa');
define('ROLE_STAFF',      'staff');
define('ROLE_KAPRODI',    'kaprodi');
define('ROLE_DEWAN',      'dewan');
define('ROLE_SUPERADMIN', 'superadmin');
define('ROLE_DOSEN',      'dosen');

// Konfigurasi Bimbingan
define('MAX_PEMBIMBING_PER_ANGKATAN', 20);

// Status alur pengajuan
define('STATUS_DRAFT',        'draft');
define('STATUS_DIAJUKAN',     'diajukan');
define('STATUS_DIVERIFIKASI', 'diverifikasi');
define('STATUS_DIREVIEW',     'direview');       // Kaprodi assign advisor, wait for Dewan
define('STATUS_DIREVIEW_DEWAN', 'direview_dewan'); // Dewan done, wait for Kaprodi Final
define('STATUS_REVISI',       'revisi');
define('STATUS_DITOLAK',      'ditolak');
define('STATUS_DISETUJUI',    'disetujui');      // Kaprodi Final Approved
define('STATUS_PEMBIMBING',   'pembimbing_ditentukan'); // Diajukan ke Fakultas
define('STATUS_SELESAI',      'selesai');        // SK Siap Dicetak
define('STATUS_SELESAI_FAKULTAS', 'selesai_fakultas'); // Selesai Diajukan ke Fakultas
define('STATUS_SK_TERBIT',    'sk_terbit');      // SK Telah Terbit

// Label status untuk tampilan
define('STATUS_LABELS', [
    'draft'                 => ['label' => 'Draft',                 'class' => 'secondary'],
    'diajukan'              => ['label' => 'Menunggu Masukan Pembimbing', 'class' => 'info'],
    'diverifikasi'          => ['label' => 'Diverifikasi Staff',    'class' => 'primary'],
    'direview'              => ['label' => 'Menunggu Review Dewan', 'class' => 'warning'],
    'direview_dewan'        => ['label' => 'Telah Direview Dewan',  'class' => 'info'],
    'revisi'                => ['label' => 'Perlu Revisi',          'class' => 'warning'],
    'ditolak'               => ['label' => 'Ditolak',               'class' => 'danger'],
    'disetujui'             => ['label' => 'Disetujui Kaprodi',     'class' => 'success'],
    'pembimbing_ditentukan' => ['label' => 'Selesai di Tingkat Prodi', 'class' => 'success'],
    'selesai'               => ['label' => 'Siap Diajukan ke Fakultas', 'class' => 'primary'],
    'selesai_fakultas'      => ['label' => 'Selesai Diajukan ke Fakultas', 'class' => 'info'],
    'sk_terbit'             => ['label' => 'SK Telah Terbit',       'class' => 'success'],
]);
