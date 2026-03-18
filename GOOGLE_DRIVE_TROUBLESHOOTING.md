# Google Drive OAuth Upload Issue - Diagnosis & Solution

## Problem
Files are not being saved to the Google Drive folder: https://drive.google.com/drive/folders/0AP4MLcqJJB2aUk9PVA

## Root Causes (Possible)

### 1. **No OAuth Authentication**
The most common issue is that you haven't authenticated with Google OAuth yet, or your access token has expired.

**How to Fix:**
1. Visit: `http://localhost/HRMO/public/google/redirectToGoogle`
2. Authenticate with your Google account
3. Grant permissions for Google Drive access
4. You'll be redirected back with an access token stored in session

### 2. **Using Shared Drive ID Instead of Folder ID** ⚠️ CRITICAL

The ID `0AP4MLcqJJB2aUk9PVA` is a **Shared Drive ID**, NOT a folder ID!

**This is the main issue!** You need to:

1. Go to the Shared Drive: https://drive.google.com/drive/folders/0AP4MLcqJJB2aUk9PVA
2. **Create a new folder inside it** (e.g., "HRMO Uploads")
3. Get that **folder's ID** from the URL (will be ~33+ characters)
4. Update your `.env` file with the NEW folder ID

**How to tell the difference:**
- **Shared Drive ID**: Short format (like `0AP4MLcqJJB2aUk9PVA` - 18 chars)
- **Regular Folder ID**: Long format (like `1ABCxyz123...` - 33+ characters)

**Example:**
```
URL: https://drive.google.com/drive/folders/0AP4MLcqJJB2aUk9PVA/subfolders/1A2B3C4D5E6F7G8H9I0J
                                                              ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                                                              This is what you need in .env!
```

**What I've Fixed:**
- Added detection to tell you if you're using a Shared Drive ID vs Folder ID
- Updated `GoogleDriveOAuthService.php` to properly handle Shared Drives
- Added `supportsAllDrives=true` parameter to all API calls
- Added automatic file verification and movement to correct folder

### 3. **Insufficient Folder Permissions**
Even after authentication, your Google account might not have write permissions to the target folder.

**How to Verify:**
1. Go to the folder in Google Drive
2. Click "Share" 
3. Check if your authenticated Google account has "Editor" or higher permissions

## Testing Steps

### Step 1: Authenticate with Google OAuth
```
Visit: http://localhost/HRMO/public/google/redirectToGoogle
```

### Step 2: Check Authentication Status
After authentication, check if you're redirected successfully and see a success message.

### Step 3: Test File Upload
Try uploading a document through the Account page or Application form.

### Step 4: Check Logs
If upload fails, check the logs at:
```
writable/logs/log-YYYY-MM-DD.php
```

Look for messages starting with:
- "Google OAuth:"
- "Google Drive Upload"
- "File Metadata Check"
- "Move File Response"

## What Changed in the Code

### Updated: `app/Libraries/GoogleDriveOAuthService.php`

1. **Enhanced Authentication Logging**
   - Now logs when token is found, expired, or refreshed
   - Better error messages for debugging

2. **Improved Upload Method**
   - Added support for Shared Drives with `supportsAllDrives=true`
   - Added file location verification after upload
   - Automatic file movement if uploaded to wrong location
   - Better error handling with detailed HTTP code logging

3. **New Helper Methods**
   - `verifyFileInSharedDrive()` - Checks if file is in correct folder
   - `moveFileToFolder()` - Moves file to target folder if needed
   - Enhanced `makeFilePublic()` with logging

## How to Use Debug Controller (Optional)

If issues persist, you can use the debug controller:

1. Add route in `app/Config/Routes.php`:
```php
$routes->get('debug-oauth', 'DebugOAuth::index');
```

2. Visit: `http://localhost/HRMO/public/debug-oauth`

This will show detailed diagnostic information about:
- Authentication status
- Token details
- Upload process
- File location verification

## Common Error Messages & Solutions

### "Google Drive service not enabled"
**Cause:** No valid access token in session
**Solution:** Re-authenticate via `/google/redirectToGoogle`

### "Upload failed with HTTP code: 404"
**Cause:** Folder ID doesn't exist or you don't have access
**Solution:** Verify folder ID and check permissions

### "Upload failed with HTTP code: 403"
**Cause:** Insufficient permissions on the folder
**Solution:** Get Editor access to the target folder

### "Access token is expired" + refresh fails
**Cause:** Refresh token expired or invalid
**Solution:** Re-authenticate via `/google/redirectToGoogle`

## Manual Verification

After uploading a file, you can verify it's in the correct location:

1. Check the returned File ID URL
2. Go to https://drive.google.com/drive/folders/0AP4MLcqJJB2aUk9PVA
3. Look for the uploaded file in that folder
4. If not there, check your "My Drive" root folder

## Next Steps

1. **First:** Authenticate via OAuth if you haven't already
2. **Test:** Try uploading a small test file
3. **Verify:** Check both the logs and the actual Google Drive folder
4. **Report:** If still failing, share the error message from logs

## Important Notes

- The OAuth token is stored in **session**, so it persists only while logged in
- Session data is stored in `writable/session/` directory
- Token automatically refreshes if you have a valid refresh token
- For production, consider storing tokens in database for persistence
