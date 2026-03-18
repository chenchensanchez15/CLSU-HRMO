# Training Certificate Combination Feature

## Overview
This feature combines multiple training certificates into a single PDF document for cleaner display in the applications view. Instead of showing redundant entries for "Certificate of Trainings and Seminars", users will see only one entry that, when clicked, opens a combined PDF containing all training certificates.

## How It Works

### 1. DOMPDF Integration
- **Library**: dompdf/dompdf v3.0
- **Purpose**: Convert HTML content with multiple certificate pages into a single PDF
- **Location**: `app/Libraries/TrainingCertificateCombiner.php`

### 2. Combined PDF Generation
When viewing an application:
1. The system fetches all trainings for the application from `application_trainings` table
2. If there are multiple trainings with certificates, the `TrainingCertificateCombiner` library is invoked
3. Each certificate is processed and added as a separate page in a combined PDF
4. The combined PDF is cached (valid for 1 hour) to avoid regenerating on every view
5. Only ONE "Certificate of Trainings and Seminars" entry appears in the Uploaded Documents section

### 3. File Naming Convention
Combined PDFs are named using this pattern:
```
combined_training_{application_id}_{hash}.pdf
```
Example: `combined_training_123_a1b2c3d4.pdf`

The hash is generated from the training IDs to ensure unique filenames when the combination changes.

### 4. Certificate Display Format
Each certificate in the combined PDF includes:
- **Header**: "Certificate of Training" title with CLSU green styling
- **Training Information**:
  - Training Name
  - Category
  - Date (From - To)
  - Venue
  - Facilitator
  - Hours
  - Sponsor
- **Certificate Image**: Embedded image/PDF (for local files) or placeholder (for Google Drive files)
- **Remarks**: Any additional remarks

## Implementation Details

### Controller Changes (`Applications.php`)

#### In `view()` method (around line 689):
```php
// Special handling for trainings (document_type_id = 7)
if ($docTypeId == 7 && !empty($trainings)) {
    log_message('debug', 'Processing training certificates combination...');
    
    $combiner = new \App\Libraries\TrainingCertificateCombiner();
    $combinedFile = $combiner->getCombinedCertificatePath($application_id, $trainings);
    
    if ($combinedFile) {
        // Use the combined PDF file path instead
        $fileId = $combinedFile;
        log_message('debug', 'Using combined training certificate: ' . $combinedFile);
    }
}
```

#### New method `viewTrainingCertificate()` (around line 1488):
Handles viewing of combined PDF files by checking if the filename starts with `combined_training_`.

### View Changes (`applications/view.php`)

The uploaded documents section now properly constructs URLs for local combined PDFs vs Google Drive files:
```php
$isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $file['id']) && !preg_match('/^\d{10}_/', $file['id']);

if (!$isGoogleDriveFile) {
    // Local file - construct URL using controller endpoint
    if ($docTypeId == 7) {
        $fileUrl = site_url('applications/viewTrainingCertificate/' . $app['id_job_application'] . '/' . $file['id']);
    }
}
```

## Caching Strategy
- Combined PDFs are cached for 1 hour
- Cache is invalidated if the file is older than 1 hour
- This balances performance with data freshness

## Benefits

1. **Cleaner UI**: Only one "Certificate of Trainings and Seminars" entry instead of multiple duplicates
2. **Better UX**: Users can view all training certificates in one document with easy navigation
3. **Professional Presentation**: Certificates are formatted consistently with proper headers and information
4. **Performance**: Cached generation reduces server load
5. **Maintainability**: Centralized PDF generation logic

## Handling Different File Types

### Local Files
- Images (JPG, PNG) are embedded as base64 in the PDF
- PDFs would need special handling (currently shows placeholder)

### Google Drive Files
- Shows a placeholder indicating the certificate is stored in Google Drive
- Future enhancement could download and embed these files

## Usage

Users don't need to do anything special - the feature works automatically:
1. Admin views an application
2. Scrolls to "Uploaded Documents" section
3. Sees ONE "Certificate of Trainings and Seminars" entry
4. Clicks "View Document"
5. Opens combined PDF showing all training certificates in sequence

## Technical Requirements

- PHP 8.1+
- DOMPDF extension (installed via Composer)
- Write permissions to `writable/uploads/trainings/` directory
- Sufficient disk space for cached PDF files

## Troubleshooting

### Combined PDF not generating
1. Check if DOMPDF is installed: `composer show dompdf/dompdf`
2. Verify write permissions: `ls -la writable/uploads/trainings/`
3. Check logs: `writable/logs/log-*.php`

### Missing certificates in combined PDF
1. Verify trainings are properly saved in `application_trainings` table
2. Check if `certificate_file` field is populated
3. For Google Drive files, ensure OAuth service is working

### PDF quality issues
- Images are rendered at screen resolution
- Large images may be scaled down
- Consider optimizing certificate images before upload

## Future Enhancements

1. Support for embedding actual PDF certificates (not just images)
2. Download and embed Google Drive certificates instead of placeholders
3. Add table of contents for large numbers of certificates
4. Bookmarks/navigation within combined PDF
5. Configurable cache duration
6. Manual cache invalidation option
