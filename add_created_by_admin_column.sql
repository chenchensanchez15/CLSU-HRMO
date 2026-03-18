-- Add created_by_admin column to users table
-- Run this SQL manually in phpMyAdmin or MySQL command line

USE hrmo;

-- Check if column exists first
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'created_by_admin';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TINYINT(1) DEFAULT 0 COMMENT \'0=Self-registered, 1=Created by admin\' AFTER first_login')
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verify the column was added
SELECT 
    COLUMN_NAME as 'Field',
    COLUMN_TYPE as 'Type',
    IS_NULLABLE as 'Null',
    COLUMN_DEFAULT as 'Default',
    COLUMN_COMMENT as 'Comment'
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'users'
AND COLUMN_NAME = 'created_by_admin';
