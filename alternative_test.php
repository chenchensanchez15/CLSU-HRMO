<?php
// Alternative test - check folder accessibility
echo "Google Drive Alternative Access Test\n";
echo "====================================\n\n";

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

echo "Service Account: " . $credentials['client_email'] . "\n";
echo "Folder ID: $folderId\n\n";

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
    echo "✓ Access token: OK\n\n";
    
    // Test 1: Try to list all files the service account can access
    echo "Test 1: Listing accessible files...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/files?pageSize=5");
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
        echo "✓ Service account can access files\n";
        echo "  Files found: " . count($files['files'] ?? []) . "\n";
        foreach ($files['files'] ?? [] as $file) {
            echo "  - " . $file['name'] . " (" . $file['mimeType'] . ")\n";
        }
    } else {
        echo "✗ Cannot list files: $response\n";
    }
    
    echo "\nTest 2: Direct folder access test...\n";
    // Try with different API endpoints
    $endpoints = [
        "https://www.googleapis.com/drive/v3/files/$folderId",
        "https://www.googleapis.com/drive/v2/files/$folderId",
    ];
    
    foreach ($endpoints as $endpoint) {
        echo "Testing: $endpoint\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "  HTTP Code: $httpCode\n";
        if ($httpCode != 200) {
            echo "  Error: " . substr($response, 0, 200) . "...\n";
        } else {
            $data = json_decode($response, true);
            echo "  Success: " . ($data['name'] ?? 'Unknown') . "\n";
        }
        echo "\n";
    }
    
    echo "Test 3: Permissions check...\n";
    // Check what permissions the service account has
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/drive/v3/about?fields=user");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $about = json_decode($response, true);
        echo "✓ Authenticated as: " . ($about['user']['emailAddress'] ?? 'Unknown') . "\n";
    } else {
        echo "✗ Auth failed: $response\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "TEST COMPLETE\n";
echo str_repeat("=", 40) . "\n";
?>