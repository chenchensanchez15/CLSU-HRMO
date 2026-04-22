<?php

namespace App\Libraries;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class GoogleDriveOAuthService
{
    protected $client;
    protected $driveService;
    protected $isAuthenticated = false;

    public function __construct()
    {
        $this->client = new Client();
        
        // Use OAuth Web Client (user delegation) - uploads use user's quota
        $credentialsPath = $_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? WRITEPATH . 'credentials/oauth_credential.json';
        
        log_message('debug', 'Google Drive: Looking for credentials at: ' . $credentialsPath);
        log_message('debug', 'Google Drive: File exists? ' . (file_exists($credentialsPath) ? 'YES' : 'NO'));
        
        if (file_exists($credentialsPath)) {
            try {
                // Load OAuth web client credentials
                $this->client->setAuthConfig($credentialsPath);
                log_message('debug', 'Google Drive: OAuth credentials loaded');
                
                // Set redirect URI
                $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost:8080/HRMO/google/callback';
                $this->client->setRedirectUri($redirectUri);
                
                // IMPORTANT: Set access type to offline to get refresh token
                $this->client->setAccessType('offline');
                $this->client->setPrompt('consent');
                
                // Set required scopes for file upload
                $scopes = [
                    'https://www.googleapis.com/auth/drive.file',
                    'https://www.googleapis.com/auth/drive.appdata'
                ];
                $this->client->addScope($scopes);
                log_message('debug', 'Google Drive: Scopes added: ' . implode(', ', $scopes));
                
                // Set application name
                $this->client->setApplicationName('HRMO Document System');
                
                // Try to load existing token
                $tokenPath = WRITEPATH . 'google-token.json';
                if (file_exists($tokenPath)) {
                    try {
                        $token = json_decode(file_get_contents($tokenPath), true);
                        $this->client->setAccessToken($token);
                        log_message('debug', 'Google Drive: Loaded existing access token');
                        
                        // Refresh token if expired
                        if ($this->client->isAccessTokenExpired()) {
                            log_message('debug', 'Google Drive: Access token expired, refreshing...');
                            if ($this->client->getRefreshToken()) {
                                $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                                file_put_contents($tokenPath, json_encode($newToken));
                                log_message('info', 'Google Drive: Access token refreshed successfully');
                            } else {
                                log_message('warning', 'Google Drive: No refresh token available, re-authentication needed');
                                $this->isAuthenticated = false;
                            }
                        } else {
                            $this->isAuthenticated = true;
                            log_message('info', 'Google Drive: Authenticated with existing token');
                        }
                    } catch (\Exception $tokenEx) {
                        log_message('warning', 'Google Drive: Failed to load token - ' . $tokenEx->getMessage());
                        $this->isAuthenticated = false;
                    }
                } else {
                    log_message('warning', 'Google Drive: No token file found. User needs to authenticate at /google/drive');
                    $this->isAuthenticated = false;
                }
                
                if ($this->isAuthenticated) {
                    log_message('info', 'Google Drive: OAuth authentication successful');
                } else {
                    log_message('error', 'Google Drive: Authentication required. Visit /google/drive to authenticate.');
                }
                
            } catch (\Exception $e) {
                log_message('error', 'Google Drive: Failed to load credentials - ' . $e->getMessage());
                log_message('error', 'Google Drive: Stack trace - ' . $e->getTraceAsString());
                $this->isAuthenticated = false;
            }
        } else {
            log_message('error', 'Google Drive: Credentials file not found at: ' . $credentialsPath);
            $this->isAuthenticated = false;
        }
    }

    /**
     * Get the Google Drive service instance
     */
    private function getDriveService()
    {
        if (!$this->driveService) {
            $this->driveService = new Drive($this->client);
        }
        return $this->driveService;
    }

    /**
     * Get the Google client instance
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Check if service is authenticated
     */
    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }
    
    /**
     * Check if service is enabled (alias for isAuthenticated)
     */
    public function isEnabled(): bool
    {
        return $this->isAuthenticated();
    }

    /**
     * Upload a file to Google Drive using the Google API client
     */
    public function uploadFile($filePath, $fileName, $mimeType = 'application/pdf')
    {
        try {
            log_message('debug', 'Uploading file to Google Drive: ' . $fileName);
            
            // Verify authentication before upload
            if (!$this->isAuthenticated()) {
                log_message('error', 'Google Drive: Not authenticated, attempting to refresh token');
                if (!$this->refreshToken()) {
                    throw new \Exception('Google Drive authentication failed. Please re-authenticate at /google/drive');
                }
            }
            
            // Get Drive service
            $driveService = $this->getDriveService();
            
            // Prepare file metadata
            $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? null;
            
            log_message('debug', 'Upload details - File: ' . $fileName . ', Folder ID: ' . ($folderId ?: 'none'));
            
            $fileMetadata = new DriveFile([
                'name' => $fileName,
                'parents' => $folderId ? [$folderId] : []
            ]);
            
            // Get file content
            $content = file_get_contents($filePath);
            
            log_message('debug', 'File size: ' . strlen($content) . ' bytes');
            
            // Upload file with proper error handling
            try {
                $uploadedFile = $driveService->files->create($fileMetadata, [
                    'data' => $content,
                    'mimeType' => $mimeType,
                    'uploadType' => 'multipart',
                    'fields' => 'id,name,webViewLink,parents',
                    'supportsAllDrives' => true
                ]);
                
                log_message('debug', 'File created successfully in Google Drive');
                
                // Make file publicly accessible
                try {
                    $permissionMetadata = new \Google\Service\Drive\Permission([
                        'type' => 'anyone',
                        'role' => 'reader'
                    ]);
                    
                    $driveService->permissions->create($uploadedFile->id, $permissionMetadata, [
                        'supportsAllDrives' => true
                    ]);
                    
                    log_message('info', 'File permissions set to public');
                } catch (\Exception $permEx) {
                    log_message('warning', 'Could not set public permissions: ' . $permEx->getMessage());
                }
                
                log_message('info', 'File uploaded to Google Drive successfully. File ID: ' . $uploadedFile->id);
                
                return $uploadedFile->id;
                
            } catch (\Exception $uploadEx) {
                log_message('error', 'Upload failed: ' . $uploadEx->getMessage());
                log_message('debug', 'Upload exception trace: ' . $uploadEx->getTraceAsString());
                throw $uploadEx;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Google Drive Upload Error: ' . $e->getMessage());
            log_message('debug', 'Upload error trace: ' . $e->getTraceAsString());
            throw new \Exception('Google Drive upload failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Refresh the access token
     */
    public function refreshToken()
    {
        if (!$this->client->getRefreshToken()) {
            log_message('error', 'Google Drive: No refresh token available');
            return false;
        }
        
        try {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            
            // Save updated token
            $tokenPath = WRITEPATH . 'google-token.json';
            file_put_contents($tokenPath, json_encode($newToken));
            
            log_message('info', 'Google Drive: Access token refreshed successfully');
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Google Drive: Token refresh failed - ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get public URL for a file
     */
    public function getFileUrl($fileId)
    {
        return "https://drive.google.com/uc?id={$fileId}";
    }
    
    /**
     * Fetch files from Google Drive folder
     */
    public function fetchFilesFromFolder($userId)
    {
        if (!$this->isAuthenticated()) {
            log_message('error', 'Google Drive: Not authenticated');
            return [];
        }
        
        try {
            $driveService = $this->getDriveService();
            $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? null;
            
            if (!$folderId) {
                log_message('warning', 'Google Drive: No folder ID configured');
                return [];
            }
            
            // Query for files with user_id prefix in name
            $query = "'" . $folderId . "' in parents and name contains '" . $userId . "_'";
            
            $optParams = [
                'q' => $query,
                'fields' => 'files(id, name, mimeType, createdTime, modifiedTime)',
                'supportsAllDrives' => true,
                'pageSize' => 100
            ];
            
            $results = $driveService->files->listFiles($optParams);
            $files = $results->getFiles();
            
            $fileList = [];
            foreach ($files as $file) {
                $fileList[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'mimeType' => $file->getMimeType(),
                    'createdTime' => $file->getCreatedTime(),
                    'modifiedTime' => $file->getModifiedTime()
                ];
            }
            
            log_message('debug', 'Found ' . count($fileList) . ' files in Google Drive for user ' . $userId);
            return $fileList;
            
        } catch (\Exception $e) {
            log_message('error', 'Error fetching Google Drive files: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Download a file from Google Drive
     */
    public function downloadFile($fileId, $destinationPath)
    {
        try {
            log_message('debug', 'Downloading Google Drive file: ' . $fileId);
            
            $driveService = $this->getDriveService();
            
            // Download file content
            $response = $driveService->files->get($fileId, [
                'alt' => 'media',
                'supportsAllDrives' => true
            ]);
            
            // Save to destination
            file_put_contents($destinationPath, $response->getBody()->getContents());
            
            log_message('info', 'Successfully downloaded Google Drive file to: ' . $destinationPath);
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Google Drive download failed: ' . $e->getMessage());
            throw new \Exception('Google Drive download failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a file from Google Drive
     */
    public function deleteFile($fileId)
    {
        try {
            log_message('debug', 'Deleting Google Drive file: ' . $fileId);
            
            $driveService = $this->getDriveService();
            
            // Delete file
            $driveService->files->delete($fileId, [
                'supportsAllDrives' => true
            ]);
            
            log_message('info', 'Successfully deleted Google Drive file: ' . $fileId);
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'Google Drive delete failed: ' . $e->getMessage());
            throw new \Exception('Google Drive delete failed: ' . $e->getMessage());
        }
    }
}