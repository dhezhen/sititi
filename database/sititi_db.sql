-- SiTiTi Database Fresh Backup
-- Generated: 2026-04-25 19:08:16

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `roles` 
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('1', 'mahasiswa', 'Mahasiswa');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('2', 'staff', 'Staff / Verifikator');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('3', 'kaprodi', 'Ketua Program Studi');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('4', 'dewan', 'Dewan Pembimbing');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('5', 'superadmin', 'Super Admin');
INSERT INTO `roles` (`id`, `kode`, `nama`) VALUES ('12', 'dosen', 'Dosen Pembimbing');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mahasiswa',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `users` 
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('8', 'Super Admin', 'ti@uniku.ac.id', '$2y$10$5uKVtYShge6hCA5SW3r1KebLn6HAz64e.oIg/Vr8J9Vxuj.WC7P6e', 'superadmin', '1', '2026-04-25 13:31:04');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('15', 'Dede Husen, M.Kom', 'dede.husen@uniku.ac.id', '$2y$10$wbyAy4l4F1KAKQLorCKDPem9AHd0/4aG4QAgEU3IwKbTxvdi5Na62', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('16', 'Fauziah, M.Kom', 'fauziah.m@uniku.ac.id', '$2y$10$zwsQLbjnQK9ofpSx4QBO9e9SW33Mhd0m6Mqa5vRc1J3A5IMs0m1ru', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('17', 'Fitra Nugraha, M.Kom', 'fitra.nugraha@uniku.ac.id', '$2y$10$Vk/7ditDBCCVpEflxSZWnek.L.TQRg.0qocgncmdbmz7l/MyFtMVW', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('18', 'Gentur Priguna Suwarto, M.T.I', 'gentur.priguna suwarto@uniku.ac.id', '$2y$10$neXZw9V0e3m3L1r/ntX3s.rgheR8D.OFEk3Nj5ihcbhB0p89vjia.', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('19', 'Heri Herwanto, M.Pd', 'heri.herwanto@uniku.ac.id', '$2y$10$J1PMBEUbFesumsivhgu7DeJTYmonqldCWZOBHmKUsZt37v8a/j6z2', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('20', 'Iwan Lesmana, M.Kom', 'iwan.lesmana@uniku.ac.id', '$2y$10$tor58XeCqUEnPO3fPVYwUOZBMv9WeisOOvnIlJIXGVjAWH63GxAxS', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('21', 'Nida Amalia Asikin, M.Pd', 'nida.amalia asikin@uniku.ac.id', '$2y$10$ovSjwyVzGBh7zVbe7T625uT61vd4dxCUOv8vZWScue0qnWNphkBQu', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('22', 'Nunu Nugraha, M.T', 'nunu.nugraha@uniku.ac.id', '$2y$10$XzY/K2C.WcGI4WMPDqhQf.BRIvACIab8HWnQvD5ISEh73ovHyZXvu', 'dosen', '1', '2026-04-25 23:41:14');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('23', 'Rio Andriyat Krisdiawan, M.Kom', 'rio.andriyat@uniku.ac.id', '$2y$10$zshK/eA8IaHICEjcVMuyB.dBcbX0SyC5Nb7iTs.gHdDRmkwkecepK', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('24', 'Rio Priantama, M.T.I', 'rio.priantama@uniku.ac.id', '$2y$10$3VzNhwajocj.JJDp7QdJMu3gnV5IhZeODkoixoqFFuFSKgPn..xCe', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('25', 'Sherly Gina Supratman, M.Kom', 'sherly.gina.supratman@uniku.ac.id', '$2y$10$zOO9EVKyI0rhc6/3jBmAXOvzZji0jq0BzrYywOoKduu3yioBnvwuC', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('26', 'Siti Maesyaroh, M.Kom', 'siti.maesyaroh@uniku.ac.id', '$2y$10$0rs1QKPnKn/UV8duRbf01u9Y3hv1Y7Cd3DzKz1E77j56uVjh.M5hK', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('27', 'Tito Sugiharto, M.Eng', 'tito@uniku.ac.id', '$2y$10$3qaKFwQyhj44QnG0ofnsMOAvte3IZKUmeWvdCll1h1PgwT2BfYax.', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('28', 'Yati Nurhayati, M.Kom', 'yati.nurhayati@uniku.ac.id', '$2y$10$gADzfZKCunrVGuoT5VkSPeiEvFxLt63ubHfaBM9OVDRpHMoPH9DVO', 'dosen', '1', '2026-04-25 23:41:15');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('29', 'alifa', 'alifa@uniku.ac.id', '$2y$10$dxOxxm3aQgkDnn1z.XIsAO.8EIQwwHmzOmybYO3g6YcqqK9Kk3mti', 'staff', '1', '2026-04-25 23:51:42');
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES ('30', 'Ade', 'ade@uniku.ac.id', '$2y$10$PbmpeQN9ZCiPymSs8booKOOYO7HZOhNUSyV/nRgc0uYSyXU6bGKzq', 'mahasiswa', '1', '2026-04-26 00:19:40');

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
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('1', 'Dede Husen, M.Kom', '0404059205', 'dede.husen@uniku.ac.id', '1', 'Kecerdasan Buatan', 'ASISTEN AHLI', 'S2', '089606941238', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('2', 'Fauziah, M.Kom', '0414107902', 'fauziah.m@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '0', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('3', 'Fitra Nugraha, M.Kom', '0412088004', 'fitra.nugraha@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '1', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('4', 'Gentur Priguna Suwarto, M.T.I', '0410118005', 'gentur.priguna suwarto@uniku.ac.id', '1', '-', 'ASISTEN AHLI', 'S2', '2', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('5', 'Heri Herwanto, M.Pd', '0421107802', 'heri.herwanto@uniku.ac.id', '1', '-', 'ASISTEN AHLI', 'S2', '3', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('6', 'Iwan Lesmana, M.Kom', '0418078403', 'iwan.lesmana@uniku.ac.id', '1', 'Computer Vision', '', 'S2', '4', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('7', 'Nida Amalia Asikin, M.Pd', '0416098701', 'nida.amalia asikin@uniku.ac.id', '1', '-', 'ASISTEN AHLI', 'S2', '5', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('8', 'Nunu Nugraha, M.T', '0413058502', 'nunu.nugraha@uniku.ac.id', '1', '-', 'LEKTOR', 'S2', '6', '1');
INSERT INTO `dosen` (`id`, `nama`, `nidn`, `email`, `prodi_id`, `bidang_keahlian`, `jabatan_akademik`, `pendidikan`, `no_hp`, `is_active`) VALUES ('9', 'Rio Andriyat Krisdiawan, M.Kom', '0427048901', 'rio.andriyat@uniku.ac.id', '1', '-', 'LEKTOR KEPALA', 'S2', '7', '1');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `mahasiswa` 
INSERT INTO `mahasiswa` (`id`, `user_id`, `nim`, `angkatan`, `semester`, `prodi_id`, `no_hp`, `alamat`) VALUES ('1', '30', '2023081003876', '2023', '8', '1', '089606941238', 'Cikijing');

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

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_page` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_page` (`kode_page`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `menus` 
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('1', 'pengajuan', 'Daftar Pengajuan', 'bi bi-file-earmark-text', '10');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('2', 'pengajuan.create', 'Buat Pengajuan', 'bi bi-plus-circle', '11');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('3', 'staff', 'Verifikasi Berkas', 'bi bi-clipboard-check', '20');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('4', 'admin.mahasiswa', 'Data Mahasiswa', 'bi bi-people', '21');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('5', 'admin.dosen', 'Data Dosen', 'bi bi-person-badge', '22');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('6', 'kaprodi', 'Review Pengajuan', 'bi bi-eye', '30');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('7', 'admin.periode', 'Data Periode', 'bi bi-calendar3', '31');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('8', 'dewan', 'Review & Penentuan', 'bi bi-people-fill', '40');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('9', 'superadmin.roles', 'Manajemen Role', 'bi bi-shield-lock', '90');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('10', 'superadmin.menus', 'Manajemen Menu', 'bi bi-list-nested', '91');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('21', 'superadmin.database', 'Backup Database', 'bi bi-database', '92');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('23', 'admin.staff', 'Data Staff', 'bi bi-person-workspace', '23');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('24', 'kaprodi.history', 'Histori Pengajuan', 'bi bi-clock-history', '3');
INSERT INTO `menus` (`id`, `kode_page`, `judul`, `icon`, `urutan`) VALUES ('25', 'dewan.history', 'Histori Review', 'bi bi-clock-history', '41');

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
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '23');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('3', '24');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('5', '24');
INSERT INTO `role_menus` (`role_id`, `menu_id`) VALUES ('4', '25');

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
  `status` enum('draft','diajukan','diverifikasi','direview','revisi','ditolak','disetujui','pembimbing_ditentukan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
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
  CONSTRAINT `fk_pj_pembimbing_1` FOREIGN KEY (`pembimbing_1_id`) REFERENCES `dosen` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pj_pembimbing_2` FOREIGN KEY (`pembimbing_2_id`) REFERENCES `dosen` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pj_periode` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pj_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `status_log`;
CREATE TABLE `status_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pengajuan_id` int unsigned NOT NULL,
  `status_lama` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status_baru` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sl_pengajuan` (`pengajuan_id`),
  KEY `fk_sl_user` (`user_id`),
  CONSTRAINT `fk_sl_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_judul` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

SET FOREIGN_KEY_CHECKS=1;
