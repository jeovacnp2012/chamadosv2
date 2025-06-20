-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.6.0.6765
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
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `postal_code` varchar(9) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) DEFAULT NULL,
  `complement` varchar(255) DEFAULT NULL,
  `neighborhood` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_company_id_foreign` (`company_id`),
  CONSTRAINT `addresses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.agreement_items
DROP TABLE IF EXISTS `agreement_items`;
CREATE TABLE IF NOT EXISTS `agreement_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `price_agreement_id` bigint(20) unsigned NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(255) DEFAULT NULL,
  `type` enum('part','service') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agreement_items_price_agreement_id_foreign` (`price_agreement_id`),
  CONSTRAINT `agreement_items_price_agreement_id_foreign` FOREIGN KEY (`price_agreement_id`) REFERENCES `price_agreements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.calleds
DROP TABLE IF EXISTS `calleds`;
CREATE TABLE IF NOT EXISTS `calleds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `called_type_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `sector_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `patrimony_id` bigint(20) unsigned NOT NULL,
  `problem` text NOT NULL,
  `protocol` varchar(30) NOT NULL,
  `status` enum('A','F') NOT NULL DEFAULT 'A' COMMENT 'A = Aberto, F = Fechado',
  `type_maintenance` char(1) NOT NULL COMMENT 'P = Preventiva, C = Corretiva, R = Retorno',
  `closing_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `calleds_protocol_unique` (`protocol`),
  KEY `calleds_user_id_foreign` (`user_id`),
  KEY `calleds_sector_id_foreign` (`sector_id`),
  KEY `calleds_supplier_id_foreign` (`supplier_id`),
  KEY `calleds_patrimony_id_foreign` (`patrimony_id`),
  KEY `calleds_called_type_id_foreign` (`called_type_id`),
  CONSTRAINT `calleds_patrimony_id_foreign` FOREIGN KEY (`patrimony_id`) REFERENCES `patrimonies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calleds_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calleds_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calleds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3851 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.called_types
DROP TABLE IF EXISTS `called_types`;
CREATE TABLE IF NOT EXISTS `called_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.companies
DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `corporate_name` varchar(255) NOT NULL,
  `trade_name` varchar(255) NOT NULL,
  `state_registration` varchar(255) DEFAULT NULL,
  `cnpj` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_cnpj_unique` (`cnpj`),
  KEY `companies_address_id_foreign` (`address_id`),
  CONSTRAINT `companies_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.departaments
DROP TABLE IF EXISTS `departaments`;
CREATE TABLE IF NOT EXISTS `departaments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `cell_phone` varchar(255) DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_company_id_foreign` (`company_id`),
  KEY `departments_address_id_foreign` (`address_id`),
  CONSTRAINT `departments_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.departament_user
DROP TABLE IF EXISTS `departament_user`;
CREATE TABLE IF NOT EXISTS `departament_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `departament_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`departament_id`),
  KEY `departament_user_departament_id_foreign` (`departament_id`),
  CONSTRAINT `departament_user_departament_id_foreign` FOREIGN KEY (`departament_id`) REFERENCES `departaments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `departament_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.interactions
DROP TABLE IF EXISTS `interactions`;
CREATE TABLE IF NOT EXISTS `interactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `called_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `message` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interactions_called_id_foreign` (`called_id`),
  KEY `interactions_user_id_foreign` (`user_id`),
  CONSTRAINT `interactions_called_id_foreign` FOREIGN KEY (`called_id`) REFERENCES `calleds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.patrimonies
DROP TABLE IF EXISTS `patrimonies`;
CREATE TABLE IF NOT EXISTS `patrimonies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sector_id` bigint(20) unsigned DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `observation` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `purchase_value` double DEFAULT NULL,
  `write_off_reason` varchar(80) DEFAULT NULL,
  `write_off_date` datetime DEFAULT NULL,
  `has_report` tinyint(1) DEFAULT NULL,
  `report_date` datetime DEFAULT NULL,
  `type` varchar(80) DEFAULT NULL,
  `acquisition_type` varchar(80) DEFAULT NULL,
  `acquisition_value` double DEFAULT NULL,
  `acquisition_date` timestamp NULL DEFAULT NULL,
  `current_value` double DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patrimonies_sector_id_foreign` (`sector_id`),
  CONSTRAINT `patrimonies_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=286918 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.price_agreements
DROP TABLE IF EXISTS `price_agreements`;
CREATE TABLE IF NOT EXISTS `price_agreements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL,
  `year` year(4) NOT NULL,
  `signature_date` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `object` text NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `price_agreements_supplier_id_foreign` (`supplier_id`),
  KEY `price_agreements_company_id_foreign` (`company_id`),
  CONSTRAINT `price_agreements_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `price_agreements_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.sectors
DROP TABLE IF EXISTS `sectors`;
CREATE TABLE IF NOT EXISTS `sectors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `departament_id` bigint(20) unsigned NOT NULL,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `cell_phone` varchar(255) DEFAULT NULL,
  `responsible` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sectors_departament_id_foreign` (`departament_id`),
  KEY `sectors_address_id_foreign` (`address_id`),
  CONSTRAINT `sectors_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sectors_departament_id_foreign` FOREIGN KEY (`departament_id`) REFERENCES `departaments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.sector_user
DROP TABLE IF EXISTS `sector_user`;
CREATE TABLE IF NOT EXISTS `sector_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `sector_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`sector_id`),
  KEY `sector_user_sector_id_foreign` (`sector_id`),
  CONSTRAINT `sector_user_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sector_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.suppliers
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `corporate_name` varchar(255) NOT NULL,
  `trade_name` varchar(255) DEFAULT NULL,
  `cnpj` varchar(255) DEFAULT NULL,
  `state_registration` varchar(255) DEFAULT NULL,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  `cell_phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `company_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_cnpj_unique` (`cnpj`),
  KEY `suppliers_address_id_foreign` (`address_id`),
  KEY `suppliers_company_id_foreign` (`company_id`),
  CONSTRAINT `suppliers_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamadosv2.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  KEY `users_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
