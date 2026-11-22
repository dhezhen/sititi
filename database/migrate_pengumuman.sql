CREATE TABLE IF NOT EXISTS `pengumuman` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_roles` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibuat_oleh` int unsigned NOT NULL,
  `penerima_count` int unsigned NOT NULL DEFAULT 0,
  `email_diminta` tinyint(1) NOT NULL DEFAULT 1,
  `email_status` enum('menunggu','tidak_diminta','belum_dikonfigurasi','diterima','sebagian_gagal','gagal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `email_diterima` int unsigned NOT NULL DEFAULT 0,
  `email_gagal` int unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pengumuman_created` (`created_at`),
  KEY `idx_pengumuman_sender` (`dibuat_oleh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menus` (`kode_page`, `judul`, `icon`, `urutan`)
VALUES ('superadmin.pengumuman', 'Pengumuman', 'bi bi-megaphone', 94)
ON DUPLICATE KEY UPDATE
  `judul` = VALUES(`judul`),
  `icon` = VALUES(`icon`),
  `urutan` = VALUES(`urutan`);

INSERT IGNORE INTO `role_menus` (`role_id`, `menu_id`)
SELECT `roles`.`id`, `menus`.`id`
FROM `roles`
JOIN `menus` ON `menus`.`kode_page` = 'superadmin.pengumuman'
WHERE `roles`.`kode` = 'superadmin';
