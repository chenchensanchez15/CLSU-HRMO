# How to Add the `created_by_admin` Column

## Problem
The migration system has conflicts because an old migration (`add_type_columns_to_application_personal`) is trying to add columns that already exist in the database.

## Solution - Manual SQL Execution

### Option 1: Using phpMyAdmin (Recommended)

1. **Open phpMyAdmin**
   - Go to: http://localhost/phpmyadmin
   - Select your HRMO database

2. **Run this SQL query:**
```sql
ALTER TABLE users 
ADD COLUMN created_by_admin TINYINT(1) DEFAULT 0 
COMMENT '0=Self-registered, 1=Created by admin' 
AFTER first_login;
```

3. **Verify it worked:**
   - Click on the `users` table
   - Click "Structure" tab
   - Look for `created_by_admin` column in the list

### Option 2: Using MySQL Command Line

1. **Open command prompt and login to MySQL:**
```bash
mysql -u root -p
```

2. **Select the database:**
```sql
USE hrmo;
```

3. **Add the column:**
```sql
ALTER TABLE users 
ADD COLUMN created_by_admin TINYINT(1) DEFAULT 0 
COMMENT '0=Self-registered, 1=Created by admin' 
AFTER first_login;
```

4. **Verify:**
```sql
DESCRIBE users;
```

5. **Exit:**
```sql
EXIT;
```

### Option 3: Using the Provided SQL File

I've created a SQL file that checks if the column exists before adding it:

```bash
# Copy the SQL content
File: c:\xampp\htdocs\HRMO\add_created_by_admin_column.sql
```

Then run it in phpMyAdmin or MySQL command line.

## After Adding the Column

### 1. Update Migration Record (Optional)
If you want CodeIgniter to know the migration ran:

```sql
INSERT INTO migrations (version, filename, group, migrated_on, batch)
VALUES ('20260309000000', '20260309000000_add_created_by_admin_to_users.php', 'default', UNIX_TIMESTAMP(), 3);
```

### 2. Test the Feature

Create a test user with `created_by_admin = 1`:

```sql
UPDATE users 
SET created_by_admin = 1 
WHERE id = YOUR_USER_ID;
```

Replace `YOUR_USER_ID` with an actual user ID from your database.

### 3. Verify It Works

1. Login as that user
2. Try to apply for a job
3. You should NOT see the yellow verification boxes
4. Self-registered users should still see them

## Troubleshooting

### Error: "Duplicate column name 'created_by_admin'"
This means the column already exists. You can verify with:

```sql
SHOW COLUMNS FROM users LIKE 'created_by_admin';
```

### Migration Still Shows Errors
The migration conflicts are from OLD migrations that were already run manually but not recorded properly. To fix:

1. **Check what's in migration table:**
```sql
SELECT * FROM migrations ORDER BY batch;
```

2. **If you see entries that don't match your files, delete them:**
```sql
DELETE FROM migrations WHERE filename = 'add_type_columns_to_application_personal';
DELETE FROM migrations WHERE filename = 'add_is_posted_to_job_vacancies';
```

3. **Then run migration again:**
```bash
php spark migrate
```

## What Each Value Means

| Value | Meaning | Use Case |
|-------|---------|----------|
| `0` | Self-registered | User registered via login page |
| `1` | Admin-created | Admin added the account with pre-filled data |

## Default Behavior

- **New users**: Default is `0` (self-registered)
- **Existing users**: Will be `NULL` or treated as `0`
- **Admin-created**: Must explicitly set to `1`

## For Admin System Integration

When creating users in your admin system:

```php
$data = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'password' => password_hash($tempPassword, PASSWORD_DEFAULT),
    'first_login' => 1,              // Force password change
    'created_by_admin' => 1,         // ← Mark as admin-created
    'role' => 'applicant'
];

$userModel->insert($data);
```

## Files Created for This Feature

1. ✅ `app/Database/Migrations/20260309000000_add_created_by_admin_to_users.php`
2. ✅ `app/Models/UserModel.php` (updated)
3. ✅ `app/Controllers/Auth.php` (updated)
4. ✅ `app/Controllers/Applications.php` (updated)
5. ✅ `app/Views/apply.php` (updated - 5 locations)
6. ✅ `ADMIN_CREATED_APPLICANTS.md` (documentation)
7. ✅ `add_created_by_admin_column.sql` (manual SQL script)

## Next Steps

1. **Run the SQL** to add the column (using any option above)
2. **Test with a user** by setting their `created_by_admin = 1`
3. **Verify the feature works** - no verification boxes for admin-created users
4. **Update admin system** to set `created_by_admin = 1` when creating accounts
