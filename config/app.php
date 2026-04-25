<?php
/**
 * Konfigurasi Aplikasi
 * File: config/app.php
 */

define('APP_NAME', 'SiTiTi');
define('APP_FULL_NAME', 'Sistem Informasi Pengajuan Judul Skripsi');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/sititi');

// Path absolut ke root aplikasi
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/uploads/proposals');
define('UPLOAD_URL', APP_URL . '/uploads/proposals');

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

// Status alur pengajuan
define('STATUS_DRAFT',        'draft');
define('STATUS_DIAJUKAN',     'diajukan');
define('STATUS_DIVERIFIKASI', 'diverifikasi');
define('STATUS_DIREVIEW',     'direview');
define('STATUS_REVISI',       'revisi');
define('STATUS_DITOLAK',      'ditolak');
define('STATUS_DISETUJUI',    'disetujui');
define('STATUS_PEMBIMBING',   'pembimbing_ditentukan');

// Label status untuk tampilan
define('STATUS_LABELS', [
    'draft'                 => ['label' => 'Draft',                 'class' => 'secondary'],
    'diajukan'              => ['label' => 'Diajukan',              'class' => 'info'],
    'diverifikasi'          => ['label' => 'Diverifikasi',          'class' => 'primary'],
    'direview'              => ['label' => 'Sedang Direview',       'class' => 'warning'],
    'revisi'                => ['label' => 'Perlu Revisi',          'class' => 'warning'],
    'ditolak'               => ['label' => 'Ditolak',               'class' => 'danger'],
    'disetujui'             => ['label' => 'Disetujui',             'class' => 'success'],
    'pembimbing_ditentukan' => ['label' => 'Pembimbing Ditentukan', 'class' => 'success'],
]);
