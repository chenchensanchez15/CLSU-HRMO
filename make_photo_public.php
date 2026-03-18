<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Photo Public</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
    </style>
</head>
<body>
    <h1>🔓 Make Profile Photo Public - User 78</h1>
    
<?php
require_once __DIR__ . '/vendor/autoload.php';

$credentialsPath = __DIR__ . '/writable/credentials/google_credentials.json';
$fileId = '1XeWDHRmU1mLAxJ7Zbyqz3jPTd1YgyrBs'; // The photo file ID from database

echo '<div style="margin: 20px 0; padding: 10px; border-left: 4px solid #ccc; background: #f8f9fa;">';
echo '<h2>Setting Public Permissions for Photo</h2>';

try {
    $client = new \Google\Client();
    $client->setAuthConfig($credentialsPath);
    $client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
    
    echo '<p class="success">✓ Google Client initialized</p>';
    
    $accessToken = $client->fetchAccessTokenWithAssertion();
    
    if (isset($accessToken['access_token'])) {
        echo '<p class="success">✓ Access token obtained</p>';
        
        // Set permissions to make file publicly accessible
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId . '/permissions?supportsAllDrives=true');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'type' => 'anyone',
            'role' => 'reader'
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken['access_token'],
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo '<p><strong>HTTP Code:</strong> ' . $httpCode . '</p>';
        
        if ($httpCode == 200) {
            echo '<p class="success">✓ File permissions set to PUBLIC successfully!</p>';
            echo '<p class="info">ℹ The photo should now be accessible without authentication</p>';
            
            // Test the public URL
            $publicUrl = 'https://drive.google.com/uc?id=' . $fileId . '&export=view';
            $apiUrl = 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?alt=media';
            
            echo '<h3>Test URLs:</h3>';
            echo '<p><strong>Google Drive Viewer:</strong> <a href="' . $publicUrl . '" target="_blank">' . $publicUrl . '</a></p>';
            echo '<p><strong>Direct API Download:</strong> <a href="' . $apiUrl . '" target="_blank">' . $apiUrl . '</a></p>';
            
            echo '<img src="' . $apiUrl . '" alt="Profile Photo" style="max-width: 300px; border: 2px solid #ddd; margin-top: 10px;" onerror="this.style.background=\'red\'; this.alt=\'Failed to load\';">';
            
        } else {
            echo '<p class="error">✗ Failed to set permissions</p>';
            echo '<pre>' . htmlspecialchars($response) . '</pre>';
        }
    } else {
        echo '<p class="error">✗ Failed to get access token</p>';
    }
    
} catch (\Exception $e) {
    echo '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
}

echo '</div>';
?>

    <hr>
    <p style="color: #6c757d; font-size: 14px;">
        After running this, refresh your profile page and the photo should appear!
    </p>
</body>
</html>
