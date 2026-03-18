<?php
// Simple test for the fixed Google Drive service
echo "Testing Fixed Google Drive Service\n";
echo "==================================\n\n";

// Temporarily override the folder ID to test without it
$_ENV['GOOGLE_DRIVE_FOLDER_ID'] = '';
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

class SimpleGoogleDriveService {
    private $enabled = false;
    private $credentials = null;
    private $accessToken = null;
    
    public function __construct() {
        // Check if credentials file exists
        $credentialsPath = 'writable/credentials/google_credentials.json';
        if (file_exists($credentialsPath)) {
            $this->credentials = json_decode(file_get_contents($credentialsPath), true);
            if ($this->credentials) {
                $this->enabled = true;
                $this->accessToken = $this->getAccessToken();
            }
        } else {
            echo "Credentials file not found at: $credentialsPath\n";
        }
    }

    public function isEnabled(): bool {
        return $this->enabled && $this->accessToken !== null;
    }

    private function getAccessToken() {
        if (!$this->credentials) {
            return null;
        }

        try {
            $now = time();
            $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            
            $jwtPayload = base64_encode(json_encode([
                'iss' => $this->credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/drive',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now
            ]));
            
            $data = $jwtHeader . '.' . $jwtPayload;
            $privateKey = $this->credentials['private_key'];
            
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
                echo "Token request failed. HTTP Code: $httpCode\n";
                echo "Response: $response\n";
            }
            
            return null;
        } catch (Exception $e) {
            echo 'Failed to get access token: ' . $e->getMessage() . "\n";
            return null;
        }
    }

    public function uploadFile($filePath, $fileName, $mimeType = 'application/pdf') {
        if (!$this->isEnabled()) {
            throw new Exception('Google Drive service not enabled. Check credentials and access token.');
        }
    
        try {
            $fileContent = file_get_contents($filePath);
                
            $boundary = '----GoogleDriveUploadBoundary' . uniqid();
            $delimiter = "\r\n--" . $boundary . "\r\n";
            $closeDelimiter = "\r\n--" . $boundary . "--";
                
            // Upload without specifying a parent folder first to test basic upload
            $metadata = json_encode([
                'name' => $fileName
            ]);
                
            $body = $delimiter;
            $body .= 'Content-Type: application/json; charset=UTF-8' . "\r\n\r\n";
            $body .= $metadata . $delimiter;
            $body .= 'Content-Type: ' . $mimeType . "\r\n\r\n";
            $body .= $fileContent;
            $body .= $closeDelimiter;
                
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: multipart/related; boundary=' . $boundary
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
                
            if ($httpCode == 200) {
                $fileData = json_decode($response, true);
                $fileId = $fileData['id'];
                    
                // Make the file publicly readable
                $this->makeFilePublic($fileId);
                    
                return $fileId;
            } else {
                throw new Exception('Upload failed with HTTP code: ' . $httpCode . '. Response: ' . $response);
            }
        } catch (Exception $e) {
            throw new Exception('Google Drive upload failed: ' . $e->getMessage());
        }
    }

    private function makeFilePublic($fileId) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId . '/permissions');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'role' => 'reader',
            'type' => 'anyone'
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        curl_exec($ch);
        curl_close($ch);
    }

    public function getFileUrl($fileId) {
        return "https://drive.google.com/uc?id={$fileId}";
    }
}

// Test the service
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