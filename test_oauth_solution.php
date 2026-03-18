<?php
// Test OAuth Google Drive upload
require_once 'vendor/autoload.php';

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Initialize session for testing
session_start();

echo "Testing OAuth Google Drive Integration\n";
echo "=====================================\n\n";

// Check if we have an access token stored
$token = $_SESSION['google_access_token'] ?? null;
if (!$token) {
    echo "No stored access token found.\n";
    echo "Please authenticate first by visiting your Google auth endpoint.\n";
    echo "Example: http://localhost/HRMO/public/index.php/google/redirectToGoogle\n";
    echo "Or use the OAuth client directly:\n\n";
    
    // Initialize Google Client for initial auth
    $client = new \Google\Client();
    $client->setAuthConfig($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH']);
    $client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
    $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
    $client->setAccessType('offline');
    $client->setPrompt('consent');

    $authUrl = $client->createAuthUrl();
    echo "Authentication URL: " . $authUrl . "\n";
    exit(1);
}

echo "✓ Using stored access token\n";

// Now test the OAuth service
require_once 'app/Libraries/GoogleDriveOAuthService.php';

use App\Libraries\GoogleDriveOAuthService;

try {
    $service = new GoogleDriveOAuthService();
    
    if ($service->isAuthenticated()) {
        echo "✓ User is authenticated\n";
        
        // Create a test file
        $testContent = "Test file created at " . date('Y-m-d H:i:s');
        $testFileName = 'oauth-test-' . time() . '.txt';
        file_put_contents($testFileName, $testContent);
        
        echo "Attempting to upload test file...\n";
        
        // Upload the test file to the specified folder
        $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? null;
        $fileId = $service->uploadFile($testFileName, $testFileName, 'text/plain', $folderId);
        
        echo "✓ File uploaded successfully! File ID: " . $fileId . "\n";
        echo "✓ Public URL: " . $service->getFileUrl($fileId) . "\n";
        
        // Clean up - delete the test file
        $service->deleteFile($fileId);
        unlink($testFileName);
        echo "✓ Test file cleaned up\n";
        
        echo "\nOAuth Google Drive integration is working!\n";
        echo "The storage quota error is resolved because we're using user Drive, not service account.\n";
    } else {
        echo "✗ User is not authenticated. Please authenticate first.\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

session_write_close();
?>