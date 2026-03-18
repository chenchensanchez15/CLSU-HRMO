<?php
// Test Google Drive Upload
require_once 'vendor/autoload.php';
require_once 'app/Services/SimpleGoogleDriveService.php';

use App\Services\SimpleGoogleDriveService;

echo "Testing Google Drive Upload\n\n";

try {
    $driveService = new SimpleGoogleDriveService();
    
    if ($driveService->isEnabled()) {
        echo "✓ Google Drive service is enabled\n";
        
        // Create a simple test file
        $testFile = 'test_upload.txt';
        $testContent = "This is a test file for Google Drive integration.\nUploaded at: " . date('Y-m-d H:i:s');
        file_put_contents($testFile, $testContent);
        
        // Upload to Google Drive
        $fileId = $driveService->uploadFile($testFile, 'test_upload_' . time() . '.txt', 'text/plain');
        
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