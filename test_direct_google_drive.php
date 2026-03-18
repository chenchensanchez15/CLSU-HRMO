<?php
// Direct Google Drive Test
require_once 'vendor/autoload.php';
require_once 'app/Services/SimpleGoogleDriveService.php';

use App\Services\SimpleGoogleDriveService;

echo "Testing Google Drive Direct Upload\n\n";

try {
    $driveService = new SimpleGoogleDriveService();
    
    if ($driveService->isEnabled()) {
        echo "✓ Google Drive service is enabled\n";
        
        // Create a simple test PDF file
        $testFile = 'test_document.pdf';
        $testContent = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj
2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj
3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
>>
endobj
4 0 obj
<<
/Length 44
>>
stream
BT
/F1 12 Tf
72 720 Td
(Test Document for Google Drive) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f 
0000000010 00000 n 
0000000053 00000 n 
0000000120 00000 n 
0000000227 00000 n 
trailer
<<
/Size 5
/Root 1 0 R
>>
startxref
345
%%EOF";
        
        file_put_contents($testFile, $testContent);
        
        echo "✓ Created test PDF file\n";
        
        // Upload to Google Drive
        $fileId = $driveService->uploadFile($testFile, 'test_document_' . time() . '.pdf', 'application/pdf');
        
        echo "✓ File uploaded successfully!\n";
        echo "File ID: $fileId\n";
        echo "Public URL: " . $driveService->getFileUrl($fileId) . "\n";
        
        // Clean up local test file
        unlink($testFile);
        
    } else {
        echo "✗ Google Drive service is not enabled\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}