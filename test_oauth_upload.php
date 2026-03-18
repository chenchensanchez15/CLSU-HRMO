<?php
// Test script to verify Google Drive upload to specific folder
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

session_start();

echo "Google Drive Upload Test\n";
echo "========================\n\n";

// Check if we have an access token
if (!isset($_SESSION['google_access_token'])) {
    echo "❌ No Google access token found.\n";
    echo "❌ You must authenticate first before uploading.\n\n";
    
    // Provide the authentication URL
    $client = new \Google\Client();
    $client->setAuthConfig($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH']);
    $client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
    $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
    $client->setAccessType('offline');
    $client->setPrompt('consent');

    $authUrl = $client->createAuthUrl();
    echo "🔗 Authentication URL: " . $authUrl . "\n\n";
    
    echo "📋 Steps to upload files:\n";
    echo "   1. Visit the authentication URL above\n";
    echo "   2. Sign in with your Google account\n";
    echo "   3. Grant permissions to access Google Drive\n";
    echo "   4. After authentication, your access token will be stored\n";
    echo "   5. Then file uploads will work and go to folder: " . $_ENV['GOOGLE_DRIVE_FOLDER_ID'] . "\n";
    
    exit(0);
}

echo "✅ Google access token found.\n";

// Now test the OAuth service
require_once 'app/Libraries/GoogleDriveOAuthService.php';

use App\Libraries\GoogleDriveOAuthService;

try {
    $service = new GoogleDriveOAuthService();
    
    if ($service->isAuthenticated()) {
        echo "✅ User is authenticated and ready to upload.\n";
        echo "📁 Target folder ID: " . $_ENV['GOOGLE_DRIVE_FOLDER_ID'] . "\n\n";
        
        // Create a test file
        $testContent = "Test file uploaded at " . date('Y-m-d H:i:s') . " via OAuth integration.";
        $testFileName = 'test-upload-' . time() . '.txt';
        file_put_contents($testFileName, $testContent);
        
        echo "📤 Attempting to upload test file: $testFileName\n";
        
        try {
            // Upload the test file - it should go to the specified folder
            $fileId = $service->uploadFile($testFileName, $testFileName, 'text/plain');
            
            echo "✅ SUCCESS! File uploaded successfully.\n";
            echo "📄 File ID: $fileId\n";
            echo "🌐 Public URL: " . $service->getFileUrl($fileId) . "\n";
            echo "📂 Location: The file should be in folder " . $_ENV['GOOGLE_DRIVE_FOLDER_ID'] . "\n\n";
            
            // Clean up - delete the test file from Google Drive
            $service->deleteFile($fileId);
            echo "🧹 Test file deleted from Google Drive.\n";
            
            // Clean up local file
            unlink($testFileName);
            echo "🧹 Local test file deleted.\n\n";
            
            echo "🎉 OAuth Google Drive integration is working correctly!\n";
            echo "📁 Files will now upload to the specified folder: " . $_ENV['GOOGLE_DRIVE_FOLDER_ID'] . "\n";
            
        } catch (Exception $e) {
            echo "❌ Upload failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ User is not authenticated. Please authenticate first.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

session_write_close();
?>