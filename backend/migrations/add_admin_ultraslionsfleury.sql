-- Cree ou promeut le compte ultraslionsfleury@gmail.com en admin.
-- Mot de passe temporaire: UltrasLions!2026
-- Compatible avec:
-- - table users ou `user`
-- - colonne email ou mail
-- - presence ou non de la colonne pseudo

SET @target_email = 'ultraslionsfleury@gmail.com';
SET @password_hash = '$2y$10$ObBvuxwt4BN1UyCN65SVw.tzgU0DJq6RNn.wHEe3o8hV1cJpdDaPC';
SET @pseudo_value = 'ultraslionsfleury';

SET @table_name = NULL;
SET @email_column = NULL;
SET @has_pseudo = 0;
SET @user_exists = 0;

SELECT CASE
    WHEN EXISTS (
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'users'
    ) THEN 'users'
    WHEN EXISTS (
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'user'
    ) THEN 'user'
    ELSE NULL
END
INTO @table_name;

SELECT CASE
    WHEN EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = @table_name
          AND COLUMN_NAME = 'email'
    ) THEN 'email'
    WHEN EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = @table_name
          AND COLUMN_NAME = 'mail'
    ) THEN 'mail'
    ELSE NULL
END
INTO @email_column;

SELECT COUNT(*)
INTO @has_pseudo
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = @table_name
  AND COLUMN_NAME = 'pseudo';

SET @sql = IF(
    @table_name IS NULL OR @email_column IS NULL,
    'SELECT ''Schema utilisateur introuvable: table users/user ou colonne email/mail absente.'' AS error_message',
    CONCAT(
        'SELECT COUNT(*) INTO @user_exists FROM `', @table_name, '` WHERE `', @email_column, '` = ', QUOTE(@target_email)
    )
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(
    @table_name IS NULL OR @email_column IS NULL,
    'SELECT ''Aucune modification effectuee.'' AS status_message',
    IF(
        @user_exists > 0,
        CONCAT(
            'UPDATE `', @table_name, '` ',
            'SET `roles` = JSON_ARRAY(\"ROLE_ADMIN\") ',
            'WHERE `', @email_column, '` = ', QUOTE(@target_email)
        ),
        IF(
            @has_pseudo > 0,
            CONCAT(
                'INSERT INTO `', @table_name, '` (`', @email_column, '`, `roles`, `password`, `pseudo`) VALUES (',
                QUOTE(@target_email), ', ',
                'JSON_ARRAY(\"ROLE_ADMIN\"), ',
                QUOTE(@password_hash), ', ',
                QUOTE(@pseudo_value),
                ')'
            ),
            CONCAT(
                'INSERT INTO `', @table_name, '` (`', @email_column, '`, `roles`, `password`) VALUES (',
                QUOTE(@target_email), ', ',
                'JSON_ARRAY(\"ROLE_ADMIN\"), ',
                QUOTE(@password_hash),
                ')'
            )
        )
    )
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
