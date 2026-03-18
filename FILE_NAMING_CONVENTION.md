# Consistent File Naming Convention

## Overview
This document describes the standardized file naming convention implemented across the HRMO system for uploaded photos and documents.

## Problem Statement
Previously, there were inconsistencies in how files were named across different storage locations:
- **Google Drive**: Files named with format like `1772884837_2x2_photo.jpg`
- **Local uploads**: Used format `{userId}_{timestamp}_{randomName}` 
- **Database**: Stored either full filename or just Google Drive file ID

This made it difficult to track files across systems and caused confusion when debugging or migrating data.

## Solution Implemented

### Standard Naming Format
All uploaded files now use the consistent format:
```
{timestamp}_{sanitized_original_name}.{extension}
```

**Examples:**
- `1772884837_profile_photo.jpg`
- `1772884839_certificate_of_trainings_and_seminars.pdf`
- `1772884844_personal_data_sheet_pds_.pdf`

### Key Features
1. **Timestamp prefix**: Ensures uniqueness and chronological ordering
2. **Sanitized original name**: Maintains human-readability while removing special characters
3. **Proper extension**: Preserves the original file type
4. **Consistency**: Same format used for both Google Drive and local storage

## Implementation Details

### Affected Controllers

#### 1. Account Controller (`app/Controllers/Account.php`)

**Photo Uploads:**
- Profile photos (both Google Drive and local fallback)
- Naming: `{timestamp}_profile_photo.{ext}`

**Document Uploads:**
- General documents via Google Drive OAuth
- Naming: `{timestamp}_{sanitized_name}.{ext}`

**Training Certificates:**
- Local storage in `writable/uploads/trainings/`
- Naming: `{timestamp}_{sanitized_name}.{ext}`

**Civil Service Certificates:**
- Local storage in `writable/uploads/civil_service/`
- Naming: `{timestamp}_{sanitized_name}.{ext}`

#### 2. Applications Controller (`app/Controllers/Applications.php`)

**Job Application Documents:**
- PDS, TOR, Diploma, Resume, Performance Rating
- Local storage in `writable/uploads/files/`
- Naming: `{timestamp}_{sanitized_name}.{ext}`

**Training Certificates:**
- Local storage in `writable/uploads/trainings/`
- Naming: `{timestamp}_{sanitized_name}.{ext}`

### Sanitization Logic
```php
$extension = $file->getClientExtension();
$baseName = pathinfo($file->getClientName(), PATHINFO_FILENAME);
// Remove special characters except underscores and hyphens
$sanitizedBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
$fileName = time() . '_' . $sanitizedBaseName . '.' . $extension;
```

This ensures:
- Only alphanumeric characters, underscores, and hyphens are kept
- Special characters are replaced with underscores
- Multiple consecutive special characters result in multiple underscores (preserved for readability)

## Database Storage

### applicant_personal Table
- **Column**: `photo`
- **Stores**: 
  - Google Drive file ID (e.g., `1XeWDHRmU1mLAxJ7Zbyqz3jPTd1YgyrBs`)
  - OR local filename (e.g., `1772884837_profile_photo.jpg`)

### applicant_documents Table
- **Column**: `filename`
- **Stores**: 
  - Google Drive file ID
  - OR local filename

### application_documents Table
- **Columns**: `pds`, `tor`, `diploma`, `resume`, `performance_rating`
- **Stores**: Local filenames only

### applicant_trainings & application_trainings Table
- **Column**: `certificate_file`
- **Stores**: Local filenames

## Benefits

1. **Consistency**: Same naming pattern across all upload types
2. **Traceability**: Files can be easily matched between database, local storage, and Google Drive
3. **Human-readable**: Original filenames are preserved (sanitized) for easy identification
4. **Unique**: Timestamp prefix prevents filename collisions
5. **Sortable**: Files sort chronologically by upload date
6. **Safe**: Special characters removed to prevent filesystem issues

## Migration Notes

### Existing Files
- Old files will continue to work as-is
- File viewing logic already handles both old and new naming conventions
- No immediate migration required

### Future Uploads
- All new uploads automatically use the new convention
- Gradual transition ensures backward compatibility

## Testing Recommendations

1. Upload profile photo → Check Google Drive naming
2. Upload training certificate → Check local storage naming
3. Upload job application documents → Verify all document types
4. Verify database stores correct filenames
5. Test file viewing functionality for both old and new files

## Related Code Locations

### Models
- `App\Models\ApplicantDocumentsModel`
- `App\Models\ApplicationDocumentsModel`
- `App\Models\ApplicantTrainingModel`
- `App\Models\ApplicantCivilServiceModel`

### Controllers
- `App\Controllers\Account` - Photo and document uploads
- `App\Controllers\File` - File viewing
- `App\Controllers\Files` - Alternative file serving
- `App\Controllers\Photo` - Profile photo serving

### Libraries
- `App\Libraries\GoogleDriveOAuthService` - Google Drive upload logic
