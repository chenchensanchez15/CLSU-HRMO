# 🎯 QUICK FIX: Shared Drive vs Folder ID Issue

## ❌ The Problem

Your current `.env` has:
```env
GOOGLE_DRIVE_FOLDER_ID = 0AP4MLcqJJB2aUk9PVA
```

**This is WRONG!** `0AP4MLcqJJB2aUk9PVA` is a **Shared Drive ID**, not a folder ID.

---

## ✅ The Solution (Choose ONE)

### OPTION A: Use Regular Folder (EASIEST - RECOMMENDED)

1. Go to your personal Google Drive: https://drive.google.com
2. Click **+ New** → **Folder**
3. Name it "HRMO Uploads" or similar
4. Open the folder
5. Copy the folder ID from the URL:
   ```
   https://drive.google.com/drive/folders/1ABCxyz123456789...
                                           ^^^^^^^^^^^^^^^^
                                           Copy this part (33+ chars)
   ```
6. Update your `.env`:
   ```env
   GOOGLE_DRIVE_FOLDER_ID=1ABCxyz123456789...
   ```

**Done!** This is the simplest solution.

---

### OPTION B: Create Folder Inside Shared Drive

If you MUST use the Shared Drive:

1. Go to the Shared Drive: https://drive.google.com/drive/folders/0AP4MLcqJJB2aUk9PVA
2. Click **+ New** → **Folder**
3. Name it "HRMO Uploads" or similar
4. Open that NEW folder
5. Copy the folder ID from the URL:
   ```
   https://drive.google.com/drive/folders/0AP4MLcqJJB2aUk9PVA/subfolders/1XYZabc789...
                                                                                   ^^^^^^^^^^^^^^^^
                                                                                   Copy this part (33+ chars)
   ```
6. Update your `.env`:
   ```env
   GOOGLE_DRIVE_FOLDER_ID=1XYZabc789...
   ```

**Important:** Make sure your Google account has **Editor** permission on the Shared Drive!

---

## 🔍 How to Verify It Worked

After updating `.env`:

1. **Clear config cache** (if using):
   ```bash
   php spark optimize:clear
   ```

2. **Authenticate with OAuth**:
   ```
   http://localhost/HRMO/public/google/redirectToGoogle
   ```

3. **Test upload** a file through your application

4. **Check** if file appears in the correct folder

---

## 📊 Quick Reference

| Type | Example ID | Length | Where to Find |
|------|-----------|--------|---------------|
| ❌ Shared Drive ID | `0AP4MLcqJJB2aUk9PVA` | 18 chars | NOT what you need! |
| ✅ Regular Folder ID | `1A2B3C4D5E6F7G8H9I0JkLmNoPqRsTuVwXyZ` | 33+ chars | Use this instead! |

---

## 💡 Pro Tip

**Use Option A** (regular folder in your personal Drive) unless you specifically need Shared Drive features like team collaboration.

Regular folders are:
- ✅ Easier to set up
- ✅ Fewer permission issues
- ✅ More reliable for single-user applications
- ✅ Work better with OAuth authentication

---

## Need Help?

Run the debug tool:
```
http://localhost/HRMO/public/debug-oauth
```

It will tell you:
- If you're authenticated ✓
- What type of folder ID you're using ✓
- Detailed upload diagnostics ✓
