<?php
// Test the fixed Google Drive service
require_once 'app/Config/Paths.php';
require_once 'app/Config/Autoload.php';

// Load environment variables
if (file_exists('.env')) {
    $env = parse_ini_file('.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Include the service
require_once 'app/Services/SimpleGoogleDriveService.php';

use App\Services\SimpleGoogleDriveService;

echo "Testing Fixed Google Drive Service\n";
echo "==================================\n\n";

try {
    $service = new SimpleGoogleDriveService();
    
    echo "Service enabled: " . ($service->isEnabled() ? "YES" : "NO") . "\n";
    
    if (!$service->isEnabled()) {
        echo "Service is not enabled. Check credentials.\n";
        exit(1);
    }
    
    // Create a test file
    $testFile = 'test_upload.txt';
    $testContent = "This is a test file uploaded at " . date('Y-m-d H:i:s');
    file_put_contents($testFile, $testContent);
    
    echo "Uploading test file...\n";
    $fileId = $service->uploadFile($testFile, 'test-file-' . time() . '.txt', 'text/plain');
    
    echo "Upload successful! File ID: $fileId\n";
    echo "Public URL: " . $service->getFileUrl($fileId) . "\n";
    
    // Clean up
    unlink($testFile);
    
    echo "\nTest completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Show detailed error information
    echo "\nDebug Information:\n";
    echo "Folder ID: " . ($_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? 'Not set') . "\n";
    echo "Credentials file exists: " . (file_exists('writable/credentials/google_credentials.json') ? 'YES' : 'NO') . "\n";
}