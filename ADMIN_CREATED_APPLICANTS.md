# Admin-Created Applicants Feature Documentation

## Overview

This feature distinguishes between two types of applicants:
1. **Admin-Created Applicants** - Added by admin with pre-filled information, skip verification steps
2. **Self-Registered Applicants** - Registered via login page, must fill all information and go through verification

## Database Changes

### Migration File
**File:** `app/Database/Migrations/AddCreatedByAdminToUsers.php`

Adds a new column `created_by_admin` to the `users` table:
```sql
ALTER TABLE users ADD COLUMN created_by_admin TINYINT(1) DEFAULT 0 COMMENT '0=Self-registered, 1=Created by admin';
```

### Field Values
- `created_by_admin = 0` (Default) - Self-registered user
- `created_by_admin = 1` - Admin-created user

## Code Changes

### 1. UserModel
**File:** `app/Models/UserModel.php`

Added `created_by_admin` to `$allowedFields`:
```php
protected $allowedFields = [
    'first_name',
    'middle_name',
    'last_name',
    'extension',
    'email',
    'password',
    'first_login',
    'created_by_admin'  // NEW
];
```

### 2. Auth Controller
**File:** `app/Controllers/Auth.php`

Updated `loginPost()` to store `created_by_admin` in session:
```php
$session->set([
    'user_id' => $user['id'],
    'first_name' => $user['first_name'],
    'email' => $user['email'],
    'logged_in' => true,
    'first_login' => $user['first_login'],
    'created_by_admin' => $user['created_by_admin'] ?? 0  // NEW
]);
```

### 3. Applications Controller
**File:** `app/Controllers/Applications.php`

#### In `apply()` method:
```php
// Check if user was created by admin (has pre-filled data)
$createdByAdmin = session()->get('created_by_admin') ?? 0;

// Pass to view
return view('apply', [
    'job'            => $job,
    'profile'        => $profile,
    'requirements'   => $requirements,
    'createdByAdmin' => $createdByAdmin  // NEW
]);
```

### 4. Application View
**File:** `app/Views/apply.php`

Wrapped all verification prompts with conditional check:

#### Personal Information Verification (Line ~214)
```php
<?php if(empty($createdByAdmin)): ?>
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    <!-- Verification prompt content -->
</div>
<?php endif; ?>
```

#### Education Verification (Line ~407)
```php
<?php if(empty($createdByAdmin)): ?>
<!-- Verification & Edit Prompt for Educational Background -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    ...
</div>
<?php endif; ?>
```

#### Work Experience Verification (Line ~497)
```php
<?php if(empty($createdByAdmin)): ?>
<!-- Verification & Edit Prompt for Work Experience -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    ...
</div>
<?php endif; ?>
```

#### Civil Service Verification (Line ~624)
```php
<?php if(empty($createdByAdmin)): ?>
<!-- Verification & Edit Prompt for Civil Service -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    ...
</div>
<?php endif; ?>
```

#### Trainings Verification (Line ~906)
```php
<?php if(empty($createdByAdmin)): ?>
<!-- Verification & Edit Prompt for Trainings -->
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 rounded">
    ...
</div>
<?php endif; ?>
```

## How It Works

### For Admin-Created Applicants

1. **Admin creates account** in admin system with `created_by_admin = 1`
2. **Admin enters all information** (personal, education, work experience, etc.)
3. **Documents uploaded** to Google Drive/local storage
4. **Credentials sent** to applicant
5. **Applicant logs in** → Sees application form WITHOUT verification prompts
6. **Can directly apply** without editing pre-filled information

### For Self-Registered Applicants

1. **User registers** via login page (automatically `created_by_admin = 0`)
2. **User logs in** for first time
3. **User applies** for a job
4. **Sees ALL verification prompts**:
   - "Please verify that all information is correct"
   - "Edit Personal Info" button
   - "Edit Education Info" button
   - "Edit Work Experience" button
   - "Edit Civil Service Info" button
   - "Edit Trainings Info" button
5. **Must fill out all sections** before submission

## Benefits

### For Admin-Created Applicants
✅ **Streamlined process** - No redundant verification steps  
✅ **Faster application** - Skip straight to submission  
✅ **Better UX** - Cleaner interface without edit prompts  

### For Self-Registered Applicants
✅ **Complete verification** - Ensures all information is reviewed  
✅ **Error prevention** - Multiple chances to correct information  
✅ **Guided process** - Clear edit buttons for each section  

### For System Administrators
✅ **Flexibility** - Can create accounts with pre-filled data  
✅ **Control** - Decide which applicants need verification  
✅ **Efficiency** - Bulk processing of recruited candidates  

## Testing Checklist

### Test Case 1: Admin-Created User
- [ ] Create user with `created_by_admin = 1`
- [ ] Login as this user
- [ ] Apply for a job
- [ ] Verify NO yellow verification boxes appear
- [ ] Verify no "Edit" buttons show in sections
- [ ] Can submit application directly

### Test Case 2: Self-Registered User
- [ ] Register new user via login page
- [ ] Login as this user
- [ ] Apply for a job
- [ ] Verify ALL yellow verification boxes appear
- [ ] Verify all "Edit" buttons are visible
- [ ] Must complete all sections before submission

### Test Case 3: Mixed Scenario
- [ ] Some users created by admin, some self-registered
- [ ] Both types can apply for same job
- [ ] Each sees appropriate interface based on their type

## SQL Script (Manual Execution)

If you prefer to run the migration manually:

```sql
-- Add column to users table
ALTER TABLE users 
ADD COLUMN created_by_admin TINYINT(1) DEFAULT 0 
COMMENT '0=Self-registered, 1=Created by admin' 
AFTER first_login;

-- Add index for performance
CREATE INDEX idx_created_by_admin ON users(created_by_admin);
```

## Running the Migration

### Command Line
```bash
php spark migrate
```

### Check Migration Status
```bash
php spark migrate:status
```

### Rollback (if needed)
```bash
php spark migrate:rollback
```

## Integration with Admin System

### When Admin Creates Applicant

In your admin system's applicant creation code, set:
```php
$data = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'password' => $hashedPassword,
    'first_login' => 1,              // Force password change
    'created_by_admin' => 1,         // Mark as admin-created
    'role' => 'applicant'
];

$userModel->insert($data);
```

### After Creating Account
1. **Enter applicant's personal information** in `applicant_personal` table
2. **Upload documents** using GoogleDriveOAuthService
3. **Save document references** in `applicant_documents` table
4. **Send credentials** to applicant via email/SMS

## Notes

- The `created_by_admin` field defaults to `0` (self-registered) for backward compatibility
- Existing users in database will be treated as self-registered unless manually updated
- This field does NOT affect authentication or authorization
- Only affects the UI/UX during job application process
- Can be combined with `first_login` flag for complete control over user flow

## Future Enhancements

Potential improvements:
1. Add admin interface to toggle `created_by_admin` flag
2. Track which admin created the account (`created_by_admin_id`)
3. Add timestamp when admin creates account
4. Email notification template for admin-created accounts
5. Bulk import feature for creating multiple admin-created applicants
