<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Debug Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        .test-result { margin: 10px 0; padding: 10px; border-left: 4px solid #ccc; background: #f8f9fa; }
        img { max-width: 300px; border: 2px solid #ddd; margin-top: 10px; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; font-size: 12px; }
    </style>
</head>
<body>
    <h1>🔍 Profile Photo Debug Test - User 78</h1>
    
<?php
// Load environment variables
$env = parse_ini_file(__DIR__ . '/.env');

// Direct database connection
$db = mysqli_connect('localhost', 'root', '', 'hrmo');
if (!$db) {
    die('<p class="error">✗ Database connection failed: ' . mysqli_connect_error() . '</p>');
}

echo '<div class="test-result">';
echo '<h2>Step 1: Check Database</h2>';

$result = mysqli_query($db, "SELECT * FROM applicant_personal WHERE user_id = 78 LIMIT 1");
$profile = mysqli_fetch_assoc($result);

if ($profile) {
    echo '<p class="success">✓ Profile found for user 78</p>';
    echo '<p><strong>Photo value:</strong> ' . htmlspecialchars($profile['photo'] ?? 'NULL') . '</p>';
    
    $photoValue = $profile['photo'];
    
    // Check if it's a Google Drive file ID
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $photoValue) && !preg_match('/^\d{10}_/', $photoValue);
    
    if ($isGoogleDriveFile) {
        echo '<p class="info">ℹ Photo appears to be a Google Drive file ID</p>';
        echo '<p><strong>Format:</strong> Alphanumeric (20+ chars)</p>';
    } else {
        echo '<p class="info">ℹ Photo appears to be a local filename</p>';
        echo '<p><strong>Format:</strong> ' . (preg_match('/^\d{10}_/', $photoValue) ? 'Timestamp prefix' : 'Other format') . '</p>';
    }
} else {
    echo '<p class="error">✗ Profile not found for user 78</p>';
    exit(1);
}
mysqli_close($db);
echo '</div>';

if ($isGoogleDriveFile) {
    echo '<div class="test-result">';
    echo '<h2>Step 2: Test Google Drive Authentication</h2>';
    
    // Load the service account credentials directly
    $credentialsPath = __DIR__ . '/writable/credentials/google_credentials.json';
    
    if (!file_exists($credentialsPath)) {
        echo '<p class="error">✗ Credentials file not found at: ' . $credentialsPath . '</p>';
        exit(1);
    }
    
    echo '<p class="info">ℹ Credentials file found</p>';
    
    try {
        require_once __DIR__ . '/vendor/autoload.php';
        
        $client = new \Google\Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
        
        echo '<p class="success">✓ Google Client initialized</p>';
        
        // Get access token using service account assertion
        try {
            $accessToken = $client->fetchAccessTokenWithAssertion();
            
            if (isset($accessToken['access_token'])) {
                echo '<p class="success">✓ Access token obtained successfully</p>';
                
                echo '<h3>Step 3: Fetch File Metadata</h3>';
                
                $fileId = $photoValue;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?fields=id,name,mimeType,size,webContentLink&supportsAllDrives=true');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken['access_token']
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                echo '<p><strong>HTTP Code:</strong> ' . $httpCode . '</p>';
                
                if ($httpCode == 200) {
                    $fileData = json_decode($response, true);
                    echo '<p class="success">✓ File metadata retrieved successfully</p>';
                    echo '<pre>' . json_encode($fileData, JSON_PRETTY_PRINT) . '</pre>';
                    
                    echo '<h3>Step 4: Display Photo</h3>';
                    $photoUrl = 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?alt=media';
                    echo '<p><strong>Direct Download URL:</strong> <a href="' . $photoUrl . '" target="_blank">' . $photoUrl . '</a></p>';
                    echo '<img src="' . $photoUrl . '" alt="Profile Photo" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">';
                    echo '<div style="display:none; background:#fee; padding:10px; border:1px solid red;">Image failed to load - Check permissions or file existence in Google Drive</div>';
                    
                } else {
                    echo '<p class="error">✗ Failed to fetch file metadata</p>';
                    echo '<p><strong>Error Response:</strong></p>';
                    echo '<pre>' . htmlspecialchars($response) . '</pre>';
                }
            } else {
                echo '<p class="error">✗ Failed to get access token</p>';
            }
        } catch (\Exception $e) {
            echo '<p class="error">✗ Authentication error: ' . $e->getMessage() . '</p>';
        }
        
    } catch (\Exception $e) {
        echo '<p class="error">✗ Error loading Google Client: ' . $e->getMessage() . '</p>';
        echo '<p class="info">ℹ Make sure composer dependencies are installed: composer install</p>';
    }
    echo '</div>';
}
?>

    <hr>
    <p style="color: #6c757d; font-size: 14px;">
        <strong>Note:</strong> If the image doesn't load above, check:
        <ul>
            <li>Service account has access to the file</li>
            <li>File hasn't been deleted from Google Drive</li>
            <li>Folder permissions are correctly set</li>
        </ul>
    </p>
</body>
</html>
