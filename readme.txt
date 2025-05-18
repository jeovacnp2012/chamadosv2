<?php
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned NOT NULL,
`section_id` bigint unsigned NOT NULL,
`enforcer_id` bigint unsigned NOT NULL,
`patrimony_id` bigint unsigned NOT NULL,
`problem` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
`protocol` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
`status` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Status o chamado',
`type_maintenance` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
`closing_date` date DEFAULT NULL,
`patrimony` tinyint unsigned DEFAULT NULL,

✅ Adicionar lógica para permitir apenas 1 chamado aberto por patrimônio
✅ Exibir uma notificação automática ao setor responsável
✅ Criar painel com chamados pendentes + tempo médio de resolução


