-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.0.30 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Copiando estrutura para tabela chamadosv2.addresses
DROP TABLE IF EXISTS `addresses`;
CREATE TABLE IF NOT EXISTS `addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `postal_code` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `neighborhood` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_company_id_foreign` (`company_id`),
  CONSTRAINT `addresses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.addresses: ~0 rows (aproximadamente)
DELETE FROM `addresses`;
INSERT INTO `addresses` (`id`, `postal_code`, `street`, `number`, `complement`, `neighborhood`, `city`, `state`, `created_at`, `updated_at`, `company_id`) VALUES
	(1, '78452-034', 'Avenida Mutum', '1250', 'N', 'Jardim das Orquídeas', 'Nova Mutum', 'MT', '2025-05-13 01:23:16', '2025-05-13 01:23:16', NULL);

-- Copiando estrutura para tabela chamadosv2.agreement_items
DROP TABLE IF EXISTS `agreement_items`;
CREATE TABLE IF NOT EXISTS `agreement_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `price_agreement_id` bigint unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('part','service') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agreement_items_price_agreement_id_foreign` (`price_agreement_id`),
  CONSTRAINT `agreement_items_price_agreement_id_foreign` FOREIGN KEY (`price_agreement_id`) REFERENCES `price_agreements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.agreement_items: ~0 rows (aproximadamente)
DELETE FROM `agreement_items`;

-- Copiando estrutura para tabela chamadosv2.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.cache: ~3 rows (aproximadamente)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('spatie.permission.cache', 'a:3:{s:5:"alias";a:4:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:28:{i:0;a:4:{s:1:"a";i:25;s:1:"b";s:9:"view user";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:8;i:1;i:10;}}i:1;a:4:{s:1:"a";i:26;s:1:"b";s:11:"create user";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:8;i:1;i:10;}}i:2;a:4:{s:1:"a";i:27;s:1:"b";s:11:"update user";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:8;i:1;i:10;}}i:3;a:4:{s:1:"a";i:28;s:1:"b";s:11:"delete user";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:8;i:1;i:10;}}i:4;a:4:{s:1:"a";i:29;s:1:"b";s:15:"view department";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:8;i:1;i:10;}}i:5;a:4:{s:1:"a";i:30;s:1:"b";s:17:"create department";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:8;i:1;i:10;}}i:6;a:4:{s:1:"a";i:31;s:1:"b";s:17:"update department";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:8;i:1;i:10;}}i:7;a:4:{s:1:"a";i:32;s:1:"b";s:17:"delete department";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:8;i:1;i:10;}}i:8;a:4:{s:1:"a";i:33;s:1:"b";s:12:"view address";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:8;i:1;i:9;i:2;i:10;}}i:9;a:4:{s:1:"a";i:34;s:1:"b";s:14:"create address";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:8;i:1;i:9;i:2;i:10;}}i:10;a:4:{s:1:"a";i:35;s:1:"b";s:14:"update address";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:8;i:1;i:9;i:2;i:10;}}i:11;a:4:{s:1:"a";i:36;s:1:"b";s:14:"delete address";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:8;i:1;i:9;i:2;i:10;}}i:12;a:4:{s:1:"a";i:37;s:1:"b";s:12:"view company";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:8;}}i:13;a:3:{s:1:"a";i:38;s:1:"b";s:14:"create company";s:1:"c";s:3:"web";}i:14;a:3:{s:1:"a";i:39;s:1:"b";s:14:"update company";s:1:"c";s:3:"web";}i:15;a:4:{s:1:"a";i:40;s:1:"b";s:14:"delete company";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:10;}}i:16;a:3:{s:1:"a";i:41;s:1:"b";s:15:"view permission";s:1:"c";s:3:"web";}i:17;a:3:{s:1:"a";i:42;s:1:"b";s:17:"create permission";s:1:"c";s:3:"web";}i:18;a:3:{s:1:"a";i:43;s:1:"b";s:17:"update permission";s:1:"c";s:3:"web";}i:19;a:3:{s:1:"a";i:44;s:1:"b";s:17:"delete permission";s:1:"c";s:3:"web";}i:20;a:3:{s:1:"a";i:45;s:1:"b";s:9:"view role";s:1:"c";s:3:"web";}i:21;a:3:{s:1:"a";i:46;s:1:"b";s:11:"create role";s:1:"c";s:3:"web";}i:22;a:3:{s:1:"a";i:47;s:1:"b";s:11:"update role";s:1:"c";s:3:"web";}i:23;a:3:{s:1:"a";i:48;s:1:"b";s:11:"delete role";s:1:"c";s:3:"web";}i:24;a:4:{s:1:"a";i:49;s:1:"b";s:11:"view sector";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:10;}}i:25;a:4:{s:1:"a";i:50;s:1:"b";s:13:"create sector";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:10;}}i:26;a:4:{s:1:"a";i:51;s:1:"b";s:13:"update sector";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:10;}}i:27;a:4:{s:1:"a";i:52;s:1:"b";s:13:"delete sector";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:10;}}}s:5:"roles";a:3:{i:0;a:3:{s:1:"a";i:8;s:1:"b";s:11:"Super Admin";s:1:"c";s:3:"web";}i:1;a:3:{s:1:"a";i:10;s:1:"b";s:7:"Gerente";s:1:"c";s:3:"web";}i:2;a:3:{s:1:"a";i:9;s:1:"b";s:8:"Operador";s:1:"c";s:3:"web";}}}', 1747362422);

-- Copiando estrutura para tabela chamadosv2.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Copiando estrutura para tabela chamadosv2.companies
DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `corporate_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trade_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_registration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `address_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_cnpj_unique` (`cnpj`),
  KEY `companies_address_id_foreign` (`address_id`),
  CONSTRAINT `companies_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.companies: ~0 rows (aproximadamente)
DELETE FROM `companies`;
INSERT INTO `companies` (`id`, `corporate_name`, `trade_name`, `state_registration`, `cnpj`, `phone`, `email`, `is_active`, `address_id`, `created_at`, `updated_at`) VALUES
	(1, 'PREFEITURA MUNICIPAL DE NOVA MUTUM', 'PREFEITURA MUNICIPAL DE NOVA MUTUM', NULL, '24772162000106', '00000000000', '', 1, 1, '2025-05-13 01:23:40', '2025-05-13 23:38:07');

-- Copiando estrutura para tabela chamadosv2.departments
DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `address_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_company_id_foreign` (`company_id`),
  KEY `departments_address_id_foreign` (`address_id`),
  CONSTRAINT `departments_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.departments: ~2 rows (aproximadamente)
DELETE FROM `departments`;
INSERT INTO `departments` (`id`, `company_id`, `address_id`, `name`, `contact_person`, `cell_phone`, `extension`, `email`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 'SECRETARIA DE ADMINISTRAÇÃO', 'ILDO', '', NULL, NULL, 1, '2025-05-13 01:24:30', '2025-05-13 01:24:30'),
	(2, 1, NULL, 'SECRETÁRIA DE SAÚDE', 'SÔNIA', '', NULL, NULL, 1, '2025-05-13 01:24:55', '2025-05-13 01:24:55');

-- Copiando estrutura para tabela chamadosv2.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Copiando estrutura para tabela chamadosv2.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Copiando estrutura para tabela chamadosv2.job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Copiando estrutura para tabela chamadosv2.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.migrations: ~10 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_05_08_173402_create_permission_tables', 1),
	(5, '2025_05_08_200606_create_addresses_table', 1),
	(6, '2025_05_08_200607_create_companies_table', 1),
	(7, '2025_05_08_200709_add_company_id_to_users_table', 1),
	(8, '2025_05_09_135947_create_departments_table', 1),
	(9, '2025_05_12_112455_add_company_id_to_addresses_table', 1),
	(10, '2025_05_12_213250_create_sectors_table', 2),
	(11, '2025_05_12_213252_create_suppliers_table', 3),
	(12, '2025_05_12_213253_create_price_agreements_table', 3),
	(13, '2025_05_12_213255_create_agreement_items_table', 3);

-- Copiando estrutura para tabela chamadosv2.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.model_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `model_has_permissions`;

-- Copiando estrutura para tabela chamadosv2.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.model_has_roles: ~2 rows (aproximadamente)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(10, 'App\\Models\\User', 2),
	(9, 'App\\Models\\User', 3),
	(8, 'App\\Models\\User', 6);

-- Copiando estrutura para tabela chamadosv2.password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Copiando estrutura para tabela chamadosv2.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.permissions: ~28 rows (aproximadamente)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(25, 'view user', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(26, 'create user', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(27, 'update user', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(28, 'delete user', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(29, 'view department', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(30, 'create department', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(31, 'update department', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(32, 'delete department', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(33, 'view address', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(34, 'create address', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(35, 'update address', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(36, 'delete address', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(37, 'view company', 'web', '2025-05-13 00:00:09', '2025-05-13 00:00:09'),
	(38, 'create company', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(39, 'update company', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(40, 'delete company', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(41, 'view permission', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(42, 'create permission', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(43, 'update permission', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(44, 'delete permission', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(45, 'view role', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(46, 'create role', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(47, 'update role', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(48, 'delete role', 'web', '2025-05-13 00:19:50', '2025-05-13 00:19:50'),
	(49, 'view sector', 'web', '2025-05-13 01:40:31', '2025-05-13 01:40:31'),
	(50, 'create sector', 'web', '2025-05-13 01:40:31', '2025-05-13 01:40:31'),
	(51, 'update sector', 'web', '2025-05-13 01:40:31', '2025-05-13 01:40:31'),
	(52, 'delete sector', 'web', '2025-05-13 01:40:31', '2025-05-13 01:40:31');

-- Copiando estrutura para tabela chamadosv2.price_agreements
DROP TABLE IF EXISTS `price_agreements`;
CREATE TABLE IF NOT EXISTS `price_agreements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  `signature_date` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `object` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `company_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `price_agreements_supplier_id_foreign` (`supplier_id`),
  KEY `price_agreements_company_id_foreign` (`company_id`),
  CONSTRAINT `price_agreements_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `price_agreements_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.price_agreements: ~0 rows (aproximadamente)
DELETE FROM `price_agreements`;

-- Copiando estrutura para tabela chamadosv2.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.roles: ~3 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(8, 'Super Admin', 'web', '2025-05-12 23:41:01', '2025-05-12 23:41:01'),
	(9, 'Operador', 'web', '2025-05-13 00:04:21', '2025-05-13 00:04:22'),
	(10, 'Gerente', 'web', '2025-05-13 00:22:59', '2025-05-13 00:22:59');

-- Copiando estrutura para tabela chamadosv2.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.role_has_permissions: ~30 rows (aproximadamente)
DELETE FROM `role_has_permissions`;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(25, 8),
	(26, 8),
	(27, 8),
	(28, 8),
	(29, 8),
	(30, 8),
	(31, 8),
	(32, 8),
	(33, 8),
	(34, 8),
	(35, 8),
	(36, 8),
	(37, 8),
	(33, 9),
	(34, 9),
	(35, 9),
	(36, 9),
	(25, 10),
	(26, 10),
	(27, 10),
	(28, 10),
	(29, 10),
	(30, 10),
	(31, 10),
	(32, 10),
	(33, 10),
	(34, 10),
	(35, 10),
	(36, 10),
	(40, 10),
	(49, 10),
	(50, 10),
	(51, 10),
	(52, 10);

-- Copiando estrutura para tabela chamadosv2.sectors
DROP TABLE IF EXISTS `sectors`;
CREATE TABLE IF NOT EXISTS `sectors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint unsigned NOT NULL,
  `address_id` bigint unsigned DEFAULT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsible` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sectors_department_id_foreign` (`department_id`),
  KEY `sectors_address_id_foreign` (`address_id`),
  CONSTRAINT `sectors_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sectors_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.sectors: ~2 rows (aproximadamente)
DELETE FROM `sectors`;
INSERT INTO `sectors` (`id`, `name`, `department_id`, `address_id`, `extension`, `cell_phone`, `responsible`, `email`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'ESF JARDIM II', 2, NULL, '0000', '00000000000', 'JEOVÁ', 'jeovacnp2012@gmail.com', 1, '2025-05-13 22:46:54', '2025-05-13 23:56:04'),
	(2, 'ESF ARARAS', 2, NULL, NULL, NULL, '', '', 1, '2025-05-13 22:47:20', '2025-05-13 22:47:20'),
	(3, 'PATRIMÔNIO GERAL', 1, NULL, NULL, NULL, '', '', 1, '2025-05-13 22:47:39', '2025-05-13 22:47:39');

-- Copiando estrutura para tabela chamadosv2.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.sessions: ~2 rows (aproximadamente)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('yChfYlcQkbOD43xjbDgl3Cb3rpmVjfQEOFwiP11b', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTo3OntzOjM6InVybCI7YTowOnt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcmljZS1hZ3JlZW1lbnRzL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NjoiX3Rva2VuIjtzOjQwOiJxZGIzVVJ2V2VoNTYwcDA5eVhiOG9YdzhicU9QckN3bE9HbE5Ha0M4IjtzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo2O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkaWVyMGlkNUN6RnV2TVEvQlNCWEZ0LnFMTXgwMXFCTXEuNVBvbVQuZXhtWWgycE5jWEtBZy4iO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=', 1747276369);

-- Copiando estrutura para tabela chamadosv2.suppliers
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `corporate_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trade_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_registration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_id` bigint unsigned DEFAULT NULL,
  `cell_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `company_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_cnpj_unique` (`cnpj`),
  KEY `suppliers_address_id_foreign` (`address_id`),
  KEY `suppliers_company_id_foreign` (`company_id`),
  CONSTRAINT `suppliers_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.suppliers: ~1 rows (aproximadamente)
DELETE FROM `suppliers`;
INSERT INTO `suppliers` (`id`, `corporate_name`, `trade_name`, `cnpj`, `state_registration`, `address_id`, `cell_phone`, `email`, `is_active`, `company_id`, `created_at`, `updated_at`) VALUES
	(1, 'TESTE1', 'TESTE1', '06504329000176', NULL, 1, NULL, NULL, 1, 1, '2025-05-14 23:15:28', '2025-05-14 23:15:28');

-- Copiando estrutura para tabela chamadosv2.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.users: ~3 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `company_id`) VALUES
	(2, 'GERENTE', 'gerente@gerente.com', NULL, '$2y$12$NQI9N4Diu382LhL2zgszLOzKqPK1WA3piZwZDKRy/2LgnxSCpT8P.', NULL, '2025-05-12 21:48:57', '2025-05-15 02:32:04', 1),
	(3, 'TESTE', 'teste@gmail.com', NULL, '$2y$12$.CMkycBeSFlxN9fp.83/mecmAw0lNsQD.Vm1llrL2erKpu/RSsXJu', NULL, '2025-05-12 22:21:58', '2025-05-15 02:21:28', 1),
	(6, 'JEOVÁ OLIVEIRA DOS SANTOS', 'jeovacnp2012@gmail.com', NULL, '$2y$12$ier0id5CzFuvMQ/BSBXFt.qLMx01qBMq.5PomT.exmYh2pNcXKAg.', NULL, '2025-05-13 01:13:51', '2025-05-15 02:09:44', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
