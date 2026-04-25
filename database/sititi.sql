-- ============================================================
-- SiTiTi — Sistem Informasi Pengajuan Judul Skripsi
-- Database Schema & Seed Data
-- File: database/sititi.sql
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET FOREIGN_KEY_CHECKS = 0;

-- Buat dan gunakan database
CREATE DATABASE IF NOT EXISTS `sititi_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `sititi_db`;

-- ============================================================
-- TABEL: users
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama`       VARCHAR(150) NOT NULL,
    `email`      VARCHAR(150) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `role`       ENUM('mahasiswa','staff','kaprodi','dewan') NOT NULL DEFAULT 'mahasiswa',
    `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_email` (`email`),
    INDEX `idx_role`  (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: prodi
-- ============================================================
DROP TABLE IF EXISTS `prodi`;
CREATE TABLE `prodi` (
    `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode`    VARCHAR(20)  NOT NULL,
    `nama`    VARCHAR(100) NOT NULL,
    `jenjang` ENUM('D3','S1','S2','S3') NOT NULL DEFAULT 'S1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: mahasiswa
-- ============================================================
DROP TABLE IF EXISTS `mahasiswa`;
CREATE TABLE `mahasiswa` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`   INT UNSIGNED NOT NULL,
    `nim`       VARCHAR(20)  NOT NULL UNIQUE,
    `angkatan`  YEAR         NOT NULL,
    `semester`  TINYINT UNSIGNED NULL,
    `prodi_id`  INT UNSIGNED NULL,
    `no_hp`     VARCHAR(20)  NULL,
    `alamat`    TEXT         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_nim` (`nim`),
    CONSTRAINT `fk_mhs_user`  FOREIGN KEY (`user_id`)  REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mhs_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: dosen
-- ============================================================
DROP TABLE IF EXISTS `dosen`;
CREATE TABLE `dosen` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama`            VARCHAR(150) NOT NULL,
    `nidn`            VARCHAR(20)  NOT NULL UNIQUE,
    `email`           VARCHAR(150) NULL,
    `prodi_id`        INT UNSIGNED NULL,
    `bidang_keahlian` VARCHAR(200) NULL,
    `no_hp`           VARCHAR(20)  NULL,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_nidn` (`nidn`),
    CONSTRAINT `fk_dosen_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: periode
-- ============================================================
DROP TABLE IF EXISTS `periode`;
CREATE TABLE `periode` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama`            VARCHAR(100) NOT NULL,
    `tanggal_mulai`   DATE         NOT NULL,
    `tanggal_selesai` DATE         NOT NULL,
    `is_active`       TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: pengajuan_judul
-- Alur status: draft → diajukan → diverifikasi → direview
--              → revisi / ditolak / disetujui → pembimbing_ditentukan
-- ============================================================
DROP TABLE IF EXISTS `pengajuan_judul`;
CREATE TABLE `pengajuan_judul` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`          INT UNSIGNED NOT NULL,
    `periode_id`       INT UNSIGNED NULL,
    `judul`            VARCHAR(300) NOT NULL,
    `deskripsi`        TEXT         NULL,
    `bidang`           VARCHAR(100) NULL,
    `file_proposal`    VARCHAR(255) NULL COMMENT 'Nama file PDF di folder uploads/proposals',
    `status`           ENUM(
                           'draft','diajukan','diverifikasi','direview',
                           'revisi','ditolak','disetujui','pembimbing_ditentukan'
                       ) NOT NULL DEFAULT 'draft',
    `catatan_terakhir` TEXT         NULL COMMENT 'Catatan dari petugas terakhir',
    `verified_at`      DATETIME     NULL COMMENT 'Waktu diverifikasi Staff',
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id`   (`user_id`),
    INDEX `idx_status`    (`status`),
    INDEX `idx_periode`   (`periode_id`),
    CONSTRAINT `fk_pj_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`   (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pj_periode` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: pembimbing
-- ============================================================
DROP TABLE IF EXISTS `pembimbing`;
CREATE TABLE `pembimbing` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pengajuan_id` INT UNSIGNED NOT NULL,
    `dosen_id`     INT UNSIGNED NOT NULL,
    `urutan`       TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=Pembimbing Utama, 2=Pembimbing Pendamping',
    `assigned_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pengajuan_urutan` (`pengajuan_id`, `urutan`),
    CONSTRAINT `fk_pb_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pb_dosen`     FOREIGN KEY (`dosen_id`)     REFERENCES `dosen`           (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: status_log (riwayat perubahan status)
-- ============================================================
DROP TABLE IF EXISTS `status_log`;
CREATE TABLE `status_log` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pengajuan_id` INT UNSIGNED NOT NULL,
    `status_lama`  VARCHAR(50)  NOT NULL DEFAULT '',
    `status_baru`  VARCHAR(50)  NOT NULL,
    `user_id`      INT UNSIGNED NOT NULL,
    `catatan`      TEXT         NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_sl_pengajuan` (`pengajuan_id`),
    CONSTRAINT `fk_sl_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sl_user`      FOREIGN KEY (`user_id`)      REFERENCES `users`           (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: notifikasi
-- ============================================================
DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      INT UNSIGNED NOT NULL,
    `pengajuan_id` INT UNSIGNED NULL,
    `pesan`        TEXT         NOT NULL,
    `is_read`      TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_notif_user`   (`user_id`, `is_read`),
    INDEX `idx_notif_created` (`created_at`),
    CONSTRAINT `fk_notif_user`      FOREIGN KEY (`user_id`)      REFERENCES `users`           (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_notif_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: revisi (catatan revisi detail per pengajuan)
-- ============================================================
DROP TABLE IF EXISTS `revisi`;
CREATE TABLE `revisi` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pengajuan_id` INT UNSIGNED NOT NULL,
    `user_id`      INT UNSIGNED NOT NULL COMMENT 'Siapa yang memberi catatan revisi',
    `catatan`      TEXT         NOT NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_revisi_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_revisi_user`      FOREIGN KEY (`user_id`)      REFERENCES `users`           (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA
-- Password untuk semua akun demo: "password"
-- Hash bcrypt untuk "password":
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================================

-- Program Studi
INSERT INTO `prodi` (`id`, `kode`, `nama`, `jenjang`) VALUES
(1, 'IF',  'Teknik Informatika',            'S1'),
(2, 'SI',  'Sistem Informasi',              'S1'),
(3, 'TI',  'Teknologi Informasi',           'S1'),
(4, 'SK',  'Sistem Komputer',               'S1'),
(5, 'MTI', 'Magister Teknik Informatika',   'S2');

-- Users (password = "password")
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES
(1, 'Budi Santoso',      'mahasiswa@demo.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', 1, NOW()),
(2, 'Siti Rahayu',       'mahasiswa2@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', 1, NOW()),
(3, 'Ahmad Fauzi',       'mahasiswa3@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', 1, NOW()),
(4, 'Dewi Kusuma',       'staff@demo.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff',     1, NOW()),
(5, 'Dr. Andi Wijaya',   'kaprodi@demo.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kaprodi',   1, NOW()),
(6, 'Prof. Rudi Hartono','dewan@demo.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dewan',     1, NOW()),
(7, 'Dr. Lestari Putri', 'dewan2@demo.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dewan',     1, NOW());

-- Mahasiswa
INSERT INTO `mahasiswa` (`user_id`, `nim`, `angkatan`, `semester`, `prodi_id`, `no_hp`) VALUES
(1, '2021001001', 2021, 7, 1, '081234567890'),
(2, '2021001002', 2021, 7, 1, '081234567891'),
(3, '2022001001', 2022, 5, 2, '081234567892');

-- Dosen
INSERT INTO `dosen` (`nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `no_hp`, `is_active`) VALUES
('Dr. Ir. Bambang Susilo, M.T.',    '0101010101', 'bambang@univ.ac.id',    1, 'Kecerdasan Buatan',          '082111111111', 1),
('Dr. Rina Marlina, M.Kom.',         '0202020202', 'rina@univ.ac.id',       1, 'Rekayasa Perangkat Lunak',   '082222222222', 1),
('Dr. Hendra Gunawan, M.T.',         '0303030303', 'hendra@univ.ac.id',     2, 'Sistem Informasi',           '082333333333', 1),
('Prof. Dr. Slamet Riyanto, M.T.',   '0404040404', 'slamet@univ.ac.id',     1, 'Jaringan Komputer',          '082444444444', 1),
('Dr. Naning Priyatni, M.Kom.',      '0505050505', 'naning@univ.ac.id',     2, 'Basis Data',                 '082555555555', 1),
('Dr. Fajar Nugroho, M.T.',          '0606060606', 'fajar@univ.ac.id',      3, 'Computer Vision',            '082666666666', 1),
('Dr. Yuni Kartika, M.Kom.',         '0707070707', 'yuni@univ.ac.id',       1, 'Keamanan Sistem',            '082777777777', 1),
('Dr. Agus Prabowo, M.T.',           '0808080808', 'agus@univ.ac.id',       2, 'Internet of Things',         '082888888888', 1);

-- Periode
INSERT INTO `periode` (`nama`, `tanggal_mulai`, `tanggal_selesai`, `is_active`) VALUES
('Ganjil 2024/2025',  '2024-08-01', '2024-12-31', 0),
('Genap 2024/2025',   '2025-02-01', '2025-06-30', 0),
('Ganjil 2025/2026',  '2025-08-01', '2025-12-31', 0),
('Genap 2025/2026',   '2026-02-01', '2026-06-30', 1);

-- Pengajuan Contoh
INSERT INTO `pengajuan_judul`
    (`user_id`, `periode_id`, `judul`, `deskripsi`, `bidang`, `status`, `catatan_terakhir`, `created_at`, `updated_at`)
VALUES
-- Mahasiswa 1: sudah ada pembimbing
(1, 4,
 'Implementasi Machine Learning untuk Prediksi Nilai Akademik Mahasiswa Menggunakan Algoritma Random Forest',
 'Prediksi nilai mahasiswa sangat penting untuk intervensi dini. Penelitian ini membangun model ML menggunakan algoritma Random Forest dengan dataset historis nilai 5 tahun terakhir untuk memprediksi risiko mahasiswa tidak lulus tepat waktu.',
 'Kecerdasan Buatan', 'pembimbing_ditentukan', 'Pembimbing telah ditentukan. Segera hubungi pembimbing Anda.',
 DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Mahasiswa 2: disetujui kaprodi, menunggu pembimbing
(2, 4,
 'Pengembangan Sistem Informasi Manajemen Perpustakaan Digital Berbasis Web dengan Fitur QR Code',
 'Perpustakaan masih menggunakan sistem manual yang tidak efisien. Sistem ini membangun aplikasi perpustakaan digital berbasis web yang dilengkapi pemindaian QR Code untuk proses peminjaman dan pengembalian buku secara cepat dan akurat.',
 'Sistem Informasi', 'disetujui', 'Judul disetujui. Tunggu penentuan pembimbing dari Dewan Bimbingan.',
 DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- Mahasiswa 3: diverifikasi, menunggu review kaprodi
(3, 4,
 'Rancang Bangun Aplikasi Mobile Monitoring Kesehatan Ibu Hamil Berbasis Android dengan Notifikasi Real-Time',
 'Pemantauan kesehatan ibu hamil di daerah terpencil masih menjadi tantangan. Aplikasi ini memungkinkan bidan dan dokter memantau kondisi ibu hamil secara remote melalui smartphone dengan notifikasi otomatis jika ada kondisi darurat.',
 'Mobile Computing', 'diverifikasi', NULL,
 DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Mahasiswa 1: draft kedua (belum diajukan)
(1, 4,
 'Analisis Sentimen Ulasan Produk E-Commerce Menggunakan Deep Learning BERT',
 'Ulasan produk pada platform e-commerce mengandung informasi berharga. Penelitian ini menganalisis sentimen ulasan menggunakan model BERT yang di-fine-tune pada dataset ulasan berbahasa Indonesia.',
 'Kecerdasan Buatan', 'draft', NULL,
 DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Status Log
INSERT INTO `status_log` (`pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES
-- Pengajuan 1 (full flow)
(1, '',              'draft',                 1, 'Pengajuan dibuat.',                                  DATE_SUB(NOW(), INTERVAL 30 DAY)),
(1, 'draft',         'diajukan',              1, 'Pengajuan diajukan oleh mahasiswa.',                 DATE_SUB(NOW(), INTERVAL 28 DAY)),
(1, 'diajukan',      'diverifikasi',          4, 'Berkas dinyatakan lengkap.',                         DATE_SUB(NOW(), INTERVAL 25 DAY)),
(1, 'diverifikasi',  'direview',              5, 'Sedang direview Kaprodi.',                           DATE_SUB(NOW(), INTERVAL 20 DAY)),
(1, 'direview',      'disetujui',             5, 'Judul relevan dan layak dilanjutkan.',               DATE_SUB(NOW(), INTERVAL 15 DAY)),
(1, 'disetujui',     'pembimbing_ditentukan', 6, 'Pembimbing telah ditentukan.',                       DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Pengajuan 2
(2, '',              'draft',                 2, 'Pengajuan dibuat.',                                  DATE_SUB(NOW(), INTERVAL 20 DAY)),
(2, 'draft',         'diajukan',              2, 'Diajukan mahasiswa.',                                DATE_SUB(NOW(), INTERVAL 18 DAY)),
(2, 'diajukan',      'diverifikasi',          4, 'Berkas lengkap dan valid.',                          DATE_SUB(NOW(), INTERVAL 14 DAY)),
(2, 'diverifikasi',  'direview',              5, 'Sedang direview.',                                   DATE_SUB(NOW(), INTERVAL 10 DAY)),
(2, 'direview',      'disetujui',             5, 'Disetujui. Tunggu penentuan pembimbing.',            DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- Pengajuan 3
(3, '',              'draft',                 3, 'Pengajuan dibuat.',                                  DATE_SUB(NOW(), INTERVAL 10 DAY)),
(3, 'draft',         'diajukan',              3, 'Diajukan mahasiswa.',                                DATE_SUB(NOW(), INTERVAL 8 DAY)),
(3, 'diajukan',      'diverifikasi',          4, 'Berkas lengkap, diteruskan ke Kaprodi.',             DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Pengajuan 4 (draft)
(4, '',              'draft',                 1, 'Draft dibuat.',                                      DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Pembimbing untuk Pengajuan 1
INSERT INTO `pembimbing` (`pengajuan_id`, `dosen_id`, `urutan`, `assigned_at`) VALUES
(1, 1, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 2, 2, DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Notifikasi contoh
INSERT INTO `notifikasi` (`user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES
(1, 1, 'Selamat! Dosen pembimbing skripsi Anda telah ditentukan. Segera hubungi pembimbing Anda.', 0, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 1, 'Judul skripsi Anda telah disetujui oleh Kaprodi.', 1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(2, 2, 'Judul skripsi Anda telah disetujui oleh Kaprodi. Menunggu penentuan pembimbing.', 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 3, 'Berkas Anda telah diverifikasi dan diteruskan ke Kaprodi.', 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 3, 'Pengajuan baru dari Ahmad Fauzi telah diverifikasi, menunggu review Anda.', 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(6, 2, 'Pengajuan Pengembangan Sistem Informasi Perpustakaan disetujui. Tentukan pembimbing.', 0, DATE_SUB(NOW(), INTERVAL 5 DAY));
