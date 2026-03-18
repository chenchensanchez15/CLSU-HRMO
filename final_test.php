<?php
// Final test to check Google Drive access
echo "Final Google Drive Access Test\n";
echo "==============================\n\n";

// Load environment
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$credPath = $_ENV['GOOGLE_CREDENTIALS_PATH'] ?? 'writable/credentials/google_credentials.json';
$folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? '';

echo "Current Configuration:\n";
echo "- Credentials file: $credPath\n";
echo "- Folder ID: $folderId\n";
echo "- File exists: " . (file_exists($credPath) ? 'YES' : 'NO') . "\n";

if (file_exists($credPath)) {
    $credentials = json_decode(file_get_contents($credPath), true);
    echo "- Service Account: " . ($credentials['client_email'] ?? 'NOT FOUND') . "\n";
    echo "- Type: " . ($credentials['type'] ?? 'NOT FOUND') . "\n";
}

echo "\n---\n\n";

// Include the service and test
require_once 'app/Services/SimpleGoogleDriveService.php';

use App\Services\SimpleGoogleDriveService;

try {
    echo "Creating Google Drive service...\n";
    $service = new SimpleGoogleDriveService();
    
    echo "Service enabled: " . ($service->isEnabled() ? 'YES' : 'NO') . "\n";
    
    if ($service->isEnabled()) {
        // Try to upload a small test file
        $testFile = 'temp_test_file.txt';
        file_put_contents($testFile, 'Test upload at ' . date('Y-m-d H:i:s'));
        
        echo "Attempting upload to folder...\n";
        $fileId = $service->uploadFile($testFile, 'temp-test-file-' . time() . '.txt');
        
        echo "SUCCESS! File uploaded with ID: $fileId\n";
        echo "Public URL: " . $service->getFileUrl($fileId) . "\n";
        
        // Clean up
        unlink($testFile);
        
        // Also try to delete the uploaded file
        $service->deleteFile($fileId);
        echo "Uploaded file cleaned up\n";
        
    } else {
        echo "Service is not enabled. Check credentials and access token.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    
    // Additional debugging
    if (strpos($e->getMessage(), 'storageQuotaExceeded') !== false) {
        echo "\nThis error means the service account doesn't have access to upload anywhere.\n";
        echo "The folder must be properly shared with the service account.\n";
    }
}

echo "\nTest completed.\n";
?>