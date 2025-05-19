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

-- Copiando estrutura para tabela chamadosv2.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.model_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `model_has_permissions`;

-- Copiando estrutura para tabela chamadosv2.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.model_has_roles: ~1 rows (aproximadamente)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(8, 'App\\Models\\User', 1),
	(10, 'App\\Models\\User', 6),
	(10, 'App\\Models\\User', 13);

-- Copiando estrutura para tabela chamadosv2.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
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
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.role_has_permissions: ~86 rows (aproximadamente)
DELETE FROM `role_has_permissions`;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(25, 8),
	(25, 10),
	(26, 8),
	(26, 10),
	(27, 8),
	(27, 10),
	(28, 8),
	(28, 10),
	(29, 8),
	(29, 10),
	(30, 8),
	(31, 8),
	(32, 8),
	(33, 8),
	(33, 9),
	(33, 10),
	(34, 8),
	(34, 9),
	(34, 10),
	(35, 8),
	(35, 9),
	(35, 10),
	(36, 8),
	(36, 9),
	(36, 10),
	(37, 8),
	(38, 8),
	(39, 8),
	(40, 8),
	(41, 8),
	(42, 8),
	(43, 8),
	(44, 8),
	(45, 8),
	(46, 8),
	(47, 8),
	(48, 8),
	(49, 8),
	(49, 10),
	(50, 8),
	(50, 10),
	(51, 8),
	(51, 10),
	(52, 8),
	(52, 10),
	(53, 8),
	(53, 10),
	(54, 8),
	(54, 10),
	(55, 8),
	(55, 10),
	(56, 8),
	(56, 10),
	(57, 8),
	(57, 10),
	(58, 8),
	(58, 10),
	(59, 8),
	(59, 10),
	(60, 8),
	(60, 10),
	(61, 8),
	(61, 10),
	(62, 8),
	(62, 10),
	(63, 8),
	(63, 10),
	(64, 8),
	(64, 10),
	(65, 10),
	(69, 9),
	(69, 10),
	(70, 9),
	(70, 10),
	(71, 9),
	(71, 10),
	(72, 9),
	(72, 10),
	(73, 9),
	(73, 10),
	(74, 9),
	(74, 10),
	(75, 9),
	(75, 10),
	(76, 9),
	(76, 10);

-- Copiando estrutura para tabela chamadosv2.users
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela chamadosv2.users: ~134 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `company_id`) VALUES
	(1, 'JEOVÁ OLIVEIRA DOS SANTOS', 'jeovacnp2012@gmail.com', NULL, '$2y$12$pzune4Xy33GU1HA7Nhd4L.gtrdo.VBt4d2YmBOJ7WxKArGjzrKLai', NULL, '2025-05-13 05:13:51', '2025-05-19 21:49:16', NULL),
	(3, 'SOLICITANTE ADMINISTRAÇÃO', 'solicitanteadministracao@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-04-04 13:54:34', '2025-01-27 01:24:37', 1),
	(4, 'GABRIELLE NATHALLIE CARDOSO BATISTA', 'gabrielle.cardoso1646@gmail.com', NULL, '$2y$12$d0butbxKgLBDdXNl7joFk.Rs14cNnp87WrmjH2Po3C2dwpyOQtNsm', 'EWPke10FtgU7QH5nfpBkm48VYjh2oDPiKEIgpze6ReocFh4OGbWQuJmFzEff', NULL, '2025-04-14 16:21:09', 1),
	(5, 'EAP-RANCHÃO', 'inakharynmanrique@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(6, 'JOSIAS NASCIMENTO DOS SANTOS', 'josiasjhoynasc0409@gmail.com', NULL, '$2y$12$x2piR8/HYrSmPOS/PoC87.V2RzESGVxed9YBvLVedOxOdYslqCIT6', 'k072VWfHsdDLoWjr7xbEpxNhCmyz0E6bAZmOlOSM1EdjgFjiZG2mBGi1uZhT', NULL, '2025-05-19 21:31:26', 1),
	(8, 'ESF ALTO DA COLINA', 'psfcolina@novamutum.mt.gov.br', NULL, '$2y$12$.NB8PqUsqxTh9Kh8cOXWMedHUZ.sDAa2p3z//.XSbgWsJV5.XBNVK', 'WhE6TcPGjGSrD7GgjLJm1dvV2ksk6khPoTn3MyABxJL8cZgSum6OHXMhww9D', NULL, '2025-02-17 19:49:17', 1),
	(9, 'ESF CIDADE NOVA', 'esfcidadenova@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2024-10-25 19:44:07', 1),
	(11, 'DANIEL TRENTIN', 'novaclima@live.com', NULL, '$2y$12$seybD9H/Bwo86ZEa.aNS/e8Df./1XibFebOlsKiPRX7PdyXezXdvK', 'Dqr41qv2jEdk67INJddcFgTRpJB5Hjl0txuXSwC7GOv3DuHaWUPIxHnMPw64', '2024-07-30 04:03:59', '2025-02-03 21:20:49', 1),
	(12, 'ZARDO MANUTENÇÃO E ASSISTENCIA TECNICA ODONTO HOSPITALAR LTDA', 'jaisonzardo@hotmail.com', NULL, '$2y$12$m.qI4x58B4rn/eaMkUqqoOEjdxlZRBCKiEIa/S2wL9/PJwwwFDRmq', '6jNTE7ORTEIRptevKMVX9vOqNA7W0nH4EbPXnY4wXHPTTMolHYGBjzZ7sF4d', NULL, '2025-02-03 22:32:44', 1),
	(13, 'SANDRA KONZEN', 'sandrinhakonzen@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', 'XwBERyMyKDLiwvroPvDYJPRxP3A5n52ddVTk4UEeDv2j0CaTQ2qIatqwu12s', NULL, '2025-05-19 21:29:05', 1),
	(14, 'PSF PARQUE DO SOL', 'lucineya_mattos@hotmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(15, 'CAF', 'caf@novamutum.mt.gov.br', NULL, '$2y$12$3BMtOroYsAzl6EMBvVdztulbmZVneIP0k8rcJBtKDuxSouqY.BReO', '95vjoEU9gFhNKDBiEA7xBb6r8KJ9pwWP8V2iLexRJJ8Nxb5psDV83N0E3wzj', NULL, '2025-02-07 11:28:43', 1),
	(16, 'PARQUE DO SOL', 'psfparquedosol@novamutum.mt.gov.br', NULL, '$2y$12$YfwNvd1LU2bHdw9lZHY60.6piUtZA7M93RByIC49lpxWDxm1GKjq.', 'vmSnuxwJUBtJ7tCTyIYivYiHTta4BonheGZlO3nX73sjoyawVXzLBYakOn2D', NULL, '2025-01-27 13:51:01', 1),
	(18, 'MARILZA MARQUES FIGUEIREDO TAVARES', 'marilzafigueiredo7@gmail.com', NULL, '$2y$12$WAeNg0qBYnOrCWVc2aXcIOgQRTVXIi8IQa3z7ekSziY5ySNEF/5Le', 'Jg1WnWlPg7eYuxwXroYU505JF5ggmr9Voz3tyRVGcJzKX26awvNJr9i3KpPL', NULL, '2025-02-03 12:20:34', 1),
	(19, 'PSF JARDIMII', 'pamquinteiro@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2025-04-16 21:58:18', 1),
	(20, 'JOSIANE DOS SANTOS', 'mikuskajosi88@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(21, 'PSF JARDIM PRIMAVERA', 'psfjardimprimavera@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(22, 'VICTOR MATTHEUS NEVES DA SILVA', 'vmneves97@gmail.com', NULL, '$2y$12$lRZ9MC8HLmO/eKUBaeLN6OfUuLufwSaVACkYdYY3HPZwRvAE52I.K', 'MawtIru3HYft56hFv5QziA6khqKpKmXzALvscbb59HfqUw2qbxGtlnwtp9r3', NULL, '2025-01-31 11:59:55', 1),
	(23, 'CAPS - CENTRO DE ATENDIMENTO PISICO SOCIAL', 'caps@novamutum.mt.gov.br', NULL, '$2y$12$t0WbSxdHJHWhX31i4mvivOx6rIDKrNBKY/VIGC9PAIMLRLhVveFCO', 'Tq81d9OwixpCLVoqDsfu5K8ncpB3HO0BE8VcXsxn2xxZHwMkJOpTcI8ueooJ', NULL, '2025-02-12 19:52:35', 1),
	(24, 'FLAVIANA DAS DORES BARROS', 'flaviana_filhos@hotmail.com', NULL, '$2y$12$184SGl9vi8yhWcOYYtb89OdtASFUPWST76TfvME4NtiwP2XKHYtpW', NULL, NULL, '2025-01-30 13:41:43', 1),
	(25, 'CENTRO DE SAÚDE RURAL', 'centrodesaude@novamutum.mt.gov.br', NULL, '$2y$12$to1XVRqfaKD6KQqsZJybceKBTwtbTXSRzm2/0bsGwFzBUM10hoae2', 'wWk46PKcKTPaIHrfBoJk9Eq8XvvpBFZjboi0UvjOd93Efjo0tJQypdPPl2kF', NULL, '2025-03-19 11:17:39', 1),
	(26, 'WALESKA', 'walescaleka-23@hotmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(27, 'ROSANGELA', 'patrimonio.saude@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2024-10-17 15:06:31', 1),
	(28, 'EAP PONTAL DO MARAPE', 'psfpontaldomarape@novamutum.mt.gov.br', NULL, '$2y$12$YTVVZcthNcBRQ0OxexONbeEsZ0vtqLi0LNiFP/v/63scS/xv6gkSW', 'ntVRpjL4DwZMtPDsUhIrfLl7qihFhXohnQXBQhLU5DEI8jH5CmSE3Fbv00yW', NULL, '2025-02-25 20:37:13', 1),
	(29, 'CRISTIANE ACARI', 'ascari.cris@hotmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2024-08-12 21:27:48', 1),
	(30, 'LUCIMARA PONTAL', 'isottonlucimara@gmail.com', NULL, '$2y$12$DjXq85MEAY/gBTOwpUkbPeCBg8Qm.rV0td6.H5IB8wquFcIir0Mb.', NULL, NULL, '2025-02-25 20:51:01', 1),
	(31, 'PSF FLOR DO CERRADO', 'psfflordocerrado@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(32, 'CENTRO DE EQUOTERAPIA E ESCOLA DE EQUITAÇÃO ELO', 'equoterapia@novamutum.mt.gov.br', NULL, '$2y$12$jxQ3pAaaQaL5TRLnFkUqzO1Vi2xvMkwn2kbODRvIXg6xiWJAfUvXa', '9mMf65dpAuchVD02o7TdlCfmfbOcoaIRT51ddkGIXi5lSzkap5waDvWHo7xO', NULL, '2025-05-08 16:25:14', 1),
	(33, 'ANA CAVALHEIRO', 'anacavalheiro_89@hotmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2024-07-23 06:50:33', 1),
	(34, 'GILÇARA FERREIRA RAMOS', 'gilsaraframos@gmail.com', NULL, '$2y$12$51EQ2ByCptN61gdW2x9EI.nG069V03ivOw76lIv1NbUSRR/S1o7Ne', NULL, NULL, '2025-04-01 12:22:24', 1),
	(35, 'FERNANDA', 'banco.sangue@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2024-07-23 07:02:46', 1),
	(36, 'SANDRA BENEDITA NEVES', 'sandrabeneves@gmail.com', NULL, '$2y$12$XtGs8ix3bOlZmc2cGlZ8ueEGovi8HKe9K66qD.HYGGTcvShirjbBy', NULL, NULL, '2025-02-17 19:55:00', 1),
	(37, 'ARIELE', 'psfararas@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2024-07-23 22:18:00', 1),
	(38, 'ISABELA ARRUDA DA LUZ', 'isabela.cnp@gmail.com', NULL, '$2y$12$7axXCWYbWSz2bVDZ76eRkuHPYJjgSXq8ZGs91RvypJD.iNsJaneiq', 'VSdMtl8yfF6GDqfVZQnLsCSQ9vSJpefjaUZa7HQ1IeGhOkskosTmg1EK4xUG', NULL, '2025-04-16 17:59:05', 1),
	(39, 'EDNA OLIVEIRA', 'ednaoliveirapenso@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(40, 'ANA FLAVIA MARIOTTI', 'psfranchao@novamutum.mt.gov.br', NULL, '$2y$12$BhUJWeD1ZwsQ7SvYm4/agux5Cc0nwkibCR3/ElsV.7tDEu7.ji0lm', 'sbTm7U902ClLUyg3BkLOlakk2oGxhrJz5NoRwQR2IVn9V4903ubDKkrASh2S', NULL, '2025-04-01 13:34:01', 1),
	(41, 'JULIANA EPIDEMIOLÓGICA', 'saude.epidemiologica@novamutum.mt.gov.br', NULL, '$2y$12$v8RaIm.OxfbEh0WM3PzZn.K/8VTzXcShg6Ewo0wsQcqW/c4Ym7D66', NULL, NULL, '2025-02-13 17:25:06', 1),
	(42, 'FARMÁCIA CIDADÃ', 'farmacia@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(43, 'NAYANE ANDERSSA', 'nayaneandressa@outlook.com', NULL, '$2y$12$deuOcYkUHAMuYwdmpQLSFOSMePXg57cZEejoYy.V/vqrllBt3YOvS', NULL, NULL, '2025-01-30 13:33:49', 1),
	(44, 'HOSANA GOMES BARROS', 'hosana_gome16@hotmail.com', NULL, '$2y$12$YaPSqFr0i2MEpx245nsF6OeaNAok40xGOUfLVYwBSzIDJ/fLT8zNS', 'D5WdI9GkB2K1qrWd3tLkVq6LoqCP1bYe9xZpWYCd9nJPzBt4gTcd9nnww5kK', NULL, '2025-01-30 13:33:21', 1),
	(46, 'ZELI DE LARA', 'zk_bibliotec@hotmail.com', NULL, '$2y$12$.k95Zmbg81xNoFbx.AuFEOZRxrylvfvLfbhpCx4VIrQh7CPwqlUQ.', 'iNZQqN00Wf9iQmj4v9LPUth9spwNnivm0DaVVetldF4QPOEB7pVswQATPqbh', NULL, '2025-04-15 00:16:11', 1),
	(47, 'ALINI DA FONSECA REIS', 'aliniffarmaceutica@gmail.com', NULL, '$2y$12$05UxCmWymgKetjW0/oMxW.T/a5FenvH1E1GYOoJZ.WX6A2.ojkLlS', NULL, NULL, '2025-05-06 15:54:35', 1),
	(49, 'ROSECLAIR KURZ', 'rkurz2019@gmail.com', NULL, '$2y$12$tGp8jVsWZW1I7K/147zW7e7CktOoQriheYpg9nWZ9xYo2oghsFrO.', NULL, NULL, '2025-02-18 11:37:02', 1),
	(50, 'JEOVA SANTOS', 'alegriaemteradeusnafamilia@msn.com', NULL, '$2y$12$HJWa9CLlfpiZmgp1YXYxGO0Pmz219Iz3OataMa1Qfg8Uo370sPwPq', NULL, NULL, '2025-04-10 20:09:34', 1),
	(51, 'MICHELI SIMONATTO CASTRO', 'simonattomicheli@gmail.com', NULL, '$2y$12$taLzlzPdX0zrk.Sn1LmTvO3fwZd0MSxurGqaMEmlp7dLFx.JJjuHe', 'Lg2FVb2c3N0dcAtmIXbd7wFpm8epLqTIIhbirgIRENY1QkQYLkF7Yi4y6EDO', NULL, '2025-03-20 19:10:52', 1),
	(53, 'BRUNA DALDATO', 'bruna-dadalto@hotmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2024-07-23 06:40:56', 1),
	(54, 'RAYLA RODRIGUES SALVALAGGIO', 'raylaenf@outlook.com', NULL, '$2y$12$WAoDW57Cu43pHreDuWonJ.FOjhPtoAGgcBmPTv.gGeJkwho4SDaqa', NULL, NULL, '2025-02-03 12:53:43', 1),
	(55, 'EDENILZA NETA DA SILVA', 'silva.ednilza@hotmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(56, 'JESSICA CAROLINA MARCHIORO VICENTE', 'compras.saude@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(57, 'JACKELINE DE ALMEIDA MATTOS', 'mattos.jackeline@yahoo.com.br', NULL, '$2y$12$Jdzti9vMNd78L6mww0wa5u8vRe3v7OcMf2i2kicHC.uN55lEG2.6.', '49z2QAfryyx9WDKf9RZzmTE6syOmwnCCfYcknHGHAYcGSW7rygxjxbbcMYup', NULL, '2025-04-15 22:19:12', 1),
	(58, 'ELIZANDRA FARIA', 'elizandra_costa@yahoo.com.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, NULL, 1),
	(59, 'ESF SERINGUEIRAS', 'psfseringueiras@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, NULL, '2024-07-23 06:46:29', 1),
	(63, 'JUCIANE DE LIMA KANIESKI', 'jucianepl@hotmail.com', NULL, '$2y$12$xDEkkQTLaMJYrlz0q.546.1F/6KtUBLW3HhJ965Y.t8nz5AvLFo4G', NULL, '2024-07-19 05:47:02', '2025-01-27 14:41:48', 1),
	(64, 'ALINE SANTOS DE SOUZA', 'alinesantosdesouza2003@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-07-23 06:03:07', '2024-07-23 06:03:07', 1),
	(65, 'CRISTINA APARECIDA LIMA FORMIGA GONCALVES', 'crisformigatrabalho@hotmail.com', NULL, '$2y$12$E.STsCVNULoFsUG01pmzn.W9CEROK7Cnbb7xB6oN4HQNKCN.x.cUm', '7x8KThULquMHdqshQGR8xozzvInDnd9Lfj4fG5TGQtEFV7CHC7cEz39v1jsr', '2024-07-23 06:05:21', '2025-01-28 18:55:40', 1),
	(66, 'JACKELINE SANTOS STEVENS', 'jackelinestevens384@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-07-23 06:08:51', '2024-07-23 06:52:35', 1),
	(67, 'JUSSARA FERREIRA DE SANTANA', 'jferreira036@gmail.com', NULL, '$2y$12$smrN4qIquK5v4yCs7eAY5e.eTvWXxMlD74Ta9Z9kq89mHrWuFcvce', NULL, '2024-07-23 06:15:52', '2025-05-08 15:58:41', 1),
	(68, 'OTAVIO AUGUSTO BATISTA LOPES', 'gutoqbencao@hotmail.com', NULL, '$2y$12$pIG/knIvNPKrGHu0Hdmbfe4FOAutNGanJVle1MwMD8KJ7RXLhVWG.', 'PNZVeV8C4QH7JrolTIvVnj1F44HKWQDA7TXGKxLLUEwpNBBYpa34lec1bLbJ', '2024-07-23 06:17:28', '2025-01-27 11:06:39', 1),
	(79, 'ELIZABETH PSF JARDIM II', 'dearaujoelizabeth9@gmail.com', NULL, '$2y$12$.1ghYkKZBRe4dulpuCjawOv.8fsFl.WyIp6KJfbOU.DOqZkmn/chS', 'FhdDdp1ryKTnBdM6sstWGtZOh0nSdSEo8AiodzaZPgXBuC2s6CZja52VnQ7i', '2024-07-29 18:11:08', '2025-04-15 18:25:21', 1),
	(80, 'EDILAINE - PSF JARDIM II', 'edilainecorrea0709@gmail.com', NULL, '$2y$12$MRr3hNdzHRRmMGu.TgPlpO8qilFJkCWLAAtHb/447KkqOCgpncrAq', NULL, '2024-07-29 18:34:17', '2025-01-27 12:07:12', 1),
	(85, 'VIDRAÇARIA - MSP EDIFICAÇÕES E SERVIÇOS LTDA', 'vidros@medeirosengenharia.com.br', NULL, '$2y$12$WqWZhz2Q9pjbEcjph.AijeY/wOQwQHhK3NQA1pRA2RjuqbhScc.Ay', NULL, '2024-07-31 22:28:25', '2025-02-07 14:10:49', 1),
	(86, 'ANTONIO LUIZ DE LIMA', 'euanfaz@outlook.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-02 21:11:48', '2024-08-02 21:11:48', 1),
	(87, 'EMANUEL ARAUJO DA LUZ', 'emanuel.cnp@gmail.com', NULL, '$2y$12$N.2iYVhG.PiQTMLpPWO.QOTNOCO4szMLiI2cgsNguhxQlr3XaGw7a', NULL, '2024-08-05 16:56:37', '2025-01-31 11:40:40', 1),
	(88, 'ROSIANE HORMINDA BARRETO SILVA FORMIGHIERI', 'farmaciacolina@novamutum.mt.gov.br', NULL, '$2y$12$rvibkpR3ieVGCCmpJLKnBeimTC6lO6FsyMxhrA.qdYGfXC4DA.eCK', 'Nlu9pgFw2PjjOPeAxwzVeWuXWzS5Wzjob1WTMg87R97JZW142QeXB0A1wD6i', '2024-08-06 00:59:19', '2025-01-28 18:35:13', 1),
	(89, 'LUCIANE MAYER', 'luciane.mayer@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-08 15:28:44', '2024-08-08 15:28:44', 1),
	(90, 'VISAO SAUDE', 'visaosaude@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-09 01:38:02', '2024-08-09 01:38:02', 1),
	(91, 'JOSIAS => ADM', 'visaoadm@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-09 01:39:17', '2025-01-29 01:41:11', 1),
	(92, 'LUCIANA ALBANO', 'centraldevagas@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-09 16:21:30', '2024-08-09 16:21:30', 1),
	(93, 'CAMILA PARIZZOTO', 'agendamentocentralnm@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-09 16:51:52', '2024-08-09 16:51:52', 1),
	(94, 'LABORATORIO PRONTO ATENDIMENTO', 'laboratoriomunicipal@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-09 18:50:27', '2024-08-09 18:50:27', 1),
	(95, 'ROSANGELA DALA RIVA', 'rosangeladalariva@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-13 17:27:33', '2024-10-21 17:10:33', 1),
	(96, 'IZABELA DE ALMEIDA LOPES', 'almeidaizabela897@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-14 22:08:22', '2024-08-15 21:17:30', 1),
	(97, 'IZABELA DE ALMEIDA LOPES', 'montesmari.ana333@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-15 16:35:15', '2024-08-15 16:35:15', 1),
	(99, 'MARA SANDRA FREITAS DA SILVA', 'marasandrafreitas@gmail.com', NULL, '$2y$12$LrDgREsU7I6lp0Df1K0t7uGV.6OP8adLP68iGAuZ39z6oHNiLcN.O', NULL, '2024-08-21 16:08:05', '2025-04-14 16:23:09', 1),
	(100, 'THALITA DE OLIVEIRA MACIEL', 'thalita11_oliveira@outlook.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-08-22 15:23:51', '2024-08-22 15:23:51', 1),
	(101, 'CRISLAYNE SORDE', 'visa@novamutum.mt.gov.br', NULL, '$2y$12$6sJjJ583HOzQUzeMyP2GvOCRDphW0Ua58tLgm7k6/dx7I.2sjq7BW', NULL, '2024-08-29 21:45:05', '2025-03-21 17:03:10', 1),
	(102, 'ANDERSON LINS FERREIRA FORMIGHIERI', 'anderson.sms@novamutum.mt.gov.br', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-09-02 16:02:40', '2024-09-02 16:02:40', 1),
	(103, 'MARCIA FREY', 'marciafrey123@gmail.com', NULL, '$2y$12$UUShWcV7irqYvARgvQssJ.PTj021hX4dN7gtpEKLTc5jmDnuJKPk2', 'F12J28RPVLxhFBMo0bJPSN0zfZt8ZD8lLZkbwAFrVdvFjRooOYNU36xSeLxD', '2024-09-02 22:39:09', '2025-04-09 11:23:21', 1),
	(104, 'WASHINGTON GODOY', 'washingtongodoy93@gmail.com', NULL, '$2y$12$XGPteA/pfMnd2qiDHYGupeNDOw1iq4lzblgB7hiGVqQ5NGer0U/Qi', 'VmHT3ilgBLW3Z5WXx1QtgsQnlnBlBs0V5FIAWkkGSQHT406Eh3v8UZ0HtstZ', '2024-09-27 16:37:42', '2025-05-16 00:27:20', 1),
	(105, 'MARÍLIA BEATRIZ SILVA DOS SANTOS', 'mabi250593@gmail.com', NULL, '$2y$12$Cjbw4f40..ynxeY8JaC.F.FsHG/DDNUWH9E2TagEy.BZwR2/tx0Wi', NULL, '2024-10-23 17:13:41', '2025-01-27 14:47:47', 1),
	(106, 'MARIA FERNANDA BONI', 'mfernandabonij@outlook.com', NULL, '$2y$12$XZZ2DoW4GgPMK/H3LvMUn.hfz1o509Y6Uy8nfiXQzT1HJ.xg36huW', 'aH6AH3WJYbFe2HIN77HZ1yvubhO4LpGn48qlXvNThyMoakteYI3jMu8XOfes', '2024-11-06 21:33:15', '2025-03-05 11:07:50', 1),
	(107, 'AMANDA CRISTINA DE OLIVEIRA', 'amandacrist2729@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-11-06 23:00:28', '2024-11-06 23:00:28', 1),
	(108, 'ELIANA ROSA DE OLIVEIRA', 'rosaeliana677@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2024-11-13 16:59:48', '2024-11-13 16:59:48', 1),
	(109, 'AMANDA GABRIELE DA SILVA', 'enfa.amandagabrielesilva@gmail.com', NULL, '$2y$12$yb03DhQ9YflOM/gx0PadtuStpm3Un52kdcTVdD9XSSqA8xKOvHcBW', NULL, '2024-12-04 23:22:42', '2025-04-16 15:58:48', 1),
	(110, 'ALLISON JUNIOR ALMEIDA NETTO', 'allisonjunior95@gmail.com', NULL, '$2y$12$gRgFNj/7Utpxlx11CjaD7ucVpSlEbLq6kP1LXD1xV2Z7mcLOWu6iu', '9jvAlBlXtTKSUnp4llHsHHfkDxUqrRdgr4iIWj581Hk9oLax01AS4e2uHWaM', '2024-12-04 23:56:50', '2025-02-18 11:36:39', 1),
	(111, 'DAYANI ALVES AMARO DUFFECK', 'Day08amaro@gmail.com', NULL, '$2y$12$b/moC9FXOBax8twoNtah6eNsKKsQkqXqgG6eBd2C5vNr4HsNM8Yrq', NULL, '2024-12-09 17:12:33', '2025-03-05 11:20:02', 1),
	(112, 'DIEGO HENRIQUE GONÇALVES DE SOUZA', 'diegohenriquegsouza@gmail.com', NULL, '$2y$12$WApdgcUgsibozWlyFGj5b.MjwfE4.HFqnh6y.3RN5qMOyT3owIhcq', NULL, '2025-01-07 22:03:12', '2025-01-27 18:10:22', 1),
	(113, 'ALISSON BOM DESPACHO DE MATOS', 'alissonbdmatos@hotmail.com', NULL, '$2y$12$O.fCkRFovMi.gFLtEgiGfuVr8NDjxPlP60JZAIZtbef7N8MmguVii', 'syY2VSans5Tmr3XDSTB0zRZa35Ceb9rToOKAtz7Q5tw9BHspkJrN6iKEQh20', '2025-01-07 22:58:24', '2025-02-04 11:49:32', 1),
	(114, 'CARLA DANILE DOS SANTOS BERTO', 'carladanielleberto@gmail.com', NULL, '$2y$12$s/FrwHVpDlMYvYEWdPBvFOBYjie/i95j47mX4wM/I7fs9plEZ7Z96', NULL, '2025-01-09 18:22:13', '2025-01-27 14:46:12', 1),
	(115, 'VALCIR RICARDO CELLONI', 'valcirvrc@gmail.com', NULL, '$2y$12$q4QooyBa4kt/y..vYG6MGuOTWVEuc/SFzE8/52ACpqNiYNxtLwh2u', NULL, '2025-01-10 16:10:21', '2025-01-10 16:10:21', 1),
	(116, 'WALESCA MARIA DA SILVA', 'walescamaria0@gmail.com', NULL, '$2y$12$TPvB48V/GDcFUyWrw4aXPuL1HZZgxyYZ9SDlb6svNRMOBtJ8o4xC2', NULL, '2025-01-22 22:02:12', '2025-05-14 18:30:30', 1),
	(117, 'SANDRA CAROLLO', 'vidros@medeirosengenhariamt.com.br', NULL, '$2y$12$V80jlrJ6hBVIWAfCid0Q1OeBG7F2U4FBGkg76ArODm9nn11eHq6Xm', 'MmPVXneFWjKpLYygWFoFtOG3nP6dOhysnyqXMd3aCMwP6MGQNn3OiqbpshV1', '2025-01-30 19:55:31', '2025-01-30 19:55:31', 1),
	(118, 'NUBYA DOS SANTOS SOUZA CAMPOS', 'campos.nubya@gmail.com', NULL, '$2y$12$5QRlARGcwPNBey22EYp6T.zbEfY54V1agVti5ZoLSOAwCtGnc1fQy', NULL, '2025-01-30 20:01:44', '2025-01-30 20:01:44', 1),
	(119, 'JANE AUDI RODELLI', 'nanimarilia@gmail.com', NULL, '$2y$12$0WLysBnv0gU6yUTrlsrNcugT/eebrdAdm9YBcMerv94EVItu9fpdK', NULL, '2025-01-31 12:00:42', '2025-01-31 12:00:42', 1),
	(120, 'JOSINEIA MINEIRO PORTELA', 'josineiaportela@gmail.com', NULL, '$2y$12$qVdPgX.fXbkoZzFSfYiwbekiYlFA0Ho5dxKDVOimLq27DE1NijbwG', NULL, '2025-01-31 12:01:16', '2025-01-31 12:01:16', 1),
	(121, 'ELAINE CRISTINA OENNING PINHO', 'elaine.oenning@hotmail.com', NULL, '$2y$12$RFz18.OtxozfMOcrgJipf.JPzlBkJZmJZWlZT/CQ7A7edInKgIuia', 'pLzbHo73cWHip84skFkn3OPtaDRzJfvqQDLvUxHPHbgpL1f3T4lnMcWLPCkP', '2025-01-31 12:02:05', '2025-01-31 12:02:05', 1),
	(122, 'SALETE APARECIDA ANTUNES', 'salete_antunes1970@hotmail.com', NULL, '$2y$12$uR5ooM5W3uFVWyPDFo1aGOifnJbvOtDX51QD.f1Uw2j0kvsPztE3G', NULL, '2025-01-31 12:02:42', '2025-01-31 12:02:42', 1),
	(123, 'BOX SERVIÇOS ', 'ordemdeservico@boxservicos.com.br', NULL, '$2y$12$n5CvAqhcaUkKu53BlSXJ3.00uOwJCFIxeJAhgtn9U3QtcNsjM9zFO', 'Ycu5LWJOSSPJvyMeWZ5EM10tBf5DjWU6vTUbROngqCrF3DMEW1oGcLu6PDZR', '2025-02-03 18:06:05', '2025-05-02 22:57:46', 1),
	(124, 'JLM CLIMATIZAÇÃO LTDA ', 'jetrefrigeracao2018@outlook.com', NULL, '$2y$12$1X.OgBlF6f76ZflhDctmeutRQo9E0R1/T5DD8EkQ3AMQHW7YLyVcG', 'VCQQgiDhrfM7gbnwiYv7ETSsFSPNya8do1GtDdNoKUqnRL5C05N7wJTCDuiC', '2025-02-03 18:15:03', '2025-04-30 17:23:32', 1),
	(125, 'GLEIDSON SILVA CABRAL ', 'gleidsonsilva55@hotmail.com', NULL, '$2y$12$yyQjvGYAiuQ4Pfx5WRPZKeX5o.dDJDzDMK3L2Hwp2r/KXXTaOwtW.', '1zuQcZKDZVBRFvQBzqix9Om5FIt3OHyYRdpYa9unpJWqJwaimC1V5nA41ODC', '2025-02-04 12:15:01', '2025-02-04 20:15:30', 1),
	(126, 'ANGELA FATIMA FREITAS', 'candeiaangela624@gmail.com', NULL, '$2y$12$4xAiytKmw40mnTJZcajoGefrvp5l5/TzXaqyADoASpT6gEzPXHWSG', 'kYK9rninod83NSvoqZZx9C0gkx2xZwtxgn8vLfhhARo4mo7JIdtms1Txv7Vq', '2025-02-05 12:40:22', '2025-05-14 22:50:58', 1),
	(127, 'CRECHE MÃE AMIGA', 'cmeiimaeamiga@novamutum.mt.gov.br', NULL, '$2y$12$vbUA3zIOAf5Dtr6NALkj3uIu4UEATOLJtJJ8hgO09zezwBJI2yWY.', 'kz1T3oJ1VtxRYBu9GkT9STAJjVp3QE6pjc7TAl4aVxlEVEd62IesdynB0Q4g', '2025-02-05 14:28:36', '2025-02-05 14:28:36', 1),
	(128, 'ANDREIA DALA RIVA', 'cmeiiaquareladosaber@novamutum.mt.gov.br', NULL, '$2y$12$w1JrKJYFyzbQtFMm8d3rouMcl.sgUH8rF81JwHjyBpaE6SvoURJgu', NULL, '2025-02-05 18:44:51', '2025-02-05 18:44:51', 1),
	(129, 'INGRID SANTOS', 'cmebicarlosdrummonddeandrade@novamutum.mt.gov.br', NULL, '$2y$12$7WALJxUEV6Jg3e7LlkK9oO/OI.6bIDg3tIC6tGaHNzkWZo5oG7amG', 'UVrQst1iJDmwDClNP3f509Nyp2RARIkhlg8khUR9MZa0xpggn8Qz6cGk3Kz2', '2025-02-05 20:09:51', '2025-04-16 15:51:35', 1),
	(130, 'DORALICE CORREIA DE ALMEIDA', 'enfermeiradoralice@hotmail.com', NULL, '$2y$12$ShgZt.5ARdZw1OtzMX0CRuEKP2g99olcsRDJCNorcNO40YyHPTTxm', 'oxR3meBRKZ8jbTZde0m9f4egjn3Tiko2rbnPh2h5TOdJpaQj4spNwVjO8jDo', '2025-02-07 12:32:22', '2025-04-16 16:56:48', 1),
	(131, 'JACQUELINE COSTA OLIVEIRA', 'emebosemeador@novamutum.mt.gov.br', NULL, '$2y$12$cax0QDmcbFukBNT03Uyza.cS2wpElEeL54XozR3iadJF2W17Oy8DS', 'gwxcdRvMBZhZBfaB8C3d9LxPryaO8O58kICe9cglobHmhrivJ2xjim9hxcMd', '2025-02-07 20:40:40', '2025-02-07 20:51:11', 1),
	(132, 'ELIANDRA OLIVEIRA ROMA', 'cmeiipequenosbrilhantes@novamutum.mt.gov.br', NULL, '$2y$12$bAg5W/iNlNVUrxUJ/yZ7oOL1PXa6eQgkeHmj86O4oRvCGz8ysnNEG', 'gvutvQjwL1IG8qYWRkrSPQJP4ZacdXdYp011nn0j9nbNKdfF2wZVjuXk1aiK', '2025-02-11 19:41:36', '2025-02-11 19:41:36', 1),
	(133, 'GILMAR ZACARIAS DE GODOY', 'emebimartonlucca@novamutum.mt.gov.br', NULL, '$2y$12$6L4vydfNX7T3D.zNTacd3.6yjrxFrxPr8F8nBKnyvwGns9DbVf1QG', 'LpuA2aejiH0QJe3e3aMK3wrObyVKbUGsKi3IPfRnG2Z73PjJ0f9wk3B2oCK1', '2025-02-12 12:58:54', '2025-02-12 12:58:54', 1),
	(134, 'JULIANA COSTA GUIMARÃES', 'emeb15deoutubro@gmail.com', NULL, '$2y$12$rl2Exh1w/yeBoAeEtfAwTOeER6ya39yZA0ndgqyqlHnGFynxYi6Le', NULL, '2025-02-12 14:54:31', '2025-02-12 14:54:31', 1),
	(135, 'MARIA DA GUIA RAMOS SANTANA', 'cmeiimonteirolobato@novamutum.mt.gov.br', NULL, '$2y$12$6MicJSNpCsWXN0tK6TqnPeqU29ixcILjOi61.7wWjWE.D.R6xnB2G', '8VuZO3tAF6FRf0bbOQPnhYIwXfP9p0oIapLcj1rnx0j1Hh7uNF2N3JE3bNLc', '2025-02-12 15:20:37', '2025-02-12 15:20:37', 1),
	(136, 'ROSILENE HERBES', 'cmebiluciafacciotasca@novamutm.mt.gov.br', NULL, '$2y$12$L4GJVj.zlDsWQvhSfZYjiObZALK5mTQPQNit5xwTz4I18M9mFGa46', 'bJVd5IDBox37KKDbHvmDAMGI7htYiwTy6SUDYcmc9dd4gWFI5ANpPLu5L52l', '2025-02-12 15:21:57', '2025-04-29 17:42:02', 1),
	(137, 'JANE CRISTINA TONEZE DE SOUZA', 'cmeiiviladossonhos@novamutum.mt.gov.br', NULL, '$2y$12$XrmkLMuX4RJZ9GI8yQEquuDxkrmErGUXFEVNBPjdPUQVtoedhQ66y', '97FxFuPTKTyscVUgOWlOHxNRV5PCxg71e1oAUGjp4Al69CfRkSXtxyidJk9t', '2025-02-12 18:46:11', '2025-02-12 18:46:11', 1),
	(138, 'NATANE BRAMBILA GIOPATTO', 'ceiirecantodosencantos@novamutum.mt.gov.br', NULL, '$2y$12$F2SJNehKdLevDgZBHaN92.rMSLJ4b6k3yVkS4pi9vmFnFh1b/f.Gy', 'lh7JVN43FHlRuDBiykgIL4eVPCCXpJQTVsHzNCTUFGIdBdiGBJqoPkN07mlz', '2025-02-13 12:57:02', '2025-02-13 12:57:02', 1),
	(139, 'JANAINA DA COSTA OLIVEIRA', 'cmebicoracoralina@novamutum.mt.gov.br', NULL, '$2y$12$2DLmE9iy9tX/Me1t5D/dIO0sIww5fol68MNWHzRcHdB65MKVsWPFm', 'ckq5l4gHbIdR1ilfH6gL86ubHt7jhQwblJ4TpFZdYShEW1fmXObuzRv1dNfo', '2025-02-13 19:16:40', '2025-02-13 19:16:40', 1),
	(140, 'KEILA PATRÍCIA SILVA SANTOS', 'ceiipequenoaprendiz@novamutum.mt.gov.br', NULL, '$2y$12$EcymBaYGNL4oOUvje./z4.Hk668Zvw70llVi4gYXrr6nfg45Spuq6', '6Br6idh5q4pPM992akGhUoTeKIypQLAGQB8M4Xbo8IP6kQh1h3IV5Xb5VB0m', '2025-02-14 18:42:26', '2025-02-14 18:47:33', 1),
	(141, 'DANIELE WOLFF SANTIN', 'psicologa.sesmt@novamutum.mt.gov.br', NULL, '$2y$12$mwFQToA3mxrNCbhcghp/kO673DhsSHBl5vQcPQWO8rLy1j0DiWOv2', NULL, '2025-02-20 20:26:26', '2025-02-20 20:26:26', 1),
	(142, 'DEBORA MILENA TREVISANUTTO', 'deboratrevisanutto7@gmail.com', NULL, '$2y$12$yk0GMDK3.lV7FoVnkYO7ReZmJdscEvmMUScBdopQVqbaexMM9gcHy', NULL, '2025-03-05 10:55:03', '2025-03-05 10:55:03', 1),
	(143, 'ELIANE GARBIN', 'cmeiijardimencantado@novamutum.mt.gov.br', NULL, '$2y$12$Q5LwIuABI7yXaKnxFVNbU.6fFnKz/7zdFqxzXM/zW5sTsWTYubz1i', 'MndjMN0ulE6fRuzMB56h3BZ5YCrRe2AXinzpKhCZu2jnAiJa2itLvLB0b03Y', '2025-03-10 18:18:48', '2025-03-18 16:56:30', 1),
	(144, 'RAFAELLA FERNANDES DALLA VALLE', 'emcsaojose@novamutum.mt.gov.br', NULL, '$2y$12$CnXxGF0NmIMXBmOtc3eXEekavHViLhEdaoVQcMwein2VxdrcDnD2y', NULL, '2025-03-19 18:41:51', '2025-03-19 18:41:51', 1),
	(145, 'MAISA HAMMERSCHMITT', 'maisa_schmitt@hotmail.com', NULL, '$2y$12$yWoOhGRMOySd7vUhGLjGOuTsOzIuX2AC3noUHKiT7YgN3TiJiVWEK', 'QKLXAwg6FWeoEZiW8HqBVQaUqdTzyr28irsTUNGZcvYYGNlZFO1SVfIiCkNo', '2025-03-24 18:38:28', '2025-03-24 18:38:28', 1),
	(146, 'HELEN DAYANE LOURENÇO DA SILVA', 'cmebi.caminhosdosaber@novamutum.mt.gov.br', NULL, '$2y$12$Jg/5y7uCfHJ3sZCv.hg3v.fbNQLOYHBMqbDlfwKjtMJOJ14EEJpra', NULL, '2025-03-25 19:39:33', '2025-03-25 19:39:33', 1),
	(147, 'DAIANA BEATRIZ SCHWERZ', 'daiana.schwerz@hotmail.com', NULL, '$2y$12$/2rKXS2379ELj9Dr4BJHM.uu3ogGBMdrkCAgieqsEQ.sF9nsOY9k2', NULL, '2025-04-02 17:23:18', '2025-04-02 17:23:18', 1),
	(148, 'THIAGO FRANCISCO DOS SANTOS', 'thiagofco85@gmail.com', NULL, '$2y$12$dBuaDJBDuaKJsQWwJWQV8.sZ2dgG140fAei1awIJbIhLIkzzhd/pa', NULL, '2025-04-07 22:27:43', '2025-04-15 23:19:54', 1),
	(149, 'SANDRA BENEDITA NEVES', 'sandrabeneves@hotmail.com', NULL, '$2y$12$3pmferyqSEWg/LWLC2R5BOEESdQZASuhfy4bAY8bPpssaAKkhTOtG', NULL, '2025-04-11 20:22:10', '2025-04-11 20:22:10', 1),
	(150, 'JACKELINY LAURINDO DA FONSECA', 'jacky.laurindo@hotmail.com', NULL, '$2y$12$lM9mVQUL6LFe0vTfTKALGepBPG5bkROjjn9wJlYEcMGGjOVdd8uwy', NULL, '2025-04-16 23:13:58', '2025-04-16 23:13:58', 1),
	(151, 'JULIANE RODRIGUES DE SOUZA', 'julianerodrigues2008@hotmail.com', NULL, '$2y$12$vfLti7Xuxx09wP9rAjnyhurJAckOFL270cfnxLf3PMRg91MH43sHa', NULL, '2025-04-24 16:29:42', '2025-04-24 16:29:42', 1),
	(152, 'ROGÉRIO JÚNIOR DA SILVA', 'juninho.ita41@gmail.com', NULL, '$2y$12$X/PZYQJhSCjZM9RNQE99k.bdNbTFr4x1cYKd5S8fQkejtv1wx8Yhy', 'iV4jdVphOvwDtN6l0IDBjXtnDd3rA76uFUvlDO0SuIJHhY0zOvzKtJsPgorO', '2025-04-25 16:45:22', '2025-04-25 16:45:22', 1),
	(153, 'RAFAELLA JAMILY DA SILVA FELIX', 'cmebiceciliameireles@novamutum.mt.gov.br', NULL, '$2y$12$YSrHgSIQM6lZG4YwKg2lOuUtQfu2nRphX.vk.s78YwqOe1ksPENOy', 'fCoWigC1Rlg6y8j85ChRozTgPdgHTxW9VVwmOZLAWxqom7g1DcrnfNV6Xy2c', '2025-04-25 21:45:46', '2025-04-25 21:45:46', 1),
	(154, 'HELEN DAYANE LOURENÇO DA SILVA', 'cmebicaminhosdosaber@novamutum.mt.gov.br', NULL, '$2y$12$YBAv2vWJ5Gqh0BYP34fz5.k3zaFQW2Fdgh5hK/fzc.NWV79Ii1z1O', NULL, '2025-04-25 23:51:35', '2025-04-25 23:51:35', 1),
	(155, 'CLEIDE DA SILVA', 'cleidedasilva407@gmail.com', NULL, '$2y$12$yCFp.8E6bVVzDoLxbv5wxOoPg.GiLFvxp7EO6SbiHI4PCouJjrUBu', NULL, '2025-05-06 21:46:46', '2025-05-06 21:46:46', 1),
	(156, 'ELENICE APARECIDA DA SILVA', 'preranchao@novamutum.mt.gov.br', NULL, '$2y$12$v1iKNnbL1tEYVo5Z.Wj3FOIG9xaDSGps9.oT/oB6Yi3X25FKpgfZW', NULL, '2025-05-07 18:03:09', '2025-05-07 18:03:09', 1),
	(157, 'MAYCON GHIZZI', 'adm@eseti.ws', NULL, '$2y$12$usTXTM.HdUGdAU0LOSAWke/5pbz1NLnv6hPXIDUwl6hk0IQc/ORs2', NULL, '2025-05-08 23:24:39', '2025-05-08 23:24:39', 1),
	(158, 'JAQUELINE COSTA OLIVEIRA', 'embosemeador@novamutum.mt.gov.br', NULL, '$2y$12$n9TU/F/2qNaNze1fbkt76O.rCCLAlG7VDglAU9rSDeFbBw9UIlByO', 'RBGju1XR7OFCppGgNgyuNIqdbPPhpLjcSE3Y0bfxEzr8mdoilte8kPNCzhPP', '2025-05-09 23:22:29', '2025-05-09 23:22:29', 1),
	(159, 'KEILA PATRÍCIA SILVA SANTOS', 'ceiipequenoaprendiz@novamutum.gov.br', NULL, '$2y$12$MF1gXFmO/QBpUgKpQtO3cuYnKuzp7XWxjm26KM0OJPWMr8v8M.wU2', NULL, '2025-05-13 18:45:23', '2025-05-13 18:45:23', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
