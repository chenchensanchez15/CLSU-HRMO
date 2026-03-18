# Training Certificates Multi-Document Viewer

## Overview
When viewing an application in the Application View, clicking"View All" on the "Certificate of Trainings and Seminars" entry opens a modal that displays **ALL actual training certificate files** one at a time. Users can navigate through certificates using Previous/Next buttons - **NO combining or creating new PDFs**.

## Implementation Summary

### What Was Implemented
A complete system to display multiple training certificate files sequentially in a dedicated modal with navigation controls. The solution:
- Shows actual certificate files (Google Drive or local storage)
- No PDF combination or file manipulation
- User-friendly navigation between certificates
- Displays training information for each certificate

### Files Created
1. **`app/Controllers/TrainingDocuments.php`** - New controller for handling multiple training certificates
2. **Routes added to `app/Config/Routes.php`**:
   - `training-documents/view-multiple/(:num)` - Fetches list of certificates
   - `training-documents/get-certificate/(:any)` - Serves individual certificate file

### Files Modified
1. **`app/Views/applications/view.php`**:
   - Added new "Multiple Training Certificates Modal" with navigation
   - Updated button display for document_type_id = 7 (trainings)
   - Added JavaScript handlers for viewing all certificates

## How It Works

### User Flow
```
1. Admin views application
   ↓
2. Scrolls to "Uploaded Documents" section
   ↓
3. Sees"Certificate of Trainings and Seminars" row
   ↓
4. Clicks green "View All (X)" button(where X = number of trainings)
   ↓
5. Modal opens showing first certificate
   ↓
6. Can navigate: Previous | Next buttons
   ↓
7. Sees training name and dates for each certificate
   ↓
8. Closes modal when done
```

### Technical Flow

#### 1. Controller (`TrainingDocuments.php`)

**Endpoint 1: `viewMultiple($application_id)`**
```php
// Fetches all trainings for the application
$trainings = $db->table('application_trainings')
    ->where('job_application_id', $application_id)
    ->get()->getResultArray();

// Collects certificate files (actual files, NOT combined)
$certificateFiles = [];
foreach ($trainings as $training) {
   if (!empty($training['certificate_file'])) {
        $certificateFiles[] = [
            'file' => $training['certificate_file'],
            'training_name' => $training['training_name'],
            'date_from' => date('F d, Y', strtotime($training['date_from'])),
            'date_to' => date('F d, Y', strtotime($training['date_to'])),
        ];
    }
}

// Returns JSON response
return json_encode([
    'status' => 'success',
    'certificates' => $certificateFiles,
    'count' => count($certificateFiles)
]);
```

**Endpoint 2: `getCertificate($filename)`**
```php
// Checks if Google Drive file or local file
if (isGoogleDriveFile($filename)) {
    // Download from Google Drive and serve
    $driveService->downloadFile($filename);
} else {
    // Serve local file from writable/uploads/trainings/
   readfile(WRITEPATH . 'uploads/trainings/' . $filename);
}
```

#### 2. View Changes (`applications/view.php`)

**Button Display Logic:**
```php
<?php if ($docTypeId == 7 && !empty($trainings)): ?>
    <!-- Special "View All" button for trainings -->
    <button class="view-all-trainings-btn bg-clsuGreen text-white" 
            data-application-id="<?= $app['id_job_application'] ?>">
        View All (<?= count($trainings) ?>)
    </button>
<?php else: ?>
    <!-- Regular "View Document" button for other documents -->
    <button class="view-document-btn text-blue-600">
        View Document
    </button>
<?php endif; ?>
```

**JavaScript Handler:**
```javascript
document.addEventListener('click', async function(e) {
   const btn = e.target.closest('.view-all-trainings-btn');
    
   if (btn) {
       const applicationId = btn.getAttribute('data-application-id');
        
        // Fetch list of certificates
       const response = await fetch(`/training-documents/view-multiple/${applicationId}`);
       const data = await response.json();
        
       if (data.status === 'success') {
            trainingCertificates = data.certificates;
            currentCertIndex = 0;
            
            // Update modal UI
            updateTrainingCertificateViewer();
            
            // Show modal
            document.getElementById('multiple-training-modal')
                .classList.remove('hidden');
        }
    }
});

function updateTrainingCertificateViewer() {
   const cert = trainingCertificates[currentCertIndex];
    
    // Update info bar
    document.getElementById('cert-training-name').textContent = cert.training_name;
    document.getElementById('cert-dates').textContent = `${cert.date_from} to ${cert.date_to}`;
    
    // Load certificate in iframe
   if (isGoogleDriveFile(cert.file)) {
        frame.src = `https://drive.google.com/file/d/${cert.file}/preview`;
    } else {
        frame.src = `/training-documents/get-certificate/${cert.file}`;
    }
    
    // Update navigation buttons
    document.getElementById('prev-cert-btn').disabled = (currentCertIndex === 0);
    document.getElementById('next-cert-btn').disabled = (currentCertIndex === trainingCertificates.length - 1);
}
```

## Modal Structure

The "Multiple Training Certificates Modal" includes:

### Header Section
- Title: "Training Certificates"
- Counter: "Certificate 1 of 5"
- Navigation buttons: Previous | Next | Close

### Info Bar
- Training Name: "Basic Occupational Safety and Health"
- Dates: "January 15, 2024 to January 17, 2024"

### Content Area
- Full-screen iframe displaying the actual certificate
- Supports PDF, images, Google Drive preview

## Features

### ✅ What Works
- Displays all training certificate files sequentially
- Navigation between certificates (Previous/Next)
- Shows training details for each certificate
- Handles both Google Drive and local files
- Responsive modal design
- Keyboard support (ESC to close)
- Click outside to close

### ❌ What Was NOT Done
- NO PDF combination(DOMPDF not used)
- NO creation of new files
- NO modification of existing certificate files
- NO caching of combined documents

## Testing Checklist

- [ ] Verify "View All" button appears for trainings
- [ ] Check button shows correct count (e.g., "View All (5)")
- [ ] Modal opens with first certificate
- [ ] Training name and dates display correctly
- [ ] Previous button disabled on first certificate
- [ ] Next button disabled on last certificate
- [ ] Navigation works smoothly
- [ ] Google Drive certificates load properly
- [ ] Local certificates load properly
- [ ] Close button works
- [ ] ESC key closes modal
- [ ] Click outside closes modal

## Troubleshooting

### "View All" button doesn't appear
- Check if `$trainings` array is populated in the view
- Verify document_type_id = 7 in database
- Ensure trainings exist for the application

### Modal shows "No Certificates Found"
- Check if trainings have `certificate_file` field populated
- Verify Google Drive OAuth is working (for Drive files)
- Check file paths for local certificates

### Certificate doesn't load in iframe
- For Google Drive: Check sharing permissions
- For local files: Verify file exists in `writable/uploads/trainings/`
- Check browser console for errors

### Navigation buttons don't work
- Verify JavaScript is loaded
- Check console for errors
- Ensure modal elements exist before attaching events

## Future Enhancements

1. **Thumbnail Preview** - Show small previews at bottom
2. **Download Button** - Allow downloading individual certificates
3. **Print Support** - Print current certificate
4. **Zoom Controls** - Zoom in/out for better viewing
5. **Fullscreen Mode** - Expand modal to full screen
6. **Keyboard Shortcuts** - Arrow keys for navigation
7. **Loading Indicator** - Show progress while loading large files

## Notes

- Each certificate is displayed in its original format
- No files are modified or combined
- Google Drive files use iframe preview
- Local files are served directly
- Modal resets when closed (starts at first certificate next time)
- Works with any number of training certificates (1 to many)
