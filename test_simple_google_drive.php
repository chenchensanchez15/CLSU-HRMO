<?php
// Test Simple Google Drive Service
require_once 'vendor/autoload.php';

// Test the simplified Google Drive service
require_once 'app/Services/SimpleGoogleDriveService.php';

echo "Testing Simple Google Drive Service\n\n";

try {
    $driveService = new App\Services\SimpleGoogleDriveService();
    
    if ($driveService->isEnabled()) {
        echo "✓ Google Drive service is enabled\n";
        echo "✓ Service is ready for use\n";
    } else {
        echo "✗ Google Drive service is not enabled\n";
        echo "Please check if the credentials file exists at: " . realpath(__DIR__ . '/writable/credentials/google_credentials.json') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}