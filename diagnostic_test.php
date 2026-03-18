<?php
// Diagnostic test for Google Drive folder access
echo "Google Drive Folder Access Diagnostic\n";
echo "=====================================\n\n";

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

// Load credentials
$credPath = $_ENV['GOOGLE_CREDENTIALS_PATH'] ?? 'writable/credentials/google_credentials.json';
if (!file_exists($credPath)) {
    die("Credentials file not found: $credPath\n");
}

$credentials = json_decode(file_get_contents($credPath), true);
$folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? '';

echo "Service Account: " . $credentials['client_email'] . "\n";
echo "Target Folder ID: '$folderId'\n\n";

// Get access token
function getAccessToken($credentials) {
    $now = time();
    $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    
    $jwtPayload = base64_encode(json_encode([
        'iss' => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/drive',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ]));
    
    $data = $jwtHeader . '.' . $jwtPayload;
    $privateKey = $credentials['private_key'];
    
    if (!openssl_sign($data, $signature, $privateKey, 'SHA256')) {
        throw new Exception('Failed to create signature');
    }
    
    $jwtSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    $jwt = $data . '.' . $jwtSignature;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $tokenData = json_decode($response, true);
        return $tokenData['access_token'] ?? null;
    } else {
        throw new Exception("Token request failed: $response");
    }
}

try {
    $accessToken = getAccessToken($credentials);
    echo "✓ Access token obtained successfully\n\n";
    
    // Test 1: Check if we can access the folder
    echo "Test 1: Checking folder access...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files/$folderId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $folderInfo = json_decode($response, true);
        echo "✓ Folder found: " . ($folderInfo['name'] ?? 'Unknown') . "\n";
        echo "  Folder type: " . ($folderInfo['mimeType'] ?? 'Unknown') . "\n";
    } else {
        echo "✗ Folder access failed. HTTP Code: $httpCode\n";
        echo "  Response: $response\n\n";
        
        if (strpos($response, 'notFound') !== false) {
            echo "Issue: Folder ID '$folderId' not found.\n";
            echo "Solution: Verify the folder ID is correct.\n\n";
        } elseif (strpos($response, 'permission') !== false) {
            echo "Issue: No permission to access folder.\n";
            echo "Solution: Share the folder with service account.\n\n";
        }
    }
    
    // Test 2: Try to list files in the folder
    echo "\nTest 2: Listing files in folder...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files?q='" . urlencode($folderId) . "' in parents");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $files = json_decode($response, true);
        echo "✓ Can list files in folder\n";
        echo "  Files found: " . count($files['files'] ?? []) . "\n";
    } else {
        echo "✗ Cannot list files. HTTP Code: $httpCode\n";
        echo "  Response: $response\n";
    }
    
    // Test 3: Try basic upload without folder
    echo "\nTest 3: Testing basic upload (no folder)...\n";
    $testContent = "Test upload at " . date('Y-m-d H:i:s');
    $boundary = '----GoogleDriveUploadBoundary' . uniqid();
    $delimiter = "\r\n--" . $boundary . "\r\n";
    $closeDelimiter = "\r\n--" . $boundary . "--";
    
    $metadata = json_encode(['name' => 'test-file-' . time() . '.txt']);
    
    $body = $delimiter;
    $body .= 'Content-Type: application/json; charset=UTF-8' . "\r\n\r\n";
    $body .= $metadata . $delimiter;
    $body .= 'Content-Type: text/plain' . "\r\n\r\n";
    $body .= $testContent;
    $body .= $closeDelimiter;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: multipart/related; boundary=' . $boundary
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $fileData = json_decode($response, true);
        $fileId = $fileData['id'];
        echo "✓ Basic upload successful! File ID: $fileId\n";
        
        // Clean up - delete the test file
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files/$fileId");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
        echo "  Test file cleaned up\n";
    } else {
        echo "✗ Basic upload failed. HTTP Code: $httpCode\n";
        echo "  Response: $response\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "DIAGNOSTIC COMPLETE\n";
echo str_repeat("=", 50) . "\n";
?>