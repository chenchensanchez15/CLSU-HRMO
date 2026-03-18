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

echo "Testing OAuth Google Drive Integration\n";
echo "=====================================\n\n";

// Initialize Google Client
$client = new \Google\Client();
$client->setAuthConfig($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH']);
$client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->setAccessType('offline');
$client->setPrompt('consent');

// Check if we have an access token stored
$tokenPath = 'writable/temp/access_token.json';
if (file_exists($tokenPath)) {
    $token = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($token);
    
    // Refresh token if expired
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        } else {
            echo "Token expired and no refresh token available.\n";
            echo "Please re-authenticate by visiting: " . $client->createAuthUrl() . "\n";
            exit(1);
        }
    }
    
    echo "✓ Using stored access token\n";
} else {
    echo "No stored access token found.\n";
    echo "Please authenticate by visiting this URL:\n";
    echo $client->createAuthUrl() . "\n";
    exit(1);
}

echo "\nTesting Google Drive access...\n";

try {
    $service = new \Google\Service\Drive($client);
    
    // Test: List files to verify access
    $files = $service->files->listFiles([
        'pageSize' => 5,
        'fields' => 'files(id, name, mimeType)'
    ]);
    
    $fileList = $files->getFiles();
    echo "✓ Successfully connected to Google Drive\n";
    echo "✓ Found " . count($fileList) . " files\n";
    
    // Create a test file
    $testContent = "Test file created at " . date('Y-m-d H:i:s');
    $testFileName = 'oauth-test-' . time() . '.txt';
    
    file_put_contents($testFileName, $testContent);
    
    // Upload the test file
    $fileMetadata = new \Google\Service\Drive\DriveFile([
        'name' => $testFileName
    ]);
    
    $content = file_get_contents($testFileName);
    
    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => 'text/plain',
        'uploadType' => 'multipart'
    ]);
    
    echo "✓ File uploaded successfully! File ID: " . $file->getId() . "\n";
    
    // Set permissions to make it publicly accessible
    $permission = new \Google\Service\Drive\Permission([
        'role' => 'reader',
        'type' => 'anyone'
    ]);
    
    $service->permissions->create($file->getId(), $permission);
    echo "✓ Set public permissions\n";
    
    echo "✓ Public URL: https://drive.google.com/uc?id=" . $file->getId() . "\n";
    
    // Clean up - delete the test file
    $service->files->delete($file->getId());
    unlink($testFileName);
    echo "✓ Test file cleaned up\n";
    
    echo "\nOAuth Google Drive integration is working!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>