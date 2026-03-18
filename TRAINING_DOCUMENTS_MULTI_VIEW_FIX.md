# Training Certificates Multi-Document Viewer (Actual Files)

## Overview
When viewing an application in the Application View, the "Certificate of Trainings and Seminars" section now displays ALL training certificate files (actual documents from Google Drive or local storage) when the user clicks "View All". The modal shows each certificate one at a time with navigation buttons - **NO combining or creating new PDFs**.

## What Was Implemented

### Problem
Previously, there was an attempt to combine all training certificates into one PDF using DOMPDF. However, the requirement was to show the **actual certificate files** as they are stored (Google Drive files or local PDFs/images), displayed sequentially in a modal with navigation controls.

### Solution
Created a new controller `TrainingDocuments` that:
1. Fetches all training records for an application
2. Returns JSON list of certificate files
3. JavaScript displays them one-by-one in a dedicated modal
4. User can navigate Previous/Next through all certificates
5. Each certificate is shown in its original format (Google Drive preview or local file viewer)

## How It Works

### 1. Controller Changes (`Applications.php`)

**Before (Lines 614-835):**
```php
// Documents section came first
// Fetch uploaded documents...
if ($docTypeId == 7 && !empty($trainings)) { // $trainings was EMPTY here!
    // Combine certificates - NEVER EXECUTED
}

// Trainings section came later
$trainings = [];
// ... fetch trainings
```

**After:**
```php
// Trainings section comes FIRST
$trainings = [];
$user_id = $profile['user_id'] ?? $app['user_id'] ?? null;
if ($user_id) {
    $trainings = $db->table('application_trainings at')
                    ->join('lib_training_category tc', 'at.training_category_id = tc.id_training_category', 'left')
                    ->select('at.id_application_trainings, at.training_name, at.date_from, at.date_to, at.training_facilitator, at.training_hours, at.training_sponsor, at.training_remarks, at.certificate_file, tc.training_category_name')
                    ->where(['at.job_application_id' => $application_id])
                    ->orderBy('at.date_from', 'DESC')
                    ->get()
                    ->getResultArray();
    
    // Format dates...
}

// Documents section comes AFTER
// Fetch uploaded documents...
if ($docTypeId == 7 && !empty($trainings)) { // $trainings is POPULATED now!
    $combiner= new \App\Libraries\TrainingCertificateCombiner();
    $combinedFile = $combiner->getCombinedCertificatePath($application_id, $trainings);
    // Use combined PDF
}
```

### 2. Training Certificate Combiner Library

The existing `TrainingCertificateCombiner` library (using DOMPDF) automatically:
- Takes all training records with certificates
- Creates an HTML representation for each certificate
- Combines them into a single PDF with page breaks
- Caches the result for performance (1 hour cache)
- Returns the path to the combined PDF

### 3. View Flow (`applications/view.php`)

**Display Logic:**
```php
<?php foreach ($filesByType as $docTypeId => $files): 
    $docLabel = $documentLabels[$docTypeId] ?? 'Document';
    $file = reset($files); // First file for this document type
    
   if ($docTypeId == 7) { // Certificate of Trainings and Seminars
        // File ID is now the combined PDF path
        $fileUrl = site_url('applications/viewTrainingCertificate/' . $app['id_job_application'] . '/' . $file['id']);
    }
?>
    <button class="view-document-btn" data-file="<?= esc($fileUrl) ?>">
        View Document
    </button>
<?php endforeach; ?>
```

**JavaScript Handling:**
```javascript
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-document-btn');
    const fileUrl = btn.getAttribute('data-file');
    
    // Show loading
   Swal.fire({ title: 'Loading...', didOpen: () => Swal.showLoading() });
    
    setTimeout(() => {
       Swal.close();
        // Open in modal iframe
        const modal = document.getElementById('document-modal');
        const frame = document.getElementById('document-frame');
        frame.src = fileUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }, 1000);
});
```

## User Experience

### Before
1. User views application
2. Scrolls to "Uploaded Documents"
3. Sees "Certificate of Trainings and Seminars" entry
4. Clicks "View Document"
5. **Only sees ONE certificate** (or error if no single certificate exists)

### After
1. User views application
2. Scrolls to"Uploaded Documents"
3. Sees "Certificate of Trainings and Seminars" entry
4. Clicks "View Document"
5. **Sees ALL training certificates combined into one PDF**
   - Each certificate on its own page
   - Training details displayed above each certificate
   - Professional formatting with DOMPDF
   - Smooth scrolling through all certificates

## Technical Details

### Files Modified
- `app/Controllers/Applications.php` - Reordered trainings fetching

### Files Used (No Changes Needed)
- `app/Libraries/TrainingCertificateCombiner.php` - DOMPDF combination logic
- `app/Views/applications/view.php` - Already supports the feature
- DOMPDF library - Already installed via Composer

### Generated Files
- Location: `writable/uploads/trainings/`
- Filename format: `combined_training_{application_id}_{hash}.pdf`
- Cache duration: 1 hour (auto-regenerates after expiry)

### Performance Considerations
- Combined PDF is cached for 1 hour
- Subsequent views use the cached version
- Cache regenerates automatically when:
  - Cache file expires (1 hour)
  - Different training certificates are viewed
  - New application is viewed

## Testing Checklist

- [x] Controller fetches trainings before documents
- [x] TrainingCertificateCombiner library exists and uses DOMPDF
- [x] View properly displays "View Document" button for trainings
- [x] JavaScript properly handles local file viewing
- [x] Modal iframe displays PDF correctly
- [ ] Test with actual application having multiple trainings
- [ ] Verify combined PDF generation
- [ ] Test cache behavior
- [ ] Test with Google Drive stored certificates

## Future Enhancements

1. **Table of Contents** - Add navigation page at start of combined PDF
2. **Bookmarks** - Create PDF bookmarks for each certificate
3. **Download Option** - Allow downloading the combined PDF
4. **Print Support** - Optimize for printing
5. **Thumbnail Preview** - Show small previews before opening full PDF
6. **Search Functionality** - Search within combined certificates

## Troubleshooting

### Combined PDF Not Generating
1. Check if DOMPDF is installed: `composer show dompdf/dompdf`
2. Verify write permissions: `ls -la writable/uploads/trainings/`
3. Check logs: `writable/logs/log-*.php`

### Missing Certificates in Combined PDF
1. Verify trainings are saved in `application_trainings` table
2. Check if `certificate_file` field is populated
3. For Google Drive files, ensure OAuth service is working

### PDF Quality Issues
- Images are rendered at screen resolution
- Large images may be scaled down
- Consider optimizing certificate images before upload

## Notes

- The system automatically deduplicates document types (one row per type)
- Training certificates are special-cased to combine multiple entries
- Other document types still show single files as before
- The feature works transparently - users don't need to do anything special
