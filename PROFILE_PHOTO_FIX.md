# Profile Photo Display Fix - Google Drive Integration

## Problem
The profile photo was displaying correctly on the Profile tab but **NOT** showing on the Home/Dashboard tab when the photo was stored in Google Drive.

### Root Cause
The Dashboard controller and view were only checking for **local file storage** and didn't have logic to handle **Google Drive file IDs**.

## Solution Implemented

### 1. Updated Dashboard Controller (`app/Controllers/Dashboard.php`)

**Added Google Drive detection logic:**
```php
// Check if profile photo exists (supports both Google Drive and local storage)
$profilePhoto = null;
$isGoogleDrivePhoto = false;

if (!empty($profile['photo'])) {
    // Check if it's a Google Drive file ID (20+ characters, alphanumeric with underscores/hyphens)
    if (preg_match('/^[a-zA-Z0-9_-]{20,}$/', $profile['photo']) && !preg_match('/^\d{10}_/', $profile['photo'])) {
        // Google Drive photo
        $profilePhoto = $profile['photo'];
        $isGoogleDrivePhoto = true;
    } else {
        // Local photo - verify file exists
        $photoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $profile['photo'];
        if (file_exists($photoPath)) {
            $profilePhoto = $profile['photo'];
            $isGoogleDrivePhoto = false;
        }
    }
}
```

**Added to data array:**
```php
'isGoogleDrivePhoto' => $isGoogleDrivePhoto,
```

### 2. Updated Dashboard View (`app/Views/dashboard.php`)

**Fixed TWO locations:**

#### Location 1: Navbar Dropdown (Lines 91-107)
```php
// Check if photo is from Google Drive or local storage
if (!empty($profile['photo']) && isset($isGoogleDrivePhoto) && $isGoogleDrivePhoto): ?>
    <!-- Google Drive Photo -->
    <img src="<?= site_url('photo/getProfilePhoto/' . session()->get('user_id')) ?>" class="w-8 h-8 rounded-full border-2 border-white object-cover">
<?php elseif(!empty($profile['photo']) && file_exists(FCPATH . 'uploads/' . $profile['photo'])): ?>
    <!-- Local Photo -->
    <img src="<?= base_url('uploads/' . $profile['photo']) ?>" class="w-8 h-8 rounded-full border-2 border-white object-cover">
<?php else: ?>
    <!-- No Photo -->
    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center border-2 border-white">
        <svg>...</svg>
    </div>
<?php endif; ?>
```

#### Location 2: Left Sidebar Profile Card (Lines 144-155)
```php
// Check if photo is from Google Drive or local storage
if (!empty($profile['photo']) && isset($isGoogleDrivePhoto) && $isGoogleDrivePhoto): ?>
    <!-- Google Drive Photo -->
    <img src="<?= site_url('photo/getProfilePhoto/' . session()->get('user_id')) ?>" class="w-full h-full object-cover rounded-full">
<?php elseif(!empty($profile['photo']) && file_exists(FCPATH . 'uploads/' . $profile['photo'])): ?>
    <!-- Local Photo -->
    <img src="<?= base_url('uploads/' . esc($profile['photo'])) ?>" class="w-full h-full object-cover rounded-full">
<?php else: ?>
    <!-- No Photo -->
    <svg>...</svg>
<?php endif; ?>
```

## How It Works

### Photo Detection Logic
1. **Check database** for `applicant_personal.photo` value
2. **Determine storage type**:
   - If matches pattern `/^[a-zA-Z0-9_-]{20,}$/` AND doesn't start with timestamp → **Google Drive**
   - If starts with timestamp (e.g., `1771380912_...`) → **Local storage**
3. **Display accordingly**:
   - Google Drive: Fetch via `/photo/getProfilePhoto/{userId}` endpoint
   - Local: Direct URL to `/uploads/{filename}`

### Photo Serving Endpoints

**Google Drive Photos:**
- URL: `site_url('photo/getProfilePhoto/' . $userId)`
- Controller: `App\Controllers\Photo::getProfilePhoto()`
- Process: Downloads from Google Drive → Serves as JPEG

**Local Photos:**
- URL: `base_url('uploads/' . $filename)`
- Physical location: `public/uploads/{filename}`
- Process: Direct file serving

## Database Examples

### Google Drive Photo (User ID: 78)
```sql
SELECT photo FROM applicant_personal WHERE user_id = 78;
-- Result: 1XeWDHRmU1mLAxJ7Zbyqz3jPTd1YgyrBs
```
- ✅ Recognized as Google Drive (33 characters, alphanumeric)
- ✅ Fetched via Photo controller

### Local Photo (User ID: 47)
```sql
SELECT photo FROM applicant_personal WHERE user_id = 47;
-- Result: 1771380912_cda2497d0ddbbd01770c.jpg
```
- ✅ Recognized as local (starts with timestamp `1771380912_`)
- ✅ Checks if file exists in `public/uploads/`

## Benefits

1. **Seamless Integration**: Works with both Google Drive and local storage
2. **Backward Compatible**: Existing local photos still work
3. **Consistent Display**: Same UI for both storage types
4. **No Migration Needed**: Old files continue to function
5. **Future-Proof**: New uploads use Google Drive by default

## Testing Checklist

- [x] User with Google Drive photo displays on Dashboard
- [x] User with local photo displays on Dashboard  
- [x] User without photo shows placeholder on Dashboard
- [x] Navbar dropdown shows correct photo
- [x] Left sidebar shows correct photo
- [x] Profile tab continues to work correctly
- [ ] Test with new photo uploads to Google Drive
- [ ] Test with new photo uploads to local storage

## Related Files

### Controllers
- `app/Controllers/Dashboard.php` - Main dashboard logic
- `app/Controllers/Photo.php` - Photo serving from Google Drive

### Views
- `app/Views/dashboard.php` - Dashboard view with photo display
- `app/Views/account/personal.php` - Profile tab (already working)

### Models
- `app/Models/ApplicantModel.php` - User profile data

## Notes

- The regex pattern `/^[a-zA-Z0-9_-]{20,}$/` matches Google Drive file IDs (typically 28-33 characters)
- The negative lookahead `!preg_match('/^\d{10}_/', ...)` excludes timestamp-prefixed local filenames
- Photo serving endpoint handles authentication and caching automatically
- Placeholder SVG shown when no photo exists
- **Important**: Use `session()->get('user_id')` in views instead of `$userId` variable which may not be available in all contexts

## Bug Fix History

### Issue #1: Undefined Variable
**Date:** Initial implementation  
**Error:** `ErrorException: Undefined variable $userId at line 97`

**Resolution:**
Changed from using `$userId` to `session()->get('user_id')` to ensure availability.

### Issue #2: Blank Image Display
**Date:** After first fix  
**Problem:** Photo showed as blank/empty image even though it displays correctly in Profile tab

**Root Cause Analysis:**
- Profile tab uses: `base_url('account/getProfilePhoto')` (relies on session)
- Dashboard used: `site_url('photo/getProfilePhoto/' . session()->get('user_id'))` (explicit parameter)
- The Photo controller's `getProfilePhoto()` method gets user ID from session when no parameter is provided
- Passing explicit parameter may have caused authentication/route issues

**Final Resolution:**
Matched the exact implementation from Profile tab:
```php
<!-- Before (❌ Blank image) -->
<img src="<?= site_url('photo/getProfilePhoto/' . session()->get('user_id')) ?>" class="...">

<!-- After (✅ Working) -->
<img id="profilePhoto" src="<?= base_url('account/getProfilePhoto') ?>" 
     class="w-full h-full object-cover rounded-full" 
     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
<svg id="profilePhotoPlaceholder" class="hidden" ...>...</svg>
```

**Key Changes:**
1. Use `base_url('account/getProfilePhoto')` instead of full URL with parameter
2. Added `onerror` handler to show placeholder on load failure
3. Changed container from `overflow-hidden` to `overflow-visible` for placeholder display
4. Added placeholder SVG element matching Profile tab implementation
