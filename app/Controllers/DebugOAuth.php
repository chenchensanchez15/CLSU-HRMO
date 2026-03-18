<?php

namespace App\Controllers;

use App\Libraries\GoogleDriveOAuthService;

class DebugOAuth extends BaseController
{
    public function index()
    {
        echo "<pre>";
        echo "===========================================\n";
        echo "Google Drive OAuth Debug Test\n";
        echo "===========================================\n\n";
        
        try {
            // Initialize service
            echo "1. Initializing GoogleDriveOAuthService...\n";
            $service = new GoogleDriveOAuthService();
            echo "   ✓ Service initialized\n\n";
            
            // Check authentication
            echo "2. Checking authentication status...\n";
            if ($service->isAuthenticated()) {
                echo "   ✓ User is authenticated\n\n";
            } else {
                echo "   ✗ User is NOT authenticated\n";
                echo "   This means no valid access token is stored in session.\n\n";
                
                // Try to get auth URL
                echo "3. Getting authorization URL...\n";
                $client = $service->getClient();
                $authUrl = $client->createAuthUrl();
                echo "   Authorization URL: <a href='$authUrl'>Click here to authenticate</a>\n\n";
                exit;
            }
            
            // Get client and check token details
            echo "3. Checking access token details...\n";
            $client = $service->getClient();
            $token = $client->getAccessToken();
            echo "   Token data: " . json_encode($token, JSON_PRETTY_PRINT) . "\n\n";
            
            // Check if token is expired
            if ($client->isAccessTokenExpired()) {
                echo "   ⚠ Access token is EXPIRED\n";
                if ($client->getRefreshToken()) {
                    echo "   ✓ Refresh token exists, attempting refresh...\n";
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    echo "   ✓ Token refreshed successfully\n\n";
                } else {
                    echo "   ✗ No refresh token available. Re-authentication required.\n\n";
                    exit;
                }
            } else {
                echo "   ✓ Access token is valid\n\n";
            }
            
            // Test file upload
            echo "4. Testing file upload...\n";
            
            // Create a test file
            $testFile = WRITEPATH . 'temp/test_oauth_upload.txt';
            $testContent = "Test file for Google Drive OAuth upload.\nCreated at: " . date('Y-m-d H:i:s');
            file_put_contents($testFile, $testContent);
            echo "   ✓ Created test file: $testFile\n";
            
            // Check environment variables
            echo "\n5. Checking environment configuration...\n";
            $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? null;
            echo "   Configured Folder ID: " . ($folderId ?: 'NOT SET') . "\n";
            echo "   Current target: https://drive.google.com/drive/folders/0AP4MLcqJJB2aUk9PVA\n\n";
            
            if (!$folderId) {
                echo "   ⚠ WARNING: GOOGLE_DRIVE_FOLDER_ID is not set in .env file!\n";
                echo "   Files will be uploaded to My Drive root instead of the shared folder.\n\n";
            } else {
                // Check if it's a Shared Drive ID or regular folder
                echo "   📁 Folder Type Analysis:\n";
                if (strlen($folderId) < 30) {
                    echo "   - This appears to be a SHARED DRIVE ID (short format)\n";
                    echo "   - ⚠ You should create a folder INSIDE the Shared Drive and use that folder's ID instead\n";
                    echo "   - Shared Drive ID: $folderId\n";
                    echo "   - Example proper folder ID format: 1ABCxyz123... (33+ characters)\n\n";
                } else {
                    echo "   - This appears to be a regular folder ID (standard format)\n";
                    echo "   - Length: " . strlen($folderId) . " characters ✓\n\n";
                }
            }
            
            // Attempt upload
            echo "6. Uploading file to Google Drive...\n";
            $fileName = 'test_upload_' . time() . '.txt';
            $fileId = $service->uploadFile($testFile, $fileName, 'text/plain');
            
            echo "   ✓ File uploaded successfully!\n";
            echo "   File ID: $fileId\n";
            echo "   File URL: " . $service->getFileUrl($fileId) . "\n\n";
            
            // Verify file location
            echo "7. Verifying file location...\n";
            $accessToken = $client->getAccessToken()['access_token'];
            
            // Get file metadata to check parents
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?fields=id,name,parents,webViewLink&supportsAllDrives=true');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200) {
                $fileData = json_decode($response, true);
                echo "   File metadata:\n";
                echo "   - Name: " . $fileData['name'] . "\n";
                echo "   - Parents: " . (isset($fileData['parents']) ? print_r($fileData['parents'], true) : 'None (root folder)') . "\n";
                echo "   - View Link: " . $fileData['webViewLink'] . "\n";
                
                if (isset($fileData['parents']) && in_array($folderId, $fileData['parents'])) {
                    echo "   ✓ File is in the CORRECT folder!\n";
                } else if ($folderId) {
                    echo "   ✗ File is NOT in the expected folder!\n";
                    echo "   Expected parent: $folderId\n";
                    
                    // Try to add file to folder
                    echo "\n8. Attempting to move file to correct folder...\n";
                    $this->moveFileToFolder($fileId, $folderId, $accessToken);
                }
            } else {
                echo "   ✗ Failed to verify file location (HTTP $httpCode)\n";
                echo "   Response: $response\n";
            }
            
            // Clean up
            unlink($testFile);
            echo "\n9. Cleaning up test file...\n";
            echo "   ✓ Local test file deleted\n\n";
            
            echo "===========================================\n";
            echo "TEST COMPLETE\n";
            echo "===========================================\n";
            
        } catch (\Exception $e) {
            echo "\n✗ ERROR: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
        echo "</pre>";
    }
    
    private function moveFileToFolder($fileId, $folderId, $accessToken)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'addParents' => $folderId,
            'removeParents' => 'root'
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            echo "   ✓ File moved to correct folder!\n";
        } else {
            echo "   ✗ Failed to move file (HTTP $httpCode)\n";
            echo "   Response: $response\n";
        }
    }
}
