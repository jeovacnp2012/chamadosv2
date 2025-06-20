-- ===============================================
-- CONSULTAS PARA USUÁRIOS SEM ROLES
-- ===============================================

-- 1. USUÁRIOS SEM NENHUMA ROLE VINCULADA
SELECT 
    u.id,
    u.name,
    u.email,
    u.created_at,
    'SEM ROLE' as status
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
WHERE mhr.model_id IS NULL
ORDER BY u.name;

-- 2. CONTAGEM DE USUÁRIOS SEM ROLES
SELECT 
    COUNT(*) as total_usuarios_sem_roles
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
WHERE mhr.model_id IS NULL;

-- 3. COMPARATIVO: USUÁRIOS COM E SEM ROLES
SELECT 
    CASE 
        WHEN mhr.model_id IS NULL THEN 'SEM ROLE'
        ELSE 'COM ROLE'
    END as status,
    COUNT(*) as quantidade
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
GROUP BY (mhr.model_id IS NULL)
ORDER BY status;

-- 4. USUÁRIOS COM SUAS ROLES (PARA COMPARAR)
SELECT 
    u.id,
    u.name,
    u.email,
    COALESCE(GROUP_CONCAT(r.name SEPARATOR ', '), 'SEM ROLE') as roles
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
LEFT JOIN roles r ON mhr.role_id = r.id
GROUP BY u.id, u.name, u.email
ORDER BY u.name;

-- 5. APENAS OS IDs DOS USUÁRIOS SEM ROLE (ÚTIL PARA SCRIPTS)
SELECT u.id
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
WHERE mhr.model_id IS NULL;

-- ===============================================
-- QUERIES EXTRAS (INFORMAÇÕES GERAIS)
-- ===============================================

-- Ver todas as roles disponíveis no sistema
SELECT id, name, created_at FROM roles ORDER BY name;

-- Ver quantos usuários cada role tem
SELECT 
    r.name as role_name,
    COUNT(mhr.model_id) as total_usuarios
FROM roles r
LEFT JOIN model_has_roles mhr ON r.id = mhr.role_id 
    AND mhr.model_type = 'App\\Models\\User'
GROUP BY r.id, r.name
ORDER BY total_usuarios DESC;

-- ===============================================
-- SCRIPT PARA ATRIBUIR ROLE PADRÃO (OPCIONAL)
-- ===============================================

-- Caso queira atribuir uma role padrão aos usuários sem role
-- SUBSTITUA 'ROLE_ID_AQUI' pelo ID da role que quer atribuir

/*
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT 
    ROLE_ID_AQUI,  -- ← SUBSTITUIR pelo ID da role desejada
    'App\\Models\\User',
    u.id
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
WHERE mhr.model_id IS NULL;
*/
-- ===============================================
-- SCRIPT PARA ATRIBUIR ROLE "SOLICITANTE" 
-- A TODOS OS USUÁRIOS SEM ROLE
-- ===============================================

-- 1. PRIMEIRO: Verificar se a role "Solicitante" existe
SELECT id, name FROM roles WHERE name = 'Solicitante';

-- Se a role não existir, descomente e execute a linha abaixo:
-- INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES ('Solicitante', 'web', NOW(), NOW());

-- 2. SEGUNDO: Ver quais usuários serão afetados (CONSULTA DE VERIFICAÇÃO)
SELECT 
    u.id,
    u.name,
    u.email,
    'Receberá role: Solicitante' as acao
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
WHERE mhr.model_id IS NULL
ORDER BY u.name;

-- 3. TERCEIRO: Contar quantos usuários serão afetados
SELECT 
    COUNT(*) as total_usuarios_que_receberao_role_solicitante
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
WHERE mhr.model_id IS NULL;

-- 4. QUARTO: EXECUTAR A ATRIBUIÇÃO DA ROLE
-- Este comando vai atribuir a role "Solicitante" a todos os usuários sem role
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT 
    (SELECT id FROM roles WHERE name = 'Solicitante' LIMIT 1) as role_id,
    'App\\Models\\User' as model_type,
    u.id as model_id
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
WHERE mhr.model_id IS NULL;

-- 5. QUINTO: Verificar se deu certo - Contar usuários sem role APÓS a atribuição
SELECT 
    COUNT(*) as usuarios_ainda_sem_role
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
WHERE mhr.model_id IS NULL;

-- 6. SEXTO: Ver o resultado final - Usuários com a role "Solicitante"
SELECT 
    u.id,
    u.name,
    u.email,
    r.name as role_atribuida
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\\Models\\User'
JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'Solicitante'
ORDER BY u.name;

-- ===============================================
-- VERIFICAÇÃO FINA