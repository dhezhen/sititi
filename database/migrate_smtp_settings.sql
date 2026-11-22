CREATE TABLE IF NOT EXISTS `smtp_settings` (
  `id` tinyint unsigned NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `host` varchar(253) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `port` smallint unsigned NOT NULL DEFAULT 587,
  `encryption` enum('tls','ssl','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tls',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password_encrypted` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `from_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menus` (`kode_page`, `judul`, `icon`, `urutan`)
VALUES ('superadmin.smtp', 'Pengaturan Email', 'bi bi-envelope', 95)
ON DUPLICATE KEY UPDATE
  `judul` = VALUES(`judul`),
  `icon` = VALUES(`icon`),
  `urutan` = VALUES(`urutan`);

INSERT IGNORE INTO `role_menus` (`role_id`, `menu_id`)
SELECT `roles`.`id`, `menus`.`id`
FROM `roles`
JOIN `menus` ON `menus`.`kode_page` = 'superadmin.smtp'
WHERE `roles`.`kode` = 'superadmin';
