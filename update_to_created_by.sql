-- Change column name from 'created_by_admin' to 'created_by'
-- Run this in phpMyAdmin or MySQL command line

USE hrmo;

-- First, check if old column exists and rename it, or add new column
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @oldcolumnname = 'created_by_admin';
SET @newcolumnname = 'created_by';

-- Check if old column exists
SET @hasOldColumn = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE table_schema = @dbname 
    AND table_name = @tablename 
    AND column_name = @oldcolumnname);

-- Check if new column already exists
SET @hasNewColumn = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE table_schema = @dbname 
    AND table_name = @tablename 
    AND column_name = @newcolumnname);

-- If old column exists, rename it
SET @renameStmt = IF(@hasOldColumn > 0 AND @hasNewColumn = 0,
    'ALTER TABLE users CHANGE COLUMN created_by_admin created_by TINYINT(1) DEFAULT 0 COMMENT ''0=User registered (login page), 1=Created by admin'' AFTER first_login',
    'SELECT 1'
);

PREPARE renameIfExists FROM @renameStmt;
EXECUTE renameIfExists;
DEALLOCATE PREPARE renameIfExists;

-- If neither column exists, add the new one
SET @hasEitherColumn = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE table_schema = @dbname 
    AND table_name = @tablename 
    AND (column_name = 'created_by_admin' OR column_name = 'created_by'));

SET @addStmt = IF(@hasEitherColumn = 0,
    'ALTER TABLE users ADD COLUMN created_by TINYINT(1) DEFAULT 0 COMMENT ''0=User registered (login page), 1=Created by admin'' AFTER first_login',
    'SELECT 1'
);

PREPARE addIfNotExists FROM @addStmt;
EXECUTE addIfNotExists;
DEALLOCATE PREPARE addIfNotExists;

-- Verify the column
SELECT 
    COLUMN_NAME as 'Field',
    COLUMN_TYPE as 'Type',
    IS_NULLABLE as 'Null',
    COLUMN_DEFAULT as 'Default',
    COLUMN_COMMENT as 'Comment'
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'users'
AND COLUMN_NAME IN ('created_by', 'created_by_admin');
