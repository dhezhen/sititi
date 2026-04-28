-- SiTiTi Database Backup
-- Generated: 2026-04-28 15:07:00
-- Host: 127.0.0.1:3307
-- Database: sititi_db

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `catatan_bimbingan`;
CREATE TABLE `catatan_bimbingan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` int unsigned NOT NULL,
  `dosen_id` int unsigned NOT NULL,
  `catatan` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pengajuan_id` (`pengajuan_id`),
  KEY `dosen_id` (`dosen_id`),
  CONSTRAINT `catatan_bimbingan_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE,
  CONSTRAINT `catatan_bimbingan_ibfk_2` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data for table `catatan_bimbingan`
INSERT INTO `catatan_bimbingan` (`id`, `pengajuan_id`, `dosen_id`, `catatan`, `created_at`) VALUES ('2', '3', '1', 'Proposal sudah OK, namun harus ada sedikit perbaikan\r\nlanjutkan', '2026-04-28 20:17:02');
INSERT INTO `catatan_bimbingan` (`id`, `pengajuan_id`, `dosen_id`, `catatan`, `created_at`) VALUES ('3', '3', '9', 'Proposal Sudah baik, namun perlu perbaikan sementara, untuk judul sebaiknya ditekankanjenis algoritma yang dipilih', '2026-04-28 20:18:34');
INSERT INTO `catatan_bimbingan` (`id`, `pengajuan_id`, `dosen_id`, `catatan`, `created_at`) VALUES ('4', '4', '9', 'Oke lanjutkan,,', '2026-04-28 21:07:08');
INSERT INTO `catatan_bimbingan` (`id`, `pengajuan_id`, `dosen_id`, `catatan`, `created_at`) VALUES ('5', '4', '1', 'Okkey', '2026-04-28 21:07:45');

DROP TABLE IF EXISTS `dosen`;
CREATE TABLE `dosen` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nidn` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prodi_id` int unsigned DEFAULT NULL,
  `bidang_keahlian` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan_akademik` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nidn` (`nidn`),
  UNIQUE KEY `uq_nidn` (`nidn`),
  KEY `fk_dosen_prodi` (`prodi_id`),
  CONSTRAINT `fk_dosen_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `dosen`
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('1', 'Dede Husen, M.Kom', '0404059205', 'dede.husen@uniku.ac.id', '1', 'Kecerdasan Buatan', 'Asisten Ahli', 'S2', '089606941238', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('2', 'Fauziah, M.Kom', '0414107902', 'fauziah.m@uniku.ac.id', '1', '', '', 'S2', '0', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('3', 'Fitra Nugraha, M.Kom', '0412088004', 'fitra.nugraha@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '1', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('4', 'Gentur Priguna Suwarto, M.T.I', '0410118005', 'gentur.priguna suwarto@uniku.ac.id', '1', '-', 'ASISTEN AHLI', 'S2', '2', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('5', 'Heri Herwanto, M.Pd', '0421107802', 'heri.herwanto@uniku.ac.id', '1', '-', 'ASISTEN AHLI', 'S2', '3', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('6', 'Iwan Lesmana, M.Kom', '0418078403', 'iwan.lesmana@uniku.ac.id', '1', 'Computer Vision', 'Lektor', 'S2', '4', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('7', 'Nida Amalia Asikin, M.Pd', '0416098701', 'nida.amalia asikin@uniku.ac.id', '1', '-', 'ASISTEN AHLI', 'S2', '5', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('8', 'Nunu Nugraha, M.T', '0413058502', 'nunu.nugraha@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '6', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('9', 'Rio Andriyat Krisdiawan, M.Kom', '0427048901', 'rio.andriyat@uniku.ac.id', '1', '', '', 'S2', '7', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('10', 'Rio Priantama, M.T.I', '0411098202', 'rio.priantama@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '8', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('11', 'Sherly Gina Supratman, M.Kom', '0406058501', 'sherly.gina.supratman@uniku.ac.id', '1', '-', 'ASISTEN AHLI', 'S2', '9', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('12', 'Siti Maesyaroh, M.Kom', '0409088701', 'siti.maesyaroh@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '10', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('13', 'Tito Sugiharto, M.Eng', '0429078702', 'tito@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '11', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('14', 'Yati Nurhayati, M.Kom', '0409048601', 'yati.nurhayati@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '12', '1');

DROP TABLE IF EXISTS `mahasiswa`;
CREATE TABLE `mahasiswa` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `nim` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan` year NOT NULL,
  `semester` tinyint unsigned DEFAULT NULL,
  `prodi_id` int unsigned DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nim` (`nim`),
  UNIQUE KEY `uq_nim` (`nim`),
  KEY `fk_mhs_user` (`user_id`),
  KEY `fk_mhs_prodi` (`prodi_id`),
  CONSTRAINT `fk_mhs_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mhs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `mahasiswa`
INSERT INTO `mahasiswa` (`id`, `user_id`, `nim`, `angkatan`, `semester`, `prodi_id`, `no_hp`, `alamat`) VALUES ('4', '33', '20240810057', '2024', '7', '1', '089606941238', 'Jl. Cut Nyak Dien No. 36.A, Cijoho, Kec. Kuningan, Kabupaten Kuningan');
INSERT INTO `mahasiswa` (`id`, `user_id`, `nim`, `angkatan`, `semester`, `prodi_id`, `no_hp`, `alamat`) VALUES ('5', '34', '202308107777', '2024', '1', '1', '089606941238', 'Jl. Cut Nyak Dien No. 36.A, Cijoho, Kec. Kuningan, Kabupaten Kuningan');

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_page` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_page` (`kode_page`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `menus`
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('1', 'pengajuan', 'Daftar Pengajuan', 'bi bi-file-earmark-text', '10');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('2', 'pengajuan.create', 'Buat Pengajuan', 'bi bi-plus-circle', '11');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('3', 'staff', 'Verifikasi Berkas', 'bi bi-clipboard-check', '20');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('4', 'admin.mahasiswa', 'Data Mahasiswa', 'bi bi-people', '22');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('5', 'admin.dosen', 'Data Dosen', 'bi bi-person-badge', '23');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('6', 'kaprodi', 'Review Pengajuan', 'bi bi-eye', '31');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('7', 'admin.periode', 'Data Periode', 'bi bi-calendar3', '32');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('8', 'dewan', 'Review & Penentuan', 'bi bi-people-fill', '41');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('9', 'superadmin.roles', 'Manajemen Role', 'bi bi-shield-lock', '91');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('10', 'superadmin.menus', 'Manajemen Menu', 'bi bi-list-nested', '92');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('21', 'superadmin.database', 'Backup Database', 'bi bi-database', '93');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('23', 'admin.staff', 'Data Staff', 'bi bi-person-workspace', '24');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('24', 'kaprodi.history', 'Histori Pengajuan', 'bi bi-clock-history', '3');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('25', 'dewan.history', 'Histori Review', 'bi bi-clock-history', '42');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('26', 'staff.siap_fakultas', 'Siap Ajukan Fakultas', 'bi bi-send-check', '21');

DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `pengajuan_id` int unsigned DEFAULT NULL,
  `pesan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`,`is_read`),
  KEY `idx_notif_created` (`created_at`),
  KEY `fk_notif_pengajuan` (`pengajuan_id`),
  CONSTRAINT `fk_notif_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `notifikasi`
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('1', '29', NULL, 'Pengajuan baru dari hanindhiya menunggu verifikasi.', '1', '2026-04-26 09:00:50');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('5', '29', NULL, 'Pengajuan baru dari Ujang menunggu verifikasi.', '1', '2026-04-26 21:00:32');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('7', '29', NULL, 'Pengajuan baru dari Ujang menunggu verifikasi.', '1', '2026-04-26 21:13:46');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('10', '29', NULL, 'Pengajuan baru dari Ujang menunggu verifikasi.', '1', '2026-04-26 21:22:20');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('33', '29', '3', 'Pengajuan baru dari ade menunggu verifikasi.', '1', '2026-04-28 20:00:49');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('34', '33', '3', 'Pengajuan Anda dikembalikan untuk revisi oleh Staff. Catatan: File proposal tidak ada, silakan upload', '1', '2026-04-28 20:14:51');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('35', '29', '3', 'Pengajuan baru dari ade menunggu verifikasi.', '1', '2026-04-28 20:15:46');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('36', '33', '3', 'Proposal Anda telah direview oleh Dewan Bimbingan. Menunggu persetujuan akhir Kaprodi.', '1', '2026-04-28 20:21:25');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('37', '33', '3', 'Proposal Anda telah direview oleh Dewan Bimbingan. Menunggu persetujuan akhir Kaprodi.', '0', '2026-04-28 20:25:44');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('38', '33', '3', 'Judul skripsi Anda telah disetujui akhir dan diajukan ke Fakultas.', '0', '2026-04-28 20:43:51');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('39', '33', '3', 'Pengajuan judul Anda telah sepenuhnya Selesai di tingkat Prodi dan Siap Diajukan ke Fakultas.', '0', '2026-04-28 20:44:29');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('40', '33', '3', 'Pengajuan judul Anda telah selesai diajukan ke Fakultas.', '0', '2026-04-28 20:48:09');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('41', '33', '3', 'Selamat! SK Dekan untuk judul skripsi Anda telah terbit. Silakan hubungi prodi untuk pengambilan.', '1', '2026-04-28 21:00:13');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('42', '29', '4', 'Pengajuan baru dari Ujang menunggu verifikasi.', '1', '2026-04-28 21:03:11');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('43', '34', '4', 'Pengajuan Anda telah diverifikasi dan diteruskan ke Kaprodi.', '0', '2026-04-28 21:03:27');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('44', '23', '4', 'Anda ditugaskan sebagai Dewan Reviewer 1 untuk pengajuan \'Rancang Bangun Teknologi Digitalizing Image Recognizer Berbasis Citra\'.', '1', '2026-04-28 21:20:56');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('45', '15', '4', 'Anda ditugaskan sebagai Dewan Reviewer 2 untuk pengajuan \'Rancang Bangun Teknologi Digitalizing Image Recognizer Berbasis Citra\'.', '0', '2026-04-28 21:20:56');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('46', '34', '4', 'Proposal Anda telah direview oleh Dewan Bimbingan. Menunggu persetujuan akhir Kaprodi.', '0', '2026-04-28 21:31:20');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('47', '34', '4', 'Proposal Anda telah direview oleh Dewan Bimbingan. Menunggu persetujuan akhir Kaprodi.', '0', '2026-04-28 21:48:20');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('48', '34', '4', 'Judul skripsi Anda telah disetujui akhir dan diajukan ke Fakultas.', '0', '2026-04-28 21:49:09');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('49', '34', '4', 'Pengajuan judul Anda telah sepenuhnya Selesai di tingkat Prodi dan Siap Diajukan ke Fakultas.', '0', '2026-04-28 21:52:47');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('50', '33', '3', 'Status pengajuan Anda dikembalikan menjadi Siap Diajukan ke Fakultas.', '0', '2026-04-28 21:53:01');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('51', '34', '4', 'Pengajuan judul Anda telah selesai diajukan ke Fakultas.', '0', '2026-04-28 21:53:08');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('52', '33', '3', 'Pengajuan judul Anda telah selesai diajukan ke Fakultas.', '0', '2026-04-28 21:53:11');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('53', '34', '4', 'Selamat! SK Dekan untuk judul skripsi Anda telah terbit. Silakan hubungi prodi untuk pengambilan.', '0', '2026-04-28 21:53:33');
INSERT INTO `notifikasi` (`id`, `user_id`, `pengajuan_id`, `pesan`, `is_read`, `created_at`) VALUES ('54', '33', '3', 'Selamat! SK Dekan untuk judul skripsi Anda telah terbit. Silakan hubungi prodi untuk pengambilan.', '0', '2026-04-28 21:53:37');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `pembimbing`
INSERT INTO `pembimbing` (`id`, `pengajuan_id`, `dosen_id`, `urutan`, `assigned_at`) VALUES ('5', '3', '9', '1', '2026-04-28 20:20:16');
INSERT INTO `pembimbing` (`id`, `pengajuan_id`, `dosen_id`, `urutan`, `assigned_at`) VALUES ('6', '3', '1', '2', '2026-04-28 20:20:16');
INSERT INTO `pembimbing` (`id`, `pengajuan_id`, `dosen_id`, `urutan`, `assigned_at`) VALUES ('7', '4', '9', '1', '2026-04-28 21:20:56');
INSERT INTO `pembimbing` (`id`, `pengajuan_id`, `dosen_id`, `urutan`, `assigned_at`) VALUES ('8', '4', '1', '2', '2026-04-28 21:20:56');

DROP TABLE IF EXISTS `pengajuan_judul`;
CREATE TABLE `pengajuan_judul` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `periode_id` int unsigned DEFAULT NULL,
  `judul` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bidang` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_proposal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama file PDF di folder uploads/proposals',
  `pembimbing_1_id` int unsigned DEFAULT NULL,
  `pembimbing_2_id` int unsigned DEFAULT NULL,
  `dewan_1_id` int unsigned DEFAULT NULL,
  `dewan_2_id` int unsigned DEFAULT NULL,
  `status` enum('draft','diajukan','diverifikasi','direview','direview_dewan','revisi','ditolak','disetujui','pembimbing_ditentukan','selesai','selesai_fakultas','sk_terbit') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `catatan_terakhir` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Catatan dari petugas terakhir',
  `verified_at` datetime DEFAULT NULL COMMENT 'Waktu diverifikasi Staff',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_periode` (`periode_id`),
  KEY `fk_pj_pembimbing_1` (`pembimbing_1_id`),
  KEY `fk_pj_pembimbing_2` (`pembimbing_2_id`),
  KEY `fk_dewan_1` (`dewan_1_id`),
  KEY `fk_dewan_2` (`dewan_2_id`),
  CONSTRAINT `fk_dewan_1` FOREIGN KEY (`dewan_1_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_dewan_2` FOREIGN KEY (`dewan_2_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_pj_pembimbing_1` FOREIGN KEY (`pembimbing_1_id`) REFERENCES `dosen` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pj_pembimbing_2` FOREIGN KEY (`pembimbing_2_id`) REFERENCES `dosen` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pj_periode` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pj_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `pengajuan_judul`
INSERT INTO `pengajuan_judul` (`id`, `user_id`, `periode_id`, `judul`, `deskripsi`, `bidang`, `file_proposal`, `pembimbing_1_id`, `pembimbing_2_id`, `dewan_1_id`, `dewan_2_id`, `status`, `catatan_terakhir`, `verified_at`, `created_at`, `updated_at`) VALUES ('3', '33', '4', 'Sistem Penunjgan Keputusan Pemilihan RT RW Berbasis Block Chain', '<h3>1. LATAR BELAKANG</h3><p>(Jelaskan fenomena/masalah yang mendasari penelitian Anda)</p><h3>2. RUMUSAN MASALAH</h3><p>(Apa masalah utama yang ingin diselesaikan?)</p><h3>3. TUJUAN PENELITIAN</h3><p>(Apa yang ingin dicapai dari penelitian ini?)</p><h3>4. METODOLOGI</h3><p>(Metode/Algoritma apa yang digunakan? Bagaimana tahapannya?)</p>', 'Kecerdasan Buatan', 'proposal_20260428131534_721c4fc7.pdf', '9', '1', NULL, NULL, 'sk_terbit', 'Oke Acc', '2026-04-28 20:14:51', '2026-04-28 19:58:38', '2026-04-28 21:53:37');
INSERT INTO `pengajuan_judul` (`id`, `user_id`, `periode_id`, `judul`, `deskripsi`, `bidang`, `file_proposal`, `pembimbing_1_id`, `pembimbing_2_id`, `dewan_1_id`, `dewan_2_id`, `status`, `catatan_terakhir`, `verified_at`, `created_at`, `updated_at`) VALUES ('4', '34', '4', 'Rancang Bangun Teknologi Digitalizing Image Recognizer Berbasis Citra', '<h3>1. LATAR BELAKANG</h3><p>(Jelaskan fenomena/masalah yang mendasari penelitian Anda)</p><p><br></p><p><br></p><h3>2. RUMUSAN MASALAH </h3><p>(Apa masalah utama yang ingin diselesaikan?)</p><p><br></p><h3>3. TUJUAN PENELITIAN</h3><p>(Apa yang ingin dicapai dari penelitian ini?)</p><p><br></p><h3>4. METODOLOGI</h3><p>(Metode/Algoritma apa yang digunakan? Bagaimana tahapannya?)</p>', 'Kecerdasan Buatan', 'proposal_20260428140307_0f9b86e1.pdf', '9', '1', '23', '15', 'sk_terbit', 'Oke ACC, Tunggu SK terbit', '2026-04-28 21:03:27', '2026-04-28 21:03:07', '2026-04-28 21:53:33');

DROP TABLE IF EXISTS `periode`;
CREATE TABLE `periode` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `kode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenjang` enum('D3','S1','S2','S3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S1',
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
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '3');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '3');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('2', '4');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '4');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '4');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('13', '4');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('2', '5');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '5');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '5');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('13', '5');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '6');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '6');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('13', '6');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '7');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '7');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('13', '7');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('4', '8');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '8');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '9');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '10');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '21');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '23');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '23');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '24');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '24');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('13', '24');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('4', '25');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '25');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('2', '26');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '26');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '26');

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `roles`
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('1', 'mahasiswa', 'Mahasiswa');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('2', 'staff', 'Staff / Verifikator');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('3', 'kaprodi', 'Ketua Program Studi');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('4', 'dewan', 'Dewan Pembimbing');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('5', 'superadmin', 'Super Admin');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('12', 'dosen', 'Dosen Pembimbing');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('13', 'sekprodi', 'Sekprodi');

DROP TABLE IF EXISTS `status_log`;
CREATE TABLE `status_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` int unsigned NOT NULL,
  `status_lama` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status_baru` enum('draft','diajukan','diverifikasi','direview','direview_dewan','revisi','ditolak','disetujui','pembimbing_ditentukan','selesai','selesai_fakultas','sk_terbit') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sl_pengajuan` (`pengajuan_id`),
  KEY `fk_sl_user` (`user_id`),
  CONSTRAINT `fk_sl_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `status_log`
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('42', '3', '', 'draft', '33', 'Pengajuan dibuat.', '2026-04-28 19:58:38');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('43', '3', 'draft', 'draft', '33', 'Data pengajuan diperbarui.', '2026-04-28 20:00:26');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('44', '3', 'draft', 'draft', '33', 'Data pengajuan diperbarui.', '2026-04-28 20:00:46');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('45', '3', 'draft', 'diajukan', '33', 'Pengajuan diajukan oleh mahasiswa.', '2026-04-28 20:00:49');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('46', '3', 'diajukan', 'diajukan', '20', 'Data pengajuan diperbarui.', '2026-04-28 20:03:42');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('47', '3', 'diajukan', 'revisi', '29', 'File proposal tidak ada, silakan upload', '2026-04-28 20:14:51');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('48', '3', 'revisi', 'revisi', '33', 'Data pengajuan diperbarui.', '2026-04-28 20:15:34');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('49', '3', 'revisi', 'diajukan', '33', 'Pengajuan diajukan oleh mahasiswa.', '2026-04-28 20:15:46');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('50', '3', 'diverifikasi', 'direview', '20', 'oke Lanjutkan sesuai dengan saran dari pembimbing', '2026-04-28 20:20:16');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('51', '3', 'direview', 'direview_dewan', '20', '<p>Oke lanjutkan</p>', '2026-04-28 20:21:25');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('52', '3', 'direview_dewan', 'direview_dewan', '20', '<p>Oke, sudah di acc, tinggal menunggu SK Bimbingan</p>', '2026-04-28 20:25:44');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('53', '3', 'direview_dewan', 'pembimbing_ditentukan', '20', 'Oke Acc', '2026-04-28 20:43:51');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('54', '3', 'pembimbing_ditentukan', 'selesai', '20', 'Proses pengajuan di Prodi selesai. Berkas Siap Diajukan ke Fakultas.', '2026-04-28 20:44:29');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('55', '3', 'selesai', 'selesai_fakultas', '29', 'Berkas telah selesai diserahkan/diajukan ke Fakultas.', '2026-04-28 20:48:09');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('56', '3', 'sk_terbit', 'selesai_fakultas', '29', 'Status SK Terbit dibatalkan. Kembali ke tahap Selesai Diajukan ke Fakultas.', '2026-04-28 20:58:55');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('57', '3', 'sk_terbit', 'selesai_fakultas', '29', 'Status SK Terbit dibatalkan. Kembali ke tahap Selesai Diajukan ke Fakultas.', '2026-04-28 21:00:09');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('58', '3', 'selesai_fakultas', 'sk_terbit', '29', 'SK Dekan telah terbit. Proses pengajuan judul selesai sepenuhnya.', '2026-04-28 21:00:13');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('59', '4', '', 'draft', '34', 'Pengajuan dibuat.', '2026-04-28 21:03:07');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('60', '4', 'draft', 'diajukan', '34', 'Pengajuan diajukan oleh mahasiswa.', '2026-04-28 21:03:11');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('61', '4', 'diajukan', 'diverifikasi', '29', 'Oke', '2026-04-28 21:03:27');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('62', '4', 'diverifikasi', 'direview', '20', 'Baik, silakan dewan reviewer untuk memberikan saran dan reviewnya', '2026-04-28 21:20:56');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('63', '4', 'direview', 'direview_dewan', '15', '<p>Oke, judul sudah sangat bagus</p>', '2026-04-28 21:31:20');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('64', '4', 'direview_dewan', 'direview_dewan', '23', '<p><strong>Oke good lanjutkan<span class=\"ql-cursor\">﻿</span></strong></p>', '2026-04-28 21:48:19');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('65', '4', 'direview_dewan', 'pembimbing_ditentukan', '20', 'Oke ACC, Tunggu SK terbit', '2026-04-28 21:49:09');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('66', '4', 'pembimbing_ditentukan', 'selesai', '29', 'Proses pengajuan di Prodi selesai. Berkas Siap Diajukan ke Fakultas.', '2026-04-28 21:52:47');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('67', '3', 'sk_terbit', 'selesai_fakultas', '29', 'Status SK Terbit dibatalkan. Kembali ke tahap Selesai Diajukan ke Fakultas.', '2026-04-28 21:52:57');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('68', '3', 'selesai_fakultas', 'selesai', '29', 'Pembatalan status. Berkas kembali menjadi Siap Diajukan ke Fakultas.', '2026-04-28 21:53:01');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('69', '4', 'selesai', 'selesai_fakultas', '29', 'Berkas telah selesai diserahkan/diajukan ke Fakultas.', '2026-04-28 21:53:08');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('70', '3', 'selesai', 'selesai_fakultas', '29', 'Berkas telah selesai diserahkan/diajukan ke Fakultas.', '2026-04-28 21:53:11');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('71', '4', 'selesai_fakultas', 'sk_terbit', '29', 'SK Dekan telah terbit. Proses pengajuan judul selesai sepenuhnya.', '2026-04-28 21:53:33');
INSERT INTO `status_log` (`id`, `pengajuan_id`, `status_lama`, `status_baru`, `user_id`, `catatan`, `created_at`) VALUES ('72', '3', 'selesai_fakultas', 'sk_terbit', '29', 'SK Dekan telah terbit. Proses pengajuan judul selesai sepenuhnya.', '2026-04-28 21:53:37');

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` int unsigned NOT NULL,
  `role_kode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`role_kode`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `user_roles`
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('8', 'superadmin');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('15', 'dewan');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('15', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('16', 'dewan');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('16', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('17', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('18', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('19', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('20', 'dewan');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('20', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('20', 'kaprodi');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('21', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('22', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('23', 'dewan');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('23', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('24', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('25', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('26', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('27', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('28', 'dosen');
INSERT INTO `user_roles` (`user_id`, `role_kode`) VALUES ('29', 'staff');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mahasiswa',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `users`
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('8', 'Super Admin', 'ti@uniku.ac.id', NULL, '$2y$10$5uKVtYShge6hCA5SW3r1KebLn6HAz64e.oIg/Vr8J9Vxuj.WC7P6e', 'superadmin', '1', '2026-04-25 13:31:04');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('15', 'Dede Husen, M.Kom', 'dede.husen@uniku.ac.id', NULL, '$2y$10$vDK01u.uph9ZhYajCKEhi.0KpSnTUyDMO0U1WAfocCMEdoaOXVp7y', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('16', 'Fauziah, M.Kom', 'fauziah.m@uniku.ac.id', NULL, '$2y$10$zwsQLbjnQK9ofpSx4QBO9e9SW33Mhd0m6Mqa5vRc1J3A5IMs0m1ru', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('17', 'Fitra Nugraha, M.Kom', 'fitra.nugraha@uniku.ac.id', NULL, '$2y$10$Vk/7ditDBCCVpEflxSZWnek.L.TQRg.0qocgncmdbmz7l/MyFtMVW', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('18', 'Gentur Priguna Suwarto, M.T.I', 'gentur.priguna suwarto@uniku.ac.id', NULL, '$2y$10$neXZw9V0e3m3L1r/ntX3s.rgheR8D.OFEk3Nj5ihcbhB0p89vjia.', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('19', 'Heri Herwanto, M.Pd', 'heri.herwanto@uniku.ac.id', NULL, '$2y$10$J1PMBEUbFesumsivhgu7DeJTYmonqldCWZOBHmKUsZt37v8a/j6z2', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('20', 'Iwan Lesmana, M.Kom', 'iwan.lesmana@uniku.ac.id', NULL, '$2y$10$tor58XeCqUEnPO3fPVYwUOZBMv9WeisOOvnIlJIXGVjAWH63GxAxS', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('21', 'Nida Amalia Asikin, M.Pd', 'nida.amalia asikin@uniku.ac.id', NULL, '$2y$10$ovSjwyVzGBh7zVbe7T625uT61vd4dxCUOv8vZWScue0qnWNphkBQu', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('22', 'Nunu Nugraha, M.T', 'nunu.nugraha@uniku.ac.id', NULL, '$2y$10$XzY/K2C.WcGI4WMPDqhQf.BRIvACIab8HWnQvD5ISEh73ovHyZXvu', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('23', 'Rio Andriyat Krisdiawan, M.Kom', 'rio.andriyat@uniku.ac.id', NULL, '$2y$10$zshK/eA8IaHICEjcVMuyB.dBcbX0SyC5Nb7iTs.gHdDRmkwkecepK', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('24', 'Rio Priantama, M.T.I', 'rio.priantama@uniku.ac.id', NULL, '$2y$10$3VzNhwajocj.JJDp7QdJMu3gnV5IhZeODkoixoqFFuFSKgPn..xCe', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('25', 'Sherly Gina Supratman, M.Kom', 'sherly.gina.supratman@uniku.ac.id', NULL, '$2y$10$zOO9EVKyI0rhc6/3jBmAXOvzZji0jq0BzrYywOoKduu3yioBnvwuC', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('26', 'Siti Maesyaroh, M.Kom', 'siti.maesyaroh@uniku.ac.id', NULL, '$2y$10$0rs1QKPnKn/UV8duRbf01u9Y3hv1Y7Cd3DzKz1E77j56uVjh.M5hK', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('27', 'Tito Sugiharto, M.Eng', 'tito@uniku.ac.id', NULL, '$2y$10$3qaKFwQyhj44QnG0ofnsMOAvte3IZKUmeWvdCll1h1PgwT2BfYax.', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('28', 'Yati Nurhayati, M.Kom', 'yati.nurhayati@uniku.ac.id', NULL, '$2y$10$gADzfZKCunrVGuoT5VkSPeiEvFxLt63ubHfaBM9OVDRpHMoPH9DVO', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('29', 'alifa', 'alifa@uniku.ac.id', NULL, '$2y$10$dxOxxm3aQgkDnn1z.XIsAO.8EIQwwHmzOmybYO3g6YcqqK9Kk3mti', 'staff', '1', '2026-04-25 23:51:42');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('33', 'ade', 'ade@uniku.ac.id', NULL, '$2y$10$YhUUnWnCJsmezHVpHvSxcOWEKdUzkOWbkYrNfzye5tNwNwq7713Ka', 'mahasiswa', '1', '2026-04-28 19:54:12');
INSERT INTO `users` (`id`, `nama`, `email`, `foto`, `password`, `role`, `is_active`, `created_at`) VALUES ('34', 'Ujang', 'ujang@uniku.ac.id', NULL, '$2y$10$htku11O1LaRV.hOa8/oJ8OGkprMobMp4TTxbF0aRE/4d23SYjHP7a', 'mahasiswa', '1', '2026-04-28 20:30:53');

SET FOREIGN_KEY_CHECKS=1;
