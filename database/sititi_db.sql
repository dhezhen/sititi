-- SiTiTi Database Backup
-- Generated: 2026-04-25 10:16:10
-- Host: localhost
-- Database: sititi_db

use sititi_db


SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `dosen`;
CREATE TABLE `dosen` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nidn` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prodi_id` int unsigned DEFAULT NULL,
  `bidang_keahlian` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan_akademik` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nidn` (`nidn`),
  UNIQUE KEY `uq_nidn` (`nidn`),
  KEY `fk_dosen_prodi` (`prodi_id`),
  CONSTRAINT `fk_dosen_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `dosen`
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('1', 'Dr. Ir. Bambang Susilo, M.T.', '0101010101', 'kaprodi@demo.com', '1', 'Kecerdasan Buatan', NULL, NULL, '082111111111', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('2', 'Dr. Rina Marlina, M.Kom.', '0202020202', 'rina@univ.ac.id', '1', 'Rekayasa Perangkat Lunak', NULL, NULL, '082222222222', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('3', 'Dr. Hendra Gunawan, M.T.', '0303030303', 'hendra@univ.ac.id', '2', 'Sistem Informasi', NULL, NULL, '082333333333', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('4', 'Prof. Dr. Slamet Riyanto, M.T.', '0404040404', 'slamet@univ.ac.id', '1', 'Jaringan Komputer', NULL, NULL, '082444444444', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('5', 'Dr. Naning Priyatni, M.Kom.', '0505050505', 'naning@univ.ac.id', '2', 'Basis Data', NULL, NULL, '082555555555', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('6', 'Dr. Fajar Nugroho, M.T.', '0606060606', 'fajar@univ.ac.id', '3', 'Computer Vision', NULL, NULL, '082666666666', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('7', 'Dr. Yuni Kartika, M.Kom.', '0707070707', 'yuni@univ.ac.id', '1', 'Keamanan Sistem', NULL, NULL, '082777777777', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('8', 'Dr. Agus Prabowo, M.T.', '0808080808', 'agus@univ.ac.id', '2', 'Internet of Things', NULL, NULL, '082888888888', '1');

DROP TABLE IF EXISTS `mahasiswa`;
CREATE TABLE `mahasiswa` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan` year NOT NULL,
  `semester` tinyint unsigned DEFAULT NULL,
  `prodi_id` int unsigned DEFAULT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nim` (`nim`),
  UNIQUE KEY `uq_nim` (`nim`),
  KEY `fk_mhs_user` (`user_id`),
  KEY `fk_mhs_prodi` (`prodi_id`),
  CONSTRAINT `fk_mhs_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mhs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `mahasiswa`
INSERT INTO `mahasiswa` (`id`, `user_id`, `nim`, `angkatan`, `semester`, `prodi_id`, `no_hp`, `alamat`) VALUES ('6', '11', '2021002', '2021', NULL, '1', NULL, NULL);
INSERT INTO `mahasiswa` (`id`, `user_id`, `nim`, `angkatan`, `semester`, `prodi_id`, `no_hp`, `alamat`) VALUES ('9', '14', '20230810037', '2023', '1', '1', '02152151585656', 'cIKIJING');

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_page` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_page` (`kode_page`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `menus`
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('1', 'pengajuan', 'Daftar Pengajuan', 'bi bi-file-earmark-text', '10');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('2', 'pengajuan.create', 'Buat Pengajuan', 'bi bi-plus-circle', '11');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('3', 'staff', 'Verifikasi Berkas', 'bi bi-clipboard-check', '20');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('4', 'admin.mahasiswa', 'Data Mahasiswa', 'bi bi-people', '21');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('5', 'admin.dosen', 'Data Dosen', 'bi bi-person-badge', '22');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('6', 'kaprodi', 'Review Pengajuan', 'bi bi-eye', '30');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('7', 'admin.periode', 'Data Periode', 'bi bi-calendar3', '31');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('8', 'dewan', 'Tentukan Pembimbing', 'bi bi-people-fill', '40');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('9', 'superadmin.roles', 'Manajemen Role', 'bi bi-shield-lock', '90');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('10', 'superadmin.menus', 'Manajemen Menu', 'bi bi-list-nested', '91');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('21', 'superadmin.database', 'Backup Database', 'bi bi-database', '92');

DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `pengajuan_id` int unsigned DEFAULT NULL,
  `pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`,`is_read`),
  KEY `idx_notif_created` (`created_at`),
  KEY `fk_notif_pengajuan` (`pengajuan_id`),
  CONSTRAINT `fk_notif_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `notifikasi`
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('5', '5', NULL, 'Pengajuan baru dari Ahmad Fauzi telah diverifikasi, menunggu review Anda.', '1', '2026-04-22 11:53:17');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('6', '6', NULL, 'Pengajuan Pengembangan Sistem Informasi Perpustakaan disetujui. Tentukan pembimbing.', '0', '2026-04-20 11:53:17');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('8', '4', '8', 'Pengajuan baru dari ADI WIJAYA menunggu verifikasi.', '1', '2026-04-25 16:07:42');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('9', '5', '8', 'Pengajuan \'Pengembangan Model Kecerdasan Buatan Berbasis Analisis Citra Makanan dan Data Kesehatan Non-Invasif untuk Prediksi Risiko Penyakit Metabolik\' telah diverifikasi, menunggu review Anda.', '1', '2026-04-25 16:08:23');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('10', '14', '8', 'Pengajuan Anda telah diverifikasi dan diteruskan ke Kaprodi.', '0', '2026-04-25 16:08:23');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('11', '6', '8', 'Pengajuan \'Pengembangan Model Kecerdasan Buatan Berbasis Analisis Citra Makanan dan Data Kesehatan Non-Invasif untuk Prediksi Risiko Penyakit Metabolik\' disetujui. Tentukan pembimbing.', '1', '2026-04-25 16:09:07');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('12', '7', '8', 'Pengajuan \'Pengembangan Model Kecerdasan Buatan Berbasis Analisis Citra Makanan dan Data Kesehatan Non-Invasif untuk Prediksi Risiko Penyakit Metabolik\' disetujui. Tentukan pembimbing.', '0', '2026-04-25 16:09:07');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('13', '14', '8', 'Judul skripsi Anda disetujui Kaprodi!', '0', '2026-04-25 16:09:07');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('14', '14', '8', 'Dosen pembimbing skripsi Anda telah ditentukan. Segera hubungi pembimbing Anda.', '0', '2026-04-25 16:10:05');

DROP TABLE IF EXISTS `pembimbing`;
CREATE TABLE `pembimbing` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` int unsigned NOT NULL,
  `dosen_id` int unsigned NOT NULL,
  `urutan` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1=Pembimbing Utama, 2=Pembimbing Pendamping',
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pengajuan_urutan` (`pengajuan_id`,`urutan`),
  KEY `fk_pb_dosen` (`dosen_id`),
  CONSTRAINT `fk_pb_dosen` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pb_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pengajuan_judul`;
CREATE TABLE `pengajuan_judul` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `periode_id` int unsigned DEFAULT NULL,
  `judul` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `bidang` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_proposal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama file PDF di folder uploads/proposals',
  `status` enum('draft','diajukan','diverifikasi','direview','revisi','ditolak','disetujui','pembimbing_ditentukan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `catatan_terakhir` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan dari petugas terakhir',
  `verified_at` datetime DEFAULT NULL COMMENT 'Waktu diverifikasi Staff',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_periode` (`periode_id`),
  CONSTRAINT `fk_pj_periode` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pj_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `pengajuan_judul`
INSERT INTO `pengajuan_judul` (`id`, `user_id`, `periode_id`, `judul`, `deskripsi`, `bidang`, `file_proposal`, `status`, `catatan_terakhir`, `verified_at`, `created_at`, `updated_at`) VALUES ('8', '14', '4', 'Pengembangan Model Kecerdasan Buatan Berbasis Analisis Citra Makanan dan Data Kesehatan Non-Invasif untuk Prediksi Risiko Penyakit Metabolik', 'dsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdfdsfdassdfsdfsdfsafdsasfsdfsddsafsdfsdfdsfsdf', 'Computer Vision', 'proposal_20260425090729_058619d6.pdf', 'pembimbing_ditentukan', 'oke disetujui', '2026-04-25 16:08:23', '2026-04-25 16:07:29', '2026-04-25 16:10:05');

DROP TABLE IF EXISTS `periode`;
CREATE TABLE `periode` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `periode`
INSERT INTO `periode` (`id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `is_active`, `created_at`) VALUES ('4', 'Genap 2025/2026', '2026-02-01', '2026-06-30', '1', '2026-04-25 11:53:17');

DROP TABLE IF EXISTS `prodi`;
CREATE TABLE `prodi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenjang` enum('D3','S1','S2','S3') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `prodi`
INSERT INTO `prodi` (`id`, `kode`, `nama`, `jenjang`) VALUES ('1', 'TI', 'Teknik Informatika', 'S1');
INSERT INTO `prodi` (`id`, `kode`, `nama`, `jenjang`) VALUES ('2', 'TS', 'Teknik Sipil', 'S1');
INSERT INTO `prodi` (`id`, `kode`, `nama`, `jenjang`) VALUES ('3', 'DKV', 'Desain Komunikasi Visual', 'S1');
INSERT INTO `prodi` (`id`, `kode`, `nama`, `jenjang`) VALUES ('4', 'SI', 'Sistem Informasi', 'S1');

DROP TABLE IF EXISTS `revisi`;
CREATE TABLE `revisi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL COMMENT 'Siapa yang memberi catatan revisi',
  `catatan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_revisi_pengajuan` (`pengajuan_id`),
  KEY `fk_revisi_user` (`user_id`),
  CONSTRAINT `fk_revisi_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_revisi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `role_menus`;
CREATE TABLE `role_menus` (
  `role_id` int unsigned NOT NULL,
  `menu_id` int unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`menu_id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `role_menus_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_menus_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `role_menus`
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('1', '1');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '1');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('1', '2');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '2');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('2', '3');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '3');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('2', '4');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '4');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('2', '5');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '5');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('4', '5');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '5');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '6');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '6');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '7');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '7');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('4', '8');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '8');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '9');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '10');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '21');

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `roles`
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('1', 'mahasiswa', 'Mahasiswa');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('2', 'staff', 'Staff / Verifikator');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('3', 'kaprodi', 'Ketua Program Studi');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('4', 'dewan', 'Dewan Pembimbing');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('5', 'superadmin', 'Super Admin');

DROP TABLE IF EXISTS `status_log`;
CREATE TABLE `status_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` int unsigned NOT NULL,
  `status_lama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status_baru` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sl_pengajuan` (`pengajuan_id`),
  KEY `fk_sl_user` (`user_id`),
  CONSTRAINT `fk_sl_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `status_log`
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('18', '8', '', 'draft', '14', 'Pengajuan dibuat.', '2026-04-25 16:07:29');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('19', '8', 'draft', 'diajukan', '14', 'Pengajuan diajukan oleh mahasiswa.', '2026-04-25 16:07:42');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('20', '8', 'diajukan', 'diverifikasi', '4', 'OKe lanjut', '2026-04-25 16:08:23');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('21', '8', 'diverifikasi', 'disetujui', '5', 'Oke lanjut', '2026-04-25 16:09:07');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('22', '8', 'disetujui', 'pembimbing_ditentukan', '6', 'oke disetujui', '2026-04-25 16:10:05');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mahasiswa',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `users`
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('4', 'Alifa', 'alifa@uniku.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '1', '2026-04-25 11:53:17');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('5', 'Dr. Andi Wijaya', 'kaprodi@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kaprodi', '1', '2026-04-25 11:53:17');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('6', 'Prof. Rudi Hartono', 'dewan@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dewan', '1', '2026-04-25 11:53:17');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('7', 'Dr. Lestari Putri', 'dewan2@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dewan', '1', '2026-04-25 11:53:17');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('8', 'Super Admin', 'superadmin@uniku.ac.id', '$2y$10$5uKVtYShge6hCA5SW3r1KebLn6HAz64e.oIg/Vr8J9Vxuj.WC7P6e', 'superadmin', '1', '2026-04-25 13:31:04');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('11', 'Siti Aminah', 'siti@uniku.ac.id', '$2y$10$eyWZht.YMHyqchRKHcUt0ul0ukwmlMi1E8DVL12IgmQgEBWPOfATu', 'mahasiswa', '1', '2026-04-25 15:54:11');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('14', 'ADI WIJAYA', 'dede@uniku.ac.id', '$2y$10$5kb/jkN/Fi0LWTivwDK4Tek.Y/ApkrKPF6BoCrK6b0Vn4l/1MD1FK', 'mahasiswa', '1', '2026-04-25 16:06:03');

SET FOREIGN_KEY_CHECKS=1;
