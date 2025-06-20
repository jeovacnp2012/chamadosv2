SELECT DISTINCT
    su.user_id,
    u.name as user_name,
    s.departament_id,
    d.name as departament_name,
    s.name as sector_name
FROM sector_user su
JOIN users u ON su.user_id = u.id
JOIN sectors s ON su.sector_id = s.id
JOIN departaments d ON s.departament_id = d.id
WHERE NOT EXISTS (
    -- Só inclui se o vínculo ainda não existir
    SELECT 1 FROM departament_user du 
    WHERE du.user_id = su.user_id 
    AND du.departament_id = s.departament_id
)
ORDER BY u.name, d.name;

INSERT INTO departament_user (user_id, departament_id)
SELECT DISTINCT
    su.user_id,
    s.departament_id
FROM sector_user su
JOIN sectors s ON su.sector_id = s.id
WHERE NOT EXISTS (
    -- Evita duplicatas - só insere se o vínculo ainda não existir
    SELECT 1 FROM departament_user du 
    WHERE du.user_id = su.user_id 
    AND du.departament_id = s.departament_id
);

-- ===============================================
-- SCRIPT PARA VINCULAR USUÁRIOS AOS DEPARTAMENTOS
-- BASEADO NOS SETORES QUE ELES JÁ POSSUEM
-- ===============================================

-- 1. PRIMEIRO: Vamos ver o que será inserido (CONSULTA DE VERIFICAÇÃO)
-- Execute esta query primeiro para ver quais vínculos serão criados
SELECT DISTINCT
    su.user_id,
    u.name as user_name,
    s.departament_id,
    d.name as departament_name,
    s.name as sector_name
FROM sector_user su
JOIN users u ON su.user_id = u.id
JOIN sectors s ON su.sector_id = s.id
JOIN departaments d ON s.departament_id = d.id
WHERE NOT EXISTS (
    -- Só inclui se o vínculo ainda não existir
    SELECT 1 FROM departament_user du 
    WHERE du.user_id = su.user_id 
    AND du.departament_id = s.departament_id
)
ORDER BY u.name, d.name;

-- 2. SEGUNDO: Inserir os vínculos automaticamente
-- Execute este comando para criar os vínculos na tabela departament_user
INSERT INTO departament_user (user_id, departament_id)
SELECT DISTINCT
    su.user_id,
    s.departament_id
FROM sector_user su
JOIN sectors s ON su.sector_id = s.id
WHERE NOT EXISTS (
    -- Evita duplicatas - só insere se o vínculo ainda não existir
    SELECT 1 FROM departament_user du 
    WHERE du.user_id = su.user_id 
    AND du.departament_id = s.departament_id
);

-- 3. TERCEIRO: Verificar quantos registros foram inseridos
SELECT 
    CONCAT('Foram criados ', COUNT(*), ' vínculos de usuários com departamentos') as resultado
FROM departament_user;

-- 4. QUARTO: Consulta final para verificar os vínculos criados
-- Mostra todos os usuários, seus setores e departamentos
SELECT 
    u.id as user_id,
    u.name as user_name,
    GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') as setores,
    GROUP_CONCAT(DISTINCT d.name ORDER BY d.name SEPARATOR ', ') as departamentos
FROM users u
LEFT JOIN sector_user su ON u.id = su.user_id
LEFT JOIN sectors s ON su.sector_id = s.id
LEFT JOIN departament_user du ON u.id = du.user_id
LEFT JOIN departaments d ON du.departament_id = d.id
WHERE su.user_id IS NOT NULL -- Só usuários que têm setores
GROUP BY u.id, u.name
ORDER BY u.name;
