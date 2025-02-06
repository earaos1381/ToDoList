-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para todolist
CREATE DATABASE IF NOT EXISTS `todolist` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `todolist`;

-- Volcando estructura para tabla todolist.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.attachments: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL DEFAULT '#FFFFFF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`description`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.categories: ~4 rows (aproximadamente)
INSERT IGNORE INTO `categories` (`id`, `description`, `color`, `created_at`, `updated_at`) VALUES
	(1, 'Personal', '#FFFFFF', '2025-02-04 20:30:21', '2025-02-04 20:30:22'),
	(2, 'Trabajo', '#FFFFFF', '2025-02-04 20:30:30', '2025-02-04 20:30:30'),
	(3, 'API', '#FFFFFF', NULL, NULL),
	(4, 'Front', '#FFFFFF', NULL, NULL);

-- Volcando estructura para tabla todolist.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.comments: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.failed_jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.imagenes
CREATE TABLE IF NOT EXISTS `imagenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `ruta` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `estado` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.imagenes: ~0 rows (aproximadamente)
INSERT IGNORE INTO `imagenes` (`id`, `nombre`, `ruta`, `tipo`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'Logo', '/assets/img/logo-2.png', 'logo', 1, '2024-06-04 22:22:55', NULL);

-- Volcando estructura para tabla todolist.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.migrations: ~11 rows (aproximadamente)
INSERT IGNORE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2024_06_06_214711_create_permission_tables', 2),
	(6, '2016_06_01_000001_create_oauth_auth_codes_table', 3),
	(7, '2016_06_01_000002_create_oauth_access_tokens_table', 3),
	(8, '2016_06_01_000003_create_oauth_refresh_tokens_table', 3),
	(9, '2016_06_01_000004_create_oauth_clients_table', 3),
	(10, '2016_06_01_000005_create_oauth_personal_access_clients_table', 3),
	(11, '2025_02_04_235840_create_sessions_table', 4);

-- Volcando estructura para tabla todolist.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.model_has_permissions: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.model_has_roles: ~25 rows (aproximadamente)
INSERT IGNORE INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(2, 'App\\Models\\User', 2),
	(2, 'App\\Models\\User', 3),
	(8, 'App\\Models\\User', 20),
	(2, 'App\\Models\\User', 25),
	(2, 'App\\Models\\User', 26),
	(2, 'App\\Models\\User', 27),
	(2, 'App\\Models\\User', 28),
	(2, 'App\\Models\\User', 29),
	(2, 'App\\Models\\User', 30),
	(2, 'App\\Models\\User', 31),
	(2, 'App\\Models\\User', 32),
	(2, 'App\\Models\\User', 33),
	(2, 'App\\Models\\User', 35),
	(2, 'App\\Models\\User', 36),
	(2, 'App\\Models\\User', 37),
	(2, 'App\\Models\\User', 38),
	(2, 'App\\Models\\User', 39),
	(2, 'App\\Models\\User', 40),
	(2, 'App\\Models\\User', 41),
	(2, 'App\\Models\\User', 42),
	(2, 'App\\Models\\User', 43),
	(2, 'App\\Models\\User', 45),
	(2, 'App\\Models\\User', 48);

-- Volcando estructura para tabla todolist.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('task_assigned','task_completed','comment_added','reminder') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `task_id` bigint unsigned DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.notifications: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.oauth_access_tokens
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.oauth_access_tokens: ~49 rows (aproximadamente)
INSERT IGNORE INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
	('11fac23631570d5104a5ee57afe867986e32956c574b6f02779276115e3b5e72001d583d95728bf0', 39, 4, NULL, '[]', 0, '2024-11-22 04:15:26', '2024-11-22 04:15:26', '2025-11-21 23:15:26'),
	('141eb555bb54ecdc33915145b9ee767ebf86393404c1665dfa8f7030d603140c6f56dd26ad694a93', 20, 4, NULL, '[]', 0, '2024-11-04 22:30:55', '2024-11-04 22:30:55', '2025-11-04 17:30:55'),
	('1478de3cae18534189f54a5ab1ae160fb4d582c8798daeac55837872feae881a0ea1fb6af5cbe01d', 43, 4, NULL, '[]', 0, '2024-11-05 02:38:45', '2024-11-05 02:38:45', '2025-11-04 21:38:45'),
	('1ed410a5b782d410c990c743b36569e14c76d9b29d0111f926e0af3175b0de6c20cc8b7fab99a3ee', 41, 4, NULL, '[]', 0, '2024-11-09 20:47:16', '2024-11-09 20:47:16', '2025-11-09 15:47:16'),
	('1fa15a2e9721b5d629d488d3bdd747665dc9e41cd2aabd77fc77fa41194020d873fcbc5992c897ed', 32, 4, NULL, '[]', 0, '2024-11-22 00:52:55', '2024-11-22 00:52:55', '2025-11-21 19:52:55'),
	('25f1e042250e4b8e70955c34d2451c859637d1b81ce1d2e426c7c4f1e154869f1787a885a0f4ed3a', 43, 4, NULL, '[]', 0, '2024-11-06 03:14:26', '2024-11-06 03:14:26', '2025-11-05 22:14:26'),
	('29399eed426f5394ef1d235170eb8e76238a060ce6bfe66158a864626ce301187a8ebb7e1b7b1f41', 36, 4, NULL, '[]', 0, '2024-11-08 04:30:03', '2024-11-08 04:30:03', '2025-11-07 23:30:03'),
	('3ad10b1e339525c879606d19bd9ffc7faac8a0332174075d59a2020bdad688d7dc3987fdf0ca0268', 32, 4, NULL, '[]', 0, '2024-11-08 03:58:05', '2024-11-08 03:58:05', '2025-11-07 22:58:05'),
	('3fed3675a1b5e142a207e4af1c6b348537a2559021a5f987013c03bf2df818d11e1da219c80fe7a3', 39, 4, NULL, '[]', 0, '2024-11-22 01:01:01', '2024-11-22 01:01:01', '2025-11-21 20:01:01'),
	('41e1c96b119dce03f593f7afaddfe1faf866eb0d14be5a209c9fb7e2641d515f72c2dcb1428b141a', 40, 4, NULL, '[]', 0, '2024-11-22 01:28:12', '2024-11-22 01:28:12', '2025-11-21 20:28:12'),
	('47404e48fbca410e07ef282b6fbc82db0a4e1b65331d3863672101fe72a618e17b6175f2b2153db8', 26, 4, NULL, '[]', 0, '2024-11-10 03:22:21', '2024-11-10 03:22:21', '2025-11-09 22:22:21'),
	('4875b85e0480ab3ce68f24a16fccae6cf98b1e2d4f8dcc942a5b540e4837ff6752a320bc5c0530b3', 32, 4, NULL, '[]', 0, '2024-11-08 03:59:14', '2024-11-08 03:59:14', '2025-11-07 22:59:14'),
	('498dbc1e884e491d063f6333af1ffb00e4f27ae695ce249363a497b6daf32ca887f953598c17a02c', 26, 4, NULL, '[]', 0, '2024-11-10 03:17:55', '2024-11-10 03:17:55', '2025-11-09 22:17:55'),
	('4d2778b453cb3ac6ccfa0466c32398cdcda4ff1d9dccca80ad10a156c08f8b54ecd100d7972eaf6f', 39, 4, NULL, '[]', 0, '2024-11-06 03:18:46', '2024-11-06 03:18:46', '2025-11-05 22:18:46'),
	('5c63748af6c6238e2b0fc8975848d9ec2712c10854b0f96dfa144dbdc51b73eb9aa193f473f4cf9b', 20, 4, NULL, '[]', 0, '2024-11-08 02:25:52', '2024-11-08 02:25:52', '2025-11-07 21:25:52'),
	('5eb9b342d5c332cea82b7df87a9366a6f04868a82267d1d52d33dc2326387d58ad645bd06240f124', 32, 4, NULL, '[]', 0, '2024-11-09 06:33:42', '2024-11-09 06:33:42', '2025-11-09 01:33:42'),
	('615723747b788dcac70e5d1d083ea1844ee1bdb25b95f8532106f16993980b5481f7daef9f0096b5', 32, 4, NULL, '[]', 0, '2024-11-08 03:00:57', '2024-11-08 03:00:57', '2025-11-07 22:00:57'),
	('65055e850db8dcc0bd058f3edf5aba5f10decfe996a5babaaa19bf8f7e146d85af675a216b243f55', 31, 4, NULL, '[]', 0, '2024-11-22 06:45:12', '2024-11-22 06:45:12', '2025-11-22 01:45:12'),
	('651f8944bc890ad24f77dce74726943160af89a8d291804f9ec162637b37f69ebd17054adc0d0600', 26, 4, NULL, '[]', 0, '2024-11-22 08:50:35', '2024-11-22 08:50:35', '2025-11-22 03:50:35'),
	('66f9c0e80caf74b3b02843ff041cf0bbfc2b99d21ff0c571eab929c77a7288620cdb5ee7dbca6c9f', 31, 4, NULL, '[]', 0, '2024-11-22 20:08:51', '2024-11-22 20:08:51', '2025-11-22 15:08:51'),
	('6d559868b900ab31b3248be6bf1562759a18fcdd6985e4a56fae398b79fdcf3f475ca4cf4ae6085d', 26, 4, NULL, '[]', 0, '2024-11-10 02:21:31', '2024-11-10 02:21:31', '2025-11-09 21:21:31'),
	('74e2f0f9b9aee5b24f46228f053569a19718dd5ff27e1b70acf0a1bdc18076faa093014885480211', 26, 4, NULL, '[]', 0, '2024-11-17 20:14:08', '2024-11-17 20:14:08', '2025-11-17 15:14:08'),
	('755f2f4a975c156ee86c5a9d708c715dc20d52bf0f804dd3833c2b9171ff6717c0f197f51d69ba0c', 41, 4, NULL, '[]', 0, '2024-11-09 20:07:14', '2024-11-09 20:07:14', '2025-11-09 15:07:14'),
	('7e0efdf55ddc98529e23f955cc366dbf9a1fa2e5069959dda14e5827767d8ae56ba870938ba01976', 39, 4, NULL, '[]', 0, '2024-11-22 04:22:22', '2024-11-22 04:22:22', '2025-11-21 23:22:22'),
	('7f880f0a72f06258e2a5adf9815c4c9c2e93f6142cdf83b48af4c0ea0b0318583b5a77375a04e054', 20, 4, NULL, '[]', 0, '2024-11-08 02:21:31', '2024-11-08 02:21:31', '2025-11-07 21:21:31'),
	('8221baf6ff0c035a2a8f3426791c93d68a9bab9fec86c071ca05d7f57469b5edf28a6cf83c7b5a86', 32, 4, NULL, '[]', 0, '2024-11-08 03:02:08', '2024-11-08 03:02:08', '2025-11-07 22:02:08'),
	('82623f4e36813faac391d2a13830ee426789c24dd3092b2a15d71316cf9e31de0275bc0a648b7da9', 36, 4, NULL, '[]', 0, '2024-11-08 04:27:58', '2024-11-08 04:27:58', '2025-11-07 23:27:58'),
	('8404252b0c2f0d606591cb7cdd2ca002f4d85a407195f4ea92186d017d2c96b2001a63bbb41d0317', 26, 4, NULL, '[]', 0, '2024-11-14 04:15:10', '2024-11-14 04:15:10', '2025-11-13 23:15:10'),
	('8642c17b0d96b646a8fbc659ec7b0d1386881ba83bf07f153e79ec7458a86fcb4dde79f45d45830e', 20, 4, NULL, '[]', 0, '2024-11-07 23:22:47', '2024-11-07 23:22:47', '2025-11-07 18:22:47'),
	('8afb8cfb42faf0d0ab58bfc6775bb7990d516fe6f6068bd87a08f86fb5005bb7baa41e6b3abeced1', 36, 4, NULL, '[]', 0, '2024-11-08 05:09:23', '2024-11-08 05:09:23', '2025-11-08 00:09:23'),
	('95d959cb665c029484c60df89b7ec09266fc60c5b8c2d33e4321b11407d060e4c760c5e09cb48163', 26, 4, NULL, '[]', 0, '2024-11-10 03:25:19', '2024-11-10 03:25:19', '2025-11-09 22:25:19'),
	('9655f2b5c30668bba0ec1cc33791e5730b5d976affcd32c3ef46b3af77bd680e17e3dd19be01a787', 32, 4, NULL, '[]', 0, '2024-11-08 03:01:09', '2024-11-08 03:01:09', '2025-11-07 22:01:09'),
	('9e0b91b91e461355fccddcd3bded30385fe89b0a5809b05a2a901e36613e856d866abbba260f5533', 26, 4, NULL, '[]', 0, '2024-11-10 03:17:59', '2024-11-10 03:17:59', '2025-11-09 22:17:59'),
	('aa0c2b2de770a9dc42a12e1afd04159ecc9a8bcb507265e322092667a67f56f0054ef877b0cfee0c', 20, 4, NULL, '[]', 0, '2024-11-05 02:28:23', '2024-11-05 02:28:23', '2025-11-04 21:28:23'),
	('af6f73aff8a51b3e588792d72669b601682dda71f3db1b6ee21fbe5bc5e784a562486bba9887378a', 41, 4, NULL, '[]', 0, '2024-11-09 20:22:24', '2024-11-09 20:22:24', '2025-11-09 15:22:24'),
	('b478b411b1f8fe726c945fef47d8fc13e3f1c066f3f2155dd6542a5c80f0efaa18ab1a2a745ba871', 11, 4, NULL, '[]', 0, '2024-11-04 22:32:58', '2024-11-04 22:32:58', '2025-11-04 17:32:58'),
	('b56f0a83918fbfb658e4b841e9ea13975e34765c9b8cf4122ddd050668c12bd21086ddb26d2f9811', 26, 4, NULL, '[]', 0, '2024-11-19 19:48:52', '2024-11-19 19:48:52', '2025-11-19 14:48:52'),
	('bb596fce0e67caee57b325ae809f7783bd8d9fbf98ccd78a28e07b5e7d8cde186f5f7eadfe7e6c81', 20, 4, NULL, '[]', 0, '2024-11-05 02:24:19', '2024-11-05 02:24:19', '2025-11-04 21:24:19'),
	('d0af7f051960db899e7411322603ba3998f5ab07acff1cc916f2da0d79653dbc4e85fb77f1e6783f', 39, 4, NULL, '[]', 0, '2024-11-22 04:35:45', '2024-11-22 04:35:45', '2025-11-21 23:35:45'),
	('d7a9c92085fbbe6cb758ae5aca58a557d80a6871be67a2b3e09406776c0d09cd3a64faca21fdbc16', 26, 4, NULL, '[]', 0, '2024-11-10 03:28:28', '2024-11-10 03:28:28', '2025-11-09 22:28:28'),
	('da2a8782e267308bf5d27f92d2d47deaf3c9f6f813ebb37726561f027fa2d465b5c88f62df250551', 40, 4, NULL, '[]', 0, '2024-11-22 01:27:40', '2024-11-22 01:27:40', '2025-11-21 20:27:40'),
	('e5424c625f163045e869228733a59e2ac07d76b921a0b06a5c3a48bff6e41d8454332a92219146af', 31, 4, NULL, '[]', 0, '2024-11-22 04:48:29', '2024-11-22 04:48:29', '2025-11-21 23:48:29'),
	('e61a05eeafacf8971604af43755c74d2b20f410cd7047c0e5e01d559b3b4c42f9c74b5f8cdb37072', 31, 4, NULL, '[]', 0, '2024-11-22 20:19:21', '2024-11-22 20:19:21', '2025-11-22 15:19:21'),
	('e8e4023447b44f0be2a73e27c8f1fff18abec8cbdec6fb7d00734c68f32e027bb4573bbb4c5cd95f', 32, 4, NULL, '[]', 0, '2024-11-08 03:02:59', '2024-11-08 03:02:59', '2025-11-07 22:02:59'),
	('f71ceed2c63aed41b00e014dae47a39645c855faf529ccf45078e5ea8f6278f0fe2eb5a506f55206', 32, 4, NULL, '[]', 0, '2024-11-08 03:04:45', '2024-11-08 03:04:45', '2025-11-07 22:04:45'),
	('f9814e4d5caa5fae5e2d7cff1f9eeff2ad2313f29dd6466046d106caadcb33148060fca052804e00', 40, 4, NULL, '[]', 0, '2024-11-22 01:34:40', '2024-11-22 01:34:40', '2025-11-21 20:34:40'),
	('fcce4f215eb0fe842e24c8f73ad143a379d11cd5f418a280d902cd460f7380ef2e95df61182f3082', 41, 4, NULL, '[]', 0, '2024-11-09 20:24:05', '2024-11-09 20:24:05', '2025-11-09 15:24:05'),
	('fdd7498bae30f53398a6345f9a12af0ae92910433833d24ec1ac51dfb0e0c5010a5838aba92c3474', 29, 4, NULL, '[]', 0, '2024-11-22 01:14:52', '2024-11-22 01:14:52', '2025-11-21 20:14:52'),
	('fe1c5742a51f10c935eedb21094e2324fa5a5a63d2efaa2772de60359db11228a13644074752216f', 31, 4, NULL, '[]', 0, '2024-11-19 20:12:45', '2024-11-19 20:12:45', '2025-11-19 15:12:45');

-- Volcando estructura para tabla todolist.oauth_auth_codes
CREATE TABLE IF NOT EXISTS `oauth_auth_codes` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.oauth_auth_codes: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.oauth_clients
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.oauth_clients: ~0 rows (aproximadamente)
INSERT IGNORE INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'hackaton Personal Access Client', 'LXu2J9NP6lnrk2Jd3MmTzpD4PiFJJ37yRW32rsoI', NULL, 'http://localhost', 1, 0, 0, '2024-11-04 22:19:51', '2024-11-04 22:19:51'),
	(2, NULL, 'hackaton Password Grant Client', 'P2H0ghm1jhiIXYW2TKCeX5W8tIVySSbdrGfyiDoA', 'users', 'http://localhost', 0, 1, 0, '2024-11-04 22:19:52', '2024-11-04 22:19:52'),
	(3, NULL, 'hackaton Personal Access Client', 'SwtpZP8X6SsJd2KJjSP3Qfe2YldxiZv62vP2hNdz', NULL, 'http://localhost', 1, 0, 0, '2024-11-04 22:27:59', '2024-11-04 22:27:59'),
	(4, NULL, 'hackaton Password Grant Client', 'VJi8wbu3t5tiXP7A7e81G8kXq6jK5VxlcLWVIucR', 'users', 'http://localhost', 0, 1, 0, '2024-11-04 22:27:59', '2024-11-04 22:27:59');

-- Volcando estructura para tabla todolist.oauth_personal_access_clients
CREATE TABLE IF NOT EXISTS `oauth_personal_access_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.oauth_personal_access_clients: ~0 rows (aproximadamente)
INSERT IGNORE INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
	(1, 1, '2024-11-04 22:19:51', '2024-11-04 22:19:51'),
	(2, 3, '2024-11-04 22:27:59', '2024-11-04 22:27:59');

-- Volcando estructura para tabla todolist.oauth_refresh_tokens
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.oauth_refresh_tokens: ~49 rows (aproximadamente)
INSERT IGNORE INTO `oauth_refresh_tokens` (`id`, `access_token_id`, `revoked`, `expires_at`) VALUES
	('060874c2b6b403207501dd8307edfd5243a4cacd75fd5ff77084c15ad635d65dd145420a8a352350', '615723747b788dcac70e5d1d083ea1844ee1bdb25b95f8532106f16993980b5481f7daef9f0096b5', 0, '2025-11-07 22:00:57'),
	('0cb635686ebc8cf4f68b6197827fc4436cbda3562a8fab00832a7ccd41f9e558d5d5af7d9a4fda8e', '498dbc1e884e491d063f6333af1ffb00e4f27ae695ce249363a497b6daf32ca887f953598c17a02c', 0, '2025-11-09 22:17:55'),
	('13b57b96f2b72b3c60a6640a2982f4fa2d9989684482ede777d1a24d739f5a4e95187454e16a07cb', '65055e850db8dcc0bd058f3edf5aba5f10decfe996a5babaaa19bf8f7e146d85af675a216b243f55', 0, '2025-11-22 01:45:12'),
	('1731d07bd397168105ffde741f9e304b31c97775ee82a68dbcf23cf64abb420412abe88b0c42ca96', '7e0efdf55ddc98529e23f955cc366dbf9a1fa2e5069959dda14e5827767d8ae56ba870938ba01976', 0, '2025-11-21 23:22:22'),
	('17ff7e23644f38035b40749961385c7c5c257469f28550c51d1d40738f13e1983eff0647d5b867bd', '41e1c96b119dce03f593f7afaddfe1faf866eb0d14be5a209c9fb7e2641d515f72c2dcb1428b141a', 0, '2025-11-21 20:28:12'),
	('1e08e6db22194dabd5e0dae094232e2a51056b2f01b8da2c8440b87e70e6b4c7669bdee09a814a3c', '3fed3675a1b5e142a207e4af1c6b348537a2559021a5f987013c03bf2df818d11e1da219c80fe7a3', 0, '2025-11-21 20:01:01'),
	('2108642d5a24c44badbf72b209877d5f057e2a49295b72703a021095c0da6b27abe7903becf77065', '141eb555bb54ecdc33915145b9ee767ebf86393404c1665dfa8f7030d603140c6f56dd26ad694a93', 0, '2025-11-04 17:30:55'),
	('21e8c8dbc8241a28f745d67c18f39603c42816661bc72ad2ac46a764b2feb0a998d7c9c43a8c13a8', 'af6f73aff8a51b3e588792d72669b601682dda71f3db1b6ee21fbe5bc5e784a562486bba9887378a', 0, '2025-11-09 15:22:24'),
	('21ea73bc74d5080004449bb6a3e8a40fd31594a99b952335756ca89e7536043d6c7e9b46c95da80a', '755f2f4a975c156ee86c5a9d708c715dc20d52bf0f804dd3833c2b9171ff6717c0f197f51d69ba0c', 0, '2025-11-09 15:07:14'),
	('24e02bebb27df35e01be30394f14909faf090a1e26a6f9fdf0de53a4537bf7372e97b44816fb58f8', '47404e48fbca410e07ef282b6fbc82db0a4e1b65331d3863672101fe72a618e17b6175f2b2153db8', 0, '2025-11-09 22:22:21'),
	('267b360f87521c755bcf6eb3ca4b7377b744b04a6ffb255d5fe24aeaffe8040b517f7caeb12559bf', 'b478b411b1f8fe726c945fef47d8fc13e3f1c066f3f2155dd6542a5c80f0efaa18ab1a2a745ba871', 0, '2025-11-04 17:32:58'),
	('2ae35897d7bf7763992449eccae098f8db76787ebde6984a5743579b810a71f4c4f7933f113cf254', 'fe1c5742a51f10c935eedb21094e2324fa5a5a63d2efaa2772de60359db11228a13644074752216f', 0, '2025-11-19 15:12:46'),
	('3407aa2cb41905477fec207db5c939143f84665f34bee650f5f8d0d2b9dc9156c8d80d6e9eb3d703', '9e0b91b91e461355fccddcd3bded30385fe89b0a5809b05a2a901e36613e856d866abbba260f5533', 0, '2025-11-09 22:17:59'),
	('35304b7f87cc897e8dbaada6196216ab8d52e01f7a1674b6a7b626cdb7fbac7c36edfe8f8844a2e8', 'bb596fce0e67caee57b325ae809f7783bd8d9fbf98ccd78a28e07b5e7d8cde186f5f7eadfe7e6c81', 0, '2025-11-04 21:24:19'),
	('37d3fffcf86646edb9a2774ef7dcbe37e7ccbbc0cde06d5a9bdd9f9639be464a200d4e4f7b13890b', 'da2a8782e267308bf5d27f92d2d47deaf3c9f6f813ebb37726561f027fa2d465b5c88f62df250551', 0, '2025-11-21 20:27:40'),
	('3840d5ffb97871367d55489314ce89b20acbbd309b5b659c02511154e4a56b2e785abb96e0711533', '8221baf6ff0c035a2a8f3426791c93d68a9bab9fec86c071ca05d7f57469b5edf28a6cf83c7b5a86', 0, '2025-11-07 22:02:08'),
	('38ca55aee58dfd800c311789484616bd488d83c972f0b2c3e7ede840ae93086ccd7a494e23ac9f79', '5c63748af6c6238e2b0fc8975848d9ec2712c10854b0f96dfa144dbdc51b73eb9aa193f473f4cf9b', 0, '2025-11-07 21:25:52'),
	('396424b96d3aad815b96b350f68b5192be6703dba545a6bc2dcf3544c6bdc785da6eb9ab9723abb7', '8404252b0c2f0d606591cb7cdd2ca002f4d85a407195f4ea92186d017d2c96b2001a63bbb41d0317', 0, '2025-11-13 23:15:10'),
	('41916d3b1057ee437061951e433673319bb1cd15f5239ddb385ee21b0541be4cfeae2805a1442742', '651f8944bc890ad24f77dce74726943160af89a8d291804f9ec162637b37f69ebd17054adc0d0600', 0, '2025-11-22 03:50:35'),
	('430ed2822c33837eabb144d24be08db69051e29d834cc3539180ffdbfde66eb7de801b37fd80f399', 'e61a05eeafacf8971604af43755c74d2b20f410cd7047c0e5e01d559b3b4c42f9c74b5f8cdb37072', 0, '2025-11-22 15:19:21'),
	('47a1700e1ed62559b8b67430c17ab8619feea07aff0d55d7c31c72b3b44c3e4968c8de87bd34c826', '6d559868b900ab31b3248be6bf1562759a18fcdd6985e4a56fae398b79fdcf3f475ca4cf4ae6085d', 0, '2025-11-09 21:21:31'),
	('4e1e5f69372e6023a23e90218e1fdf5a2b32e451ceef9abc23217e83bff355e482cae33d92a19b38', 'e5424c625f163045e869228733a59e2ac07d76b921a0b06a5c3a48bff6e41d8454332a92219146af', 0, '2025-11-21 23:48:29'),
	('54637cad75aff966a172579f80180b3356b71e2ed883ab4ef119aea33231e8ce8ce3b90c079f3131', 'b56f0a83918fbfb658e4b841e9ea13975e34765c9b8cf4122ddd050668c12bd21086ddb26d2f9811', 0, '2025-11-19 14:48:52'),
	('570d1a42272acc670e74fcbc068baf1fbc44dd83c6ffcf59088d59764019845eb2e1af7a645f690d', '7f880f0a72f06258e2a5adf9815c4c9c2e93f6142cdf83b48af4c0ea0b0318583b5a77375a04e054', 0, '2025-11-07 21:21:31'),
	('7339afb50454e9e3280f358ca5e6ae70eee88714335f73f316e9b4c655ba94999fe8a2f060c5cea6', '25f1e042250e4b8e70955c34d2451c859637d1b81ce1d2e426c7c4f1e154869f1787a885a0f4ed3a', 0, '2025-11-05 22:14:26'),
	('76532b0e1c67db44abef53396a31b96a47d29491f9ffbb21453b2003fdfd2c1c42197ba7029e3451', '29399eed426f5394ef1d235170eb8e76238a060ce6bfe66158a864626ce301187a8ebb7e1b7b1f41', 0, '2025-11-07 23:30:03'),
	('7b3beb170a3d58b4763a38ea681a4fce6eee77d0169948f5113271ac62f1ffb3b155b602c2dd2822', 'fcce4f215eb0fe842e24c8f73ad143a379d11cd5f418a280d902cd460f7380ef2e95df61182f3082', 0, '2025-11-09 15:24:05'),
	('7e4241e4e7245c4a79ac6a6a26c942eb1bf851b58835e08e150457d86200b13f62b80d8b062b0bd0', '74e2f0f9b9aee5b24f46228f053569a19718dd5ff27e1b70acf0a1bdc18076faa093014885480211', 0, '2025-11-17 15:14:08'),
	('7e4ea1c02c8797702f9743cb4ac7ac2627d14533b3bc54b6061490022a1e32d52b8f4f09414a4320', '1fa15a2e9721b5d629d488d3bdd747665dc9e41cd2aabd77fc77fa41194020d873fcbc5992c897ed', 0, '2025-11-21 19:52:55'),
	('a2085f46c302371bf3c9b95169f299c6ba6bb9b66ba9c313f02b300f4bdeb9568cbca09546e3de14', 'd0af7f051960db899e7411322603ba3998f5ab07acff1cc916f2da0d79653dbc4e85fb77f1e6783f', 0, '2025-11-21 23:35:46'),
	('a6cd366a52c41e5a5ff4d91eabd5d98f7f29a041b35ab163cba0e92b26298f163a5d7f4cdbc6fe86', '11fac23631570d5104a5ee57afe867986e32956c574b6f02779276115e3b5e72001d583d95728bf0', 0, '2025-11-21 23:15:26'),
	('ae0992a1e75e510e9d2ad759ed612058051baf768dc9a515c3bae074d18f58b24dc032edea7737d7', '4875b85e0480ab3ce68f24a16fccae6cf98b1e2d4f8dcc942a5b540e4837ff6752a320bc5c0530b3', 0, '2025-11-07 22:59:14'),
	('afb8d0c250ab8defedf2e02a61eedaccab6bdc43b514f4ed3226d5f84a694d81ff72291428096a68', '66f9c0e80caf74b3b02843ff041cf0bbfc2b99d21ff0c571eab929c77a7288620cdb5ee7dbca6c9f', 0, '2025-11-22 15:08:51'),
	('b007c498d3997a436beff70ed1ac0d1fe11b2b0ea18aa92ceaa6c4b8e14a5140c3055e1a4c479132', '8afb8cfb42faf0d0ab58bfc6775bb7990d516fe6f6068bd87a08f86fb5005bb7baa41e6b3abeced1', 0, '2025-11-08 00:09:23'),
	('b0827e70e665b7693899ccbc3e2706d05e50f224637f38fb2292b4461e101e62daa3da944a55d928', '3ad10b1e339525c879606d19bd9ffc7faac8a0332174075d59a2020bdad688d7dc3987fdf0ca0268', 0, '2025-11-07 22:58:05'),
	('b6bab19247f67d930950612d0f35470ef0b23fd586a1eeab4f8516906201b520fb9ee65d80d57b28', '5eb9b342d5c332cea82b7df87a9366a6f04868a82267d1d52d33dc2326387d58ad645bd06240f124', 0, '2025-11-09 01:33:42'),
	('ba364463c002d3add0882833d4947b9c0a12b267bdecc810c2568d3dc77c66e63c875f358e33c4a7', 'd7a9c92085fbbe6cb758ae5aca58a557d80a6871be67a2b3e09406776c0d09cd3a64faca21fdbc16', 0, '2025-11-09 22:28:28'),
	('bdf827123ef136d8f6f94b1bcce9b9e28980cea0d1d65a95b03361e5ba38f039a35798ca6dba2f03', '1ed410a5b782d410c990c743b36569e14c76d9b29d0111f926e0af3175b0de6c20cc8b7fab99a3ee', 0, '2025-11-09 15:47:16'),
	('bfa6ab904ebceba28b2296be1b73025518554e24d20fdd3d617c133d791597867f486c0d3a1507bb', '4d2778b453cb3ac6ccfa0466c32398cdcda4ff1d9dccca80ad10a156c08f8b54ecd100d7972eaf6f', 0, '2025-11-05 22:18:46'),
	('bfa929ba15a185bc4a0efc715ed18c4a3af7ef5145bd5d3fc70ab711a931083638f8530c6d274ca2', '82623f4e36813faac391d2a13830ee426789c24dd3092b2a15d71316cf9e31de0275bc0a648b7da9', 0, '2025-11-07 23:27:58'),
	('cb9818eea22552461b9c6e4c6b2e490a14e2271868be3557181f7c60455ad70f14cc5c51f19e3932', 'f71ceed2c63aed41b00e014dae47a39645c855faf529ccf45078e5ea8f6278f0fe2eb5a506f55206', 0, '2025-11-07 22:04:45'),
	('d1184d9cc1c832b92fa7fde771d3ccb1590212c0064c9725e3c4cd30c0b35d845e1455cb7c7e9959', 'f9814e4d5caa5fae5e2d7cff1f9eeff2ad2313f29dd6466046d106caadcb33148060fca052804e00', 0, '2025-11-21 20:34:40'),
	('d173742d8f479c5f5d07355f74fcb6323e48051111fae47ae90bd2e442d91253c770831c7d0f13a8', 'fdd7498bae30f53398a6345f9a12af0ae92910433833d24ec1ac51dfb0e0c5010a5838aba92c3474', 0, '2025-11-21 20:14:52'),
	('d2409e45bfa21f34c1b0d131861fefc33c9cb3e11d5f7346fe3eb0832652625335bf42c2314263c6', '1478de3cae18534189f54a5ab1ae160fb4d582c8798daeac55837872feae881a0ea1fb6af5cbe01d', 0, '2025-11-04 21:38:45'),
	('d9cf4ccf7c2d3c26c41f25f4183b3ddab52e80b0fc33a47c8bc4e0b212e3838cf38bf588ddd6ccc5', 'e8e4023447b44f0be2a73e27c8f1fff18abec8cbdec6fb7d00734c68f32e027bb4573bbb4c5cd95f', 0, '2025-11-07 22:02:59'),
	('e505d9b89843e3f8b37ded9558ede76718b58d7a44b3c4c9eee947c352a11da2a0de300dd5762204', 'aa0c2b2de770a9dc42a12e1afd04159ecc9a8bcb507265e322092667a67f56f0054ef877b0cfee0c', 0, '2025-11-04 21:28:23'),
	('ea1d3db1cc23957879b617cb3d04ce80de6bf20229d41ddbee3f48e8452436d3224bd1a09902576b', '9655f2b5c30668bba0ec1cc33791e5730b5d976affcd32c3ef46b3af77bd680e17e3dd19be01a787', 0, '2025-11-07 22:01:10'),
	('f3a51ee3c9d55b98c7f1ae0d41b36ea1d6f369420c74b7cd89da9dc2997db96aaf629ea09433e613', '95d959cb665c029484c60df89b7ec09266fc60c5b8c2d33e4321b11407d060e4c760c5e09cb48163', 0, '2025-11-09 22:25:19'),
	('fd453cfb70456c4004cce420649dcd35181964fbe87bf50db0fd6fe4c557a2cbd0761c68e6af523b', '8642c17b0d96b646a8fbc659ec7b0d1386881ba83bf07f153e79ec7458a86fcb4dde79f45d45830e', 0, '2025-11-07 18:22:47');

-- Volcando estructura para tabla todolist.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.permissions: ~16 rows (aproximadamente)
INSERT IGNORE INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Modulo_Dashboard', 'web', '2024-06-07 21:45:43', '2024-06-07 21:45:43'),
	(2, 'Modulo_Administracion', 'web', '2024-06-07 21:46:26', '2024-06-07 21:46:26'),
	(16, 'Agregar_Permiso', 'web', '2024-08-09 21:08:54', '2024-08-09 21:08:54'),
	(17, 'Eliminar_Permiso', 'web', '2024-08-09 21:09:00', '2024-08-09 21:09:00'),
	(18, 'Editar_Permiso', 'web', '2024-08-09 21:09:10', '2024-08-09 21:09:10'),
	(19, 'Agregar_Rol', 'web', '2024-08-09 21:09:24', '2024-08-09 21:09:24'),
	(20, 'Editar_Rol', 'web', '2024-08-09 21:09:29', '2024-08-09 21:09:29'),
	(21, 'Eliminar_Rol', 'web', '2024-08-09 21:09:35', '2024-08-09 21:09:35'),
	(22, 'Asignar_Permisos', 'web', '2024-08-09 21:13:32', '2024-08-09 21:13:32'),
	(23, 'Agregar_Usuario', 'web', '2024-08-09 21:17:16', '2024-08-09 21:17:16'),
	(24, 'Editar_Usuario', 'web', '2024-08-09 21:17:21', '2024-08-09 21:17:21'),
	(25, 'Eliminar_Usuario', 'web', '2024-08-09 21:17:26', '2024-08-09 21:17:26'),
	(26, 'Asignar_Rol', 'web', '2024-08-09 21:17:38', '2024-08-09 21:17:38'),
	(34, 'Modulo_Log', 'web', '2024-08-10 02:08:39', '2024-08-10 02:08:39'),
	(35, 'Modulo_Utilidades', 'web', '2024-08-10 02:14:44', '2024-08-10 02:14:44'),
	(46, 'Cambiar_password', 'web', '2024-11-20 21:01:51', '2024-11-20 21:01:51'),
	(48, 'Agregar_Evento', 'web', '2024-11-20 21:02:03', '2024-11-20 21:02:03'),
	(49, 'Editar_Evento', 'web', '2024-11-20 21:02:07', '2024-11-20 21:02:07'),
	(50, 'Eliminar_Evento', 'web', '2024-11-20 21:02:13', '2024-11-20 21:02:13'),
	(51, 'Agregar_Acceso', 'web', '2024-11-20 21:02:19', '2024-11-20 21:02:19'),
	(52, 'Editar_Acceso', 'web', '2024-11-20 21:02:24', '2024-11-20 21:02:24');

-- Volcando estructura para tabla todolist.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.personal_access_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.priorities
CREATE TABLE IF NOT EXISTS `priorities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.priorities: ~4 rows (aproximadamente)
INSERT IGNORE INTO `priorities` (`id`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'Baja', '2025-02-04 20:47:12', '2025-02-04 20:47:13'),
	(2, 'Media', '2025-02-04 20:47:20', '2025-02-05 02:38:40'),
	(3, 'Alta', '2025-02-04 20:47:29', '2025-02-04 20:47:30'),
	(4, 'Sin Prioridad', '2025-02-04 20:47:37', '2025-02-04 20:47:37');

-- Volcando estructura para tabla todolist.respaldos
CREATE TABLE IF NOT EXISTS `respaldos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `size` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `archivo` varchar(750) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `adjunto` varchar(750) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.respaldos: ~11 rows (aproximadamente)

-- Volcando estructura para tabla todolist.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.roles: ~3 rows (aproximadamente)
INSERT IGNORE INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'web', '2024-06-07 23:58:32', '2024-06-07 23:58:32'),
	(2, 'Usuario', 'web', '2024-06-07 23:58:37', '2024-06-07 23:58:37'),
	(8, 'Root', 'web', '2024-08-29 23:15:02', '2024-08-29 23:15:02');

-- Volcando estructura para tabla todolist.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.role_has_permissions: ~17 rows (aproximadamente)
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 1),
	(23, 1),
	(24, 1),
	(25, 1),
	(1, 2),
	(1, 8),
	(2, 8),
	(16, 8),
	(17, 8),
	(18, 8),
	(19, 8),
	(20, 8),
	(21, 8),
	(22, 8),
	(23, 8),
	(24, 8),
	(25, 8),
	(26, 8),
	(34, 8),
	(35, 8),
	(46, 8);

-- Volcando estructura para tabla todolist.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.sessions: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.shared_tasks
CREATE TABLE IF NOT EXISTS `shared_tasks` (
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`task_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `shared_tasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shared_tasks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.shared_tasks: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.status
CREATE TABLE IF NOT EXISTS `status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.status: ~5 rows (aproximadamente)
INSERT IGNORE INTO `status` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'No iniciada', '2025-02-04 20:51:00', '2025-02-04 20:51:00'),
	(2, 'Pause', '2025-02-04 20:51:06', '2025-02-05 01:51:07'),
	(3, 'En Progreso', '2025-02-04 00:00:00', '2025-02-04 20:51:16'),
	(4, 'Revisión', '2025-02-04 20:51:21', '2025-02-04 20:51:22'),
	(5, 'Completada', '2025-02-04 20:51:26', '2025-02-04 20:51:27');

-- Volcando estructura para tabla todolist.tasks
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci,
  `priority_id` int DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  `task_type_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `user_assignments` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.tasks: ~0 rows (aproximadamente)

-- Volcando estructura para tabla todolist.task_types
CREATE TABLE IF NOT EXISTS `task_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Volcando datos para la tabla todolist.task_types: ~4 rows (aproximadamente)
INSERT IGNORE INTO `task_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 'Nueva Mejora', '2025-02-05 01:53:11', '2025-02-05 01:53:47'),
	(2, 'Actualización', '2025-02-05 01:53:27', '2025-02-05 01:53:27'),
	(3, 'Error', '2025-02-05 01:53:53', '2025-02-05 01:53:53'),
	(4, 'Otra', '2025-02-05 01:54:04', '2025-02-05 01:54:04');

-- Volcando estructura para tabla todolist.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla todolist.users: ~1 rows (aproximadamente)
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(20, 'Edgar Araos', 'usuario@gmail.com', NULL, '$2y$12$I3YG2eU6Zj8zbWVUQOIhUeKimmaUZpmnGg5MZOqIT81b6FoYK76ZG', NULL, '2024-08-29 23:17:35', '2025-02-06 02:55:09'),
	(48, 'Prueba de Usuario', 'prueba@gmail.com', NULL, '$2y$12$qd5K3pVic5o8zX/D6fRmy.JvRDiaDgitijBp9O4e9mqqo.9KQLXci', NULL, '2025-02-06 02:49:15', '2025-02-06 02:49:15');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
