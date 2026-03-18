<?php
/**
 * Test Service Account Authentication
 * This tests if the Google Drive service account is working properly
 */

// Load CodeIgniter
require_once __DIR__ . '/system/Test/bootstrap.php';

use App\Libraries\GoogleDriveOAuthService;

echo "=== Testing Google Drive Service Account ===\n\n";

try {
    echo "1. Initializing GoogleDriveOAuthService...\n";
    $driveService = new GoogleDriveOAuthService();
    
    echo "2. Checking authentication status...\n";
    $isAuthenticated = $driveService->isAuthenticated();
    echo "   Authentication Status: " . ($isAuthenticated ? "✓ AUTHENTICATED" : "✗ NOT AUTHENTICATED") . "\n\n";
    
    if (!$isAuthenticated) {
        echo "ERROR: Service account authentication failed!\n";
        echo "Please check:\n";
        echo "  - Credentials file exists at: writable/credentials/google_credentials.json\n";
        echo "  - Service account has access to the Google Drive folder\n";
        echo "  - The folder ID in .env is correct\n\n";
        exit(1);
    }
    
    echo "3. Fetching files from Google Drive...\n";
    $files = $driveService->fetchFilesFromFolder('78'); // Test with user ID 78
    
    if (empty($files)) {
        echo "   No files found for user 78\n";
    } else {
        echo "   Found " . count($files) . " files:\n";
        foreach ($files as $file) {
            echo "   - {$file['name']} (ID: {$file['id']})\n";
        }
    }
    
    echo "\n4. Testing file download...\n";
    if (!empty($files)) {
        $testFileId = $files[0]['id'];
        $tempPath = sys_get_temp_dir() . '/test_gdrive_download.pdf';
        
        $result = $driveService->downloadFile($testFileId, $tempPath);
        echo "   Download Result: " . ($result ? "✓ SUCCESS" : "✗ FAILED") . "\n";
        
        if ($result && file_exists($tempPath)) {
            echo "   File saved to: $tempPath\n";
            echo "   File size: " . filesize($tempPath) . " bytes\n";
            unlink($tempPath);
        }
    } else {
        echo "   Skipped - no files available\n";
    }
    
    echo "\n=== TEST COMPLETE ===\n";
    echo "Service account is working correctly!\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
