<?php
// Comprehensive Google Drive API Test
echo "Comprehensive Google Drive API Test\n";
echo "===================================\n\n";

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
$credentials = json_decode(file_get_contents($credPath), true);
$folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? '';

echo "Configuration:\n";
echo "- Service Account: " . $credentials['client_email'] . "\n";
echo "- Folder ID: $folderId\n";
echo "- Credentials Path: $credPath\n\n";

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
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $tokenData = json_decode($response, true);
        return $tokenData['access_token'] ?? null;
    } else {
        throw new Exception("Token request failed: HTTP $httpCode, Response: $response, Curl Error: $curlError");
    }
}

try {
    echo "Step 1: Getting access token...\n";
    $accessToken = getAccessToken($credentials);
    echo "✓ Got access token\n\n";
    
    echo "Step 2: Testing general drive access...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/about");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✓ General drive access: OK\n";
    } else {
        echo "✗ General drive access failed: HTTP $httpCode, Error: $curlError, Response: $response\n";
    }
    
    echo "\nStep 3: Testing file listing...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files?pageSize=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        $fileCount = count($data['files'] ?? []);
        echo "✓ File listing: OK ($fileCount files accessible)\n";
    } else {
        echo "✗ File listing failed: HTTP $httpCode, Response: $response\n";
    }
    
    echo "\nStep 4: Testing folder access...\n";
    if (!empty($folderId)) {
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
            $data = json_decode($response, true);
            echo "✓ Folder access: OK (Name: " . $data['name'] . ")\n";
        } else {
            echo "✗ Folder access failed: HTTP $httpCode, Response: $response\n";
        }
    } else {
        echo "✗ No folder ID configured\n";
    }
    
    echo "\nStep 5: Testing file upload capability...\n";
    $testContent = "Test file created at " . date('Y-m-d H:i:s');
    $boundary = '----GoogleDriveUploadBoundary' . uniqid();
    $delimiter = "\r\n--" . $boundary . "\r\n";
    $closeDelimiter = "\r\n--" . $boundary . "--";
    
    $metadata = json_encode(['name' => 'test-upload-' . time() . '.txt']);
    
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
        echo "✓ Upload succeeded! File ID: " . $fileData['id'] . "\n";
        
        // Clean up the test file
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files/" . $fileData['id']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
        echo "✓ Test file cleaned up\n";
    } else {
        echo "✗ Upload failed: HTTP $httpCode, Response: $response\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "TEST COMPLETE\n";
    echo str_repeat("=", 50) . "\n";
    
} catch (Exception $e) {
    echo "Error occurred: " . $e->getMessage() . "\n";
}
?>