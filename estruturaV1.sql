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

-- Copiando estrutura para tabela chamados.accounts_payables
CREATE TABLE IF NOT EXISTS `accounts_payables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `called_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `enforcer_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `platelet` varchar(50) NOT NULL,
  `patrimony_id` int(10) unsigned DEFAULT NULL,
  `nad` int(11) DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_payables_called_id_foreign` (`called_id`),
  KEY `accounts_payables_product_id_foreign` (`product_id`),
  KEY `accounts_payables_user_id_foreign` (`user_id`),
  KEY `accounts_payables_section_id_foreign` (`section_id`),
  KEY `accounts_payables_enforcer_id_foreign` (`enforcer_id`) USING BTREE,
  CONSTRAINT `accounts_payables_called_id_foreign` FOREIGN KEY (`called_id`) REFERENCES `calleds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accounts_payables_enforcer_id_foreign` FOREIGN KEY (`enforcer_id`) REFERENCES `enforcers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accounts_payables_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accounts_payables_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accounts_payables_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=269 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.anexos
CREATE TABLE IF NOT EXISTS `anexos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chamado_id` bigint(20) unsigned NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `local` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `anexos_chamado_id_foreign` (`chamado_id`),
  CONSTRAINT `anexos_chamado_id_foreign` FOREIGN KEY (`chamado_id`) REFERENCES `chamados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.atas
CREATE TABLE IF NOT EXISTS `atas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `data` datetime NOT NULL,
  `vencimento` datetime NOT NULL,
  `vlrGlobal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.audits
CREATE TABLE IF NOT EXISTS `audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` bigint(20) unsigned NOT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(1023) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audits_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audits_user_id_user_type_index` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4085 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.calleds
CREATE TABLE IF NOT EXISTS `calleds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `enforcer_id` bigint(20) unsigned NOT NULL,
  `patrimony_id` bigint(20) unsigned NOT NULL,
  `problem` longtext NOT NULL,
  `protocol` varchar(30) NOT NULL,
  `status` varchar(1) NOT NULL COMMENT 'Status o chamado',
  `type_maintenance` varchar(30) NOT NULL,
  `closing_date` date DEFAULT NULL,
  `patrimony` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calleds_user_id_foreign` (`user_id`),
  KEY `calleds_section_id_foreign` (`section_id`),
  KEY `calleds_enforcer_id_foreign` (`enforcer_id`),
  KEY `calleds_patrimony_id_foreign` (`patrimony_id`),
  CONSTRAINT `calleds_enforcer_id_foreign` FOREIGN KEY (`enforcer_id`) REFERENCES `enforcers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `calleds_patrimony_id_foreign` FOREIGN KEY (`patrimony_id`) REFERENCES `patrimonies` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `calleds_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `calleds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3649 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.chamados
CREATE TABLE IF NOT EXISTS `chamados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `setor_id` bigint(20) unsigned NOT NULL,
  `tipo_chamado_id` bigint(20) unsigned NOT NULL,
  `patrimonio_id` bigint(20) unsigned NOT NULL,
  `executor_id` bigint(20) unsigned NOT NULL,
  `problema` longtext NOT NULL,
  `protocolo` varchar(50) NOT NULL,
  `status` enum('A','P','F','EP') NOT NULL DEFAULT 'A',
  `dt_encerramento` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chamados_user_id_foreign` (`user_id`),
  KEY `chamados_setor_id_foreign` (`setor_id`),
  KEY `chamados_patrimonio_id_foreign` (`patrimonio_id`),
  KEY `chamados_executor_id_foreign` (`executor_id`),
  KEY `chamados_tipo_chamado_id_foreign` (`tipo_chamado_id`),
  CONSTRAINT `chamados_executor_id_foreign` FOREIGN KEY (`executor_id`) REFERENCES `executores` (`id`),
  CONSTRAINT `chamados_patrimonio_id_foreign` FOREIGN KEY (`patrimonio_id`) REFERENCES `patrimonios` (`id`),
  CONSTRAINT `chamados_setor_id_foreign` FOREIGN KEY (`setor_id`) REFERENCES `setores` (`id`),
  CONSTRAINT `chamados_tipo_chamado_id_foreign` FOREIGN KEY (`tipo_chamado_id`) REFERENCES `tipo_chamados` (`id`),
  CONSTRAINT `chamados_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1473 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.classes
CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `order_classe` int(11) NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `classes_course_id_foreign` (`course_id`),
  CONSTRAINT `classes_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.companies
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reason` varchar(80) NOT NULL,
  `fantasy` varchar(80) NOT NULL,
  `ie` varchar(20) DEFAULT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `andress` varchar(100) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `complement` varchar(20) DEFAULT NULL,
  `neighborhood` varchar(80) DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `cell_phone` varchar(16) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.courses
CREATE TABLE IF NOT EXISTS `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` double(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.departamentos
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `nome` varchar(150) NOT NULL COMMENT 'nome do departamento',
  `contato` varchar(80) NOT NULL COMMENT 'contato/secretário',
  `endereco` varchar(150) DEFAULT NULL,
  `numero` varchar(6) DEFAULT NULL,
  `cidade` varchar(150) DEFAULT NULL,
  `bairro` varchar(150) DEFAULT NULL,
  `compl` varchar(80) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `celular` varchar(16) DEFAULT NULL,
  `ramal` varchar(4) DEFAULT NULL COMMENT 'ramal interno telefônico',
  `email` varchar(255) DEFAULT NULL,
  `ativo` varchar(1) NOT NULL DEFAULT 'S',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departamentos_empresa_id_foreign` (`empresa_id`),
  CONSTRAINT `departamentos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.departamento_user
CREATE TABLE IF NOT EXISTS `departamento_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `departamento_id` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `departamento_user_user_id_departamento_id_unique` (`user_id`,`departamento_id`) USING BTREE,
  KEY `departamento_user_departamento_id_foreign` (`departamento_id`) USING BTREE,
  CONSTRAINT `departamento_user_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`),
  CONSTRAINT `departamento_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.departaments
CREATE TABLE IF NOT EXISTS `departaments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `contact` varchar(80) DEFAULT NULL,
  `andress` varchar(100) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `complement` varchar(20) DEFAULT NULL,
  `neighborhood` varchar(80) DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `cell_phone` varchar(16) DEFAULT NULL,
  `extension` varchar(6) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departaments_company_id_foreign` (`company_id`),
  CONSTRAINT `departaments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.departament_section
CREATE TABLE IF NOT EXISTS `departament_section` (
  `departament_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `departament_section_departament_id_foreign` (`departament_id`),
  KEY `departament_section_section_id_foreign` (`section_id`),
  CONSTRAINT `departament_section_departament_id_foreign` FOREIGN KEY (`departament_id`) REFERENCES `departaments` (`id`),
  CONSTRAINT `departament_section_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.empresas
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `razao` varchar(150) NOT NULL,
  `fantasia` varchar(150) DEFAULT NULL,
  `ie` varchar(15) DEFAULT NULL,
  `cnpj` varchar(26) DEFAULT NULL,
  `endereco` varchar(150) DEFAULT NULL,
  `numero` varchar(6) DEFAULT NULL,
  `compl` varchar(80) DEFAULT NULL,
  `bairro` varchar(150) DEFAULT NULL,
  `cidade` varchar(150) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `celular` varchar(16) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ativo` varchar(1) NOT NULL DEFAULT 'S',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.enforcers
CREATE TABLE IF NOT EXISTS `enforcers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reason` varchar(80) NOT NULL,
  `fantasy` varchar(80) NOT NULL,
  `cnpj` varchar(26) DEFAULT NULL,
  `ie` varchar(15) DEFAULT NULL,
  `andress` varchar(150) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `complement` varchar(20) DEFAULT NULL,
  `neighborhood` varchar(150) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `cell_phone` varchar(16) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.executores
CREATE TABLE IF NOT EXISTS `executores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ata_id` bigint(20) unsigned DEFAULT NULL,
  `razao` varchar(150) NOT NULL,
  `fantasia` varchar(150) DEFAULT NULL,
  `ie` varchar(15) DEFAULT NULL,
  `cnpj` varchar(26) DEFAULT NULL,
  `endereco` varchar(150) DEFAULT NULL,
  `numero` varchar(6) DEFAULT NULL,
  `compl` varchar(80) DEFAULT NULL,
  `bairro` varchar(150) DEFAULT NULL,
  `cidade` varchar(150) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `celular` varchar(16) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ativo` varchar(1) NOT NULL DEFAULT 'S',
  `servico` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `executores_ata_id_foreign` (`ata_id`),
  CONSTRAINT `executores_ata_id_foreign` FOREIGN KEY (`ata_id`) REFERENCES `atas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.failed_jobs
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

-- Copiando estrutura para tabela chamados.financeiros
CREATE TABLE IF NOT EXISTS `financeiros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chamado_id` bigint(20) unsigned NOT NULL,
  `chamado_protocolo` varchar(100) NOT NULL,
  `patrimonio_id` bigint(20) unsigned NOT NULL,
  `ata_id` bigint(20) unsigned NOT NULL,
  `executor_id` bigint(20) unsigned NOT NULL,
  `qtd` double NOT NULL,
  `valor` decimal(15,2) NOT NULL,
  `referencia` varchar(255) NOT NULL,
  `pago` varchar(1) NOT NULL DEFAULT 'N',
  `dt_pgto` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financeiros_chamado_id_foreign` (`chamado_id`),
  KEY `financeiros_patrimonio_id_foreign` (`patrimonio_id`),
  KEY `financeiros_ata_id_foreign` (`ata_id`),
  KEY `financeiros_executor_id_foreign` (`executor_id`),
  CONSTRAINT `financeiros_ata_id_foreign` FOREIGN KEY (`ata_id`) REFERENCES `itensata` (`id`),
  CONSTRAINT `financeiros_chamado_id_foreign` FOREIGN KEY (`chamado_id`) REFERENCES `chamados` (`id`),
  CONSTRAINT `financeiros_executor_id_foreign` FOREIGN KEY (`executor_id`) REFERENCES `executores` (`id`),
  CONSTRAINT `financeiros_patrimonio_id_foreign` FOREIGN KEY (`patrimonio_id`) REFERENCES `patrimonios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.interacoes
CREATE TABLE IF NOT EXISTS `interacoes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chamado_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `post` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `interacoes_chamado_id_foreign` (`chamado_id`) USING BTREE,
  KEY `interacoes_user_id_foreign` (`user_id`) USING BTREE,
  CONSTRAINT `interacoes_chamado_id_foreign` FOREIGN KEY (`chamado_id`) REFERENCES `chamados` (`id`),
  CONSTRAINT `interacoes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.interactions
CREATE TABLE IF NOT EXISTS `interactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `called_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `patrimony_id` bigint(20) unsigned NOT NULL,
  `post` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `interactions_called_id_foreign` (`called_id`) USING BTREE,
  KEY `interactions_user_id_foreign` (`user_id`) USING BTREE,
  KEY `interactions_patrimony_id_foreign` (`patrimony_id`),
  CONSTRAINT `interactions_called_id_foreign` FOREIGN KEY (`called_id`) REFERENCES `calleds` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `interactions_patrimony_id_foreign` FOREIGN KEY (`patrimony_id`) REFERENCES `patrimonies` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `interactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=299 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.itensata
CREATE TABLE IF NOT EXISTS `itensata` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ata_id` bigint(20) unsigned NOT NULL,
  `numero_ata` int(11) DEFAULT NULL,
  `codigo_gextec` varchar(15) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `qtd_original` int(11) NOT NULL,
  `qtd_saldo` int(11) NOT NULL DEFAULT 0,
  `valor` decimal(15,2) NOT NULL,
  `dt_ultima_baixa` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `itensata_ata_id_foreign` (`ata_id`),
  CONSTRAINT `itensata_ata_id_foreign` FOREIGN KEY (`ata_id`) REFERENCES `atas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.jobs
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

-- Copiando estrutura para tabela chamados.job_batches
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

-- Copiando estrutura para tabela chamados.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.minutes
CREATE TABLE IF NOT EXISTS `minutes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enforcer_id` bigint(20) unsigned DEFAULT NULL,
  `object` varchar(255) DEFAULT NULL,
  `number` int(10) unsigned NOT NULL,
  `year` int(10) unsigned DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `signature_date` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `minutes_enforcer_id_foreign` (`enforcer_id`),
  CONSTRAINT `minutes_enforcer_id_foreign` FOREIGN KEY (`enforcer_id`) REFERENCES `enforcers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `team_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`team_id`,`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_permissions_permission_id_foreign` (`permission_id`),
  KEY `model_has_permissions_team_foreign_key_index` (`team_id`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.motivo_baixa
CREATE TABLE IF NOT EXISTS `motivo_baixa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `motivo` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.ocorrencias
CREATE TABLE IF NOT EXISTS `ocorrencias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chamado_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `patrimonio_id` bigint(20) unsigned NOT NULL,
  `mensagem` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ocorrencias_chamado_id_foreign` (`chamado_id`),
  KEY `ocorrencias_user_id_foreign` (`user_id`),
  KEY `ocorrencias_patrimonio_id_foreign` (`patrimonio_id`),
  CONSTRAINT `ocorrencias_chamado_id_foreign` FOREIGN KEY (`chamado_id`) REFERENCES `chamados` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `ocorrencias_patrimonio_id_foreign` FOREIGN KEY (`patrimonio_id`) REFERENCES `patrimonios` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `ocorrencias_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.patrimonies
CREATE TABLE IF NOT EXISTS `patrimonies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` bigint(20) unsigned NOT NULL,
  `platelet` varchar(255) NOT NULL COMMENT 'Plaqueta de patrimônio',
  `description` varchar(255) DEFAULT NULL COMMENT 'Descrião do esquipamento comprado',
  `observation` varchar(255) DEFAULT NULL COMMENT 'Observação do item',
  `image` varchar(255) DEFAULT NULL COMMENT 'Imagem do bem/imóvel',
  `buy_date` datetime DEFAULT NULL COMMENT 'Data da compra do bem/imóvel',
  `buy_value` float DEFAULT NULL COMMENT 'Valor da compra do bem/imóvel',
  `motive_low` varchar(80) DEFAULT NULL COMMENT 'Motivo da baixa',
  `low_date` datetime DEFAULT NULL COMMENT 'Data da baixa',
  `report` tinyint(1) DEFAULT NULL COMMENT 'Se foi emitido um laudo para a baixa',
  `report_date` datetime DEFAULT NULL COMMENT 'Data do laude de baixa',
  `patrimony_type` varchar(80) DEFAULT NULL COMMENT 'Tipo de baixa',
  `acquisition_type` varchar(80) DEFAULT NULL COMMENT 'Tipo de aquisição do bem/imóvel',
  `acquisition_value` float DEFAULT NULL COMMENT 'Valor de aquisição do bem/imóvel',
  `acquisition_date` timestamp NULL DEFAULT NULL,
  `current_value` float DEFAULT NULL COMMENT 'Valor atual do bem',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patrimonies_section_id_foreign` (`section_id`),
  CONSTRAINT `patrimonies_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=286909 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.patrimonios
CREATE TABLE IF NOT EXISTS `patrimonios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setor_id` bigint(20) unsigned NOT NULL,
  `plaqueta` varchar(10) NOT NULL,
  `descricao` text NOT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `data_compra` date DEFAULT NULL,
  `detalhe_problema` varchar(255) DEFAULT NULL,
  `vlr_compra` decimal(15,2) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `motivo_baixa` varchar(50) DEFAULT NULL,
  `data_baixa` date DEFAULT NULL,
  `laudo` tinyint(1) NOT NULL DEFAULT 0,
  `data_laudo` date DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `tipo_aquisicao` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data_aquisicao` date DEFAULT NULL,
  `vlr_aquisicao` decimal(15,2) DEFAULT NULL,
  `vlr_atual` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `patrimonios_setor_id_foreign` (`setor_id`) USING BTREE,
  CONSTRAINT `patrimonios_setor_id_foreign` FOREIGN KEY (`setor_id`) REFERENCES `setores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=285995 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.personal_access_tokens
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `minute_id` bigint(20) unsigned NOT NULL,
  `code` bigint(20) unsigned NOT NULL COMMENT 'Código GEXTEC',
  `description` varchar(255) NOT NULL COMMENT 'Descrição do produto ou serviço',
  `quantity` int(10) unsigned NOT NULL COMMENT 'quantidade de produtos da ata',
  `price` float NOT NULL COMMENT 'valor do produto',
  `measurement_unit` varchar(255) NOT NULL COMMENT 'Unidae de medida',
  `type` varchar(255) NOT NULL COMMENT 'Tipo de produto da ATA se é serviço ou peça',
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Se é ativo ou não, caso o produto tenha passado por desistência na ATA, será desativado pra não usar mais',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_minute_id_foreign` (`minute_id`),
  CONSTRAINT `products_minute_id_foreign` FOREIGN KEY (`minute_id`) REFERENCES `minutes` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=404 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `roles_name_guard_name_unique` (`guard_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.sections
CREATE TABLE IF NOT EXISTS `sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `departament_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `andress` varchar(150) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `complement` varchar(20) DEFAULT NULL,
  `neighborhood` varchar(150) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `cell_phone` varchar(16) DEFAULT NULL,
  `extension` varchar(6) DEFAULT NULL,
  `responsible` varchar(6) DEFAULT NULL,
  `type_property` varchar(6) DEFAULT NULL,
  `uc_energy` varchar(6) DEFAULT NULL,
  `uc_water` varchar(6) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sections_departament_id_foreign` (`departament_id`),
  CONSTRAINT `sections_departament_id_foreign` FOREIGN KEY (`departament_id`) REFERENCES `departaments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.section_user
CREATE TABLE IF NOT EXISTS `section_user` (
  `id_pivo` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pivo`),
  KEY `section_user_user_id_section_id` (`user_id`,`section_id`) USING BTREE,
  KEY `section_user_section_id_foreign` (`section_id`),
  CONSTRAINT `section_user_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `section_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1408 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.sessions
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

-- Copiando estrutura para tabela chamados.setores
CREATE TABLE IF NOT EXISTS `setores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `departamento_id` bigint(20) unsigned NOT NULL,
  `executor_id` bigint(20) unsigned DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `endereco` varchar(150) DEFAULT NULL,
  `bairro` varchar(150) DEFAULT NULL,
  `numero` varchar(6) DEFAULT NULL,
  `compl` varchar(80) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `cidade` varchar(150) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `ramal` varchar(6) DEFAULT NULL,
  `celular` varchar(16) DEFAULT NULL,
  `responsavel` varchar(80) DEFAULT NULL,
  `tipo_imovel` varchar(30) DEFAULT NULL,
  `uc_energisa` varchar(20) DEFAULT NULL,
  `uc_saae` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `ativo` varchar(1) NOT NULL DEFAULT 'S',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `setores_departamento_id_foreign` (`departamento_id`),
  KEY `users_executor_id_foreign` (`executor_id`),
  CONSTRAINT `setores_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `users_executor_id_foreign` FOREIGN KEY (`executor_id`) REFERENCES `executores` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.setor_user
CREATE TABLE IF NOT EXISTS `setor_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `setor_id` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `user_setor_user_id_setor_id_unique` (`user_id`,`setor_id`),
  KEY `user_setor_setor_id_foreign` (`setor_id`),
  CONSTRAINT `setor_user_setor_id_foreign` FOREIGN KEY (`setor_id`) REFERENCES `setores` (`id`),
  CONSTRAINT `setor_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.tipo_chamados
CREATE TABLE IF NOT EXISTS `tipo_chamados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `descricao` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela chamados.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enforcer_id` bigint(20) unsigned DEFAULT NULL COMMENT 'FK do executor caso ele seja executor.',
  `avatar` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `type_user` varchar(30) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
