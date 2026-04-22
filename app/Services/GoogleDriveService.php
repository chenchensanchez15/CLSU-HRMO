<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

/**
 * Google Drive Service - OAuth User Authentication
 * Same implementation as HRMIS template
 */
class GoogleDriveService
{
    protected $client;
    protected $service;
    protected $config;
    protected $isAuthenticated = false;
    
    public function __construct()
    {
        $this->client = new Client();
        
        // Load OAuth credentials
        $this->client->setAuthConfig($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? WRITEPATH . 'credentials/oauth_credential.json');
        
        // Set scopes - same as HRMIS
        $this->client->addScope([
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/drive.appdata',
            'https://www.googleapis.com/auth/drive'
        ]);
        
        // Set redirect URI - MUST match exactly what's in Google Cloud Console
        $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost:8080/HRMO/google/callback';
        log_message('debug', 'Setting Google redirect URI: ' . $redirectUri);
        $this->client->setRedirectUri($redirectUri);
        
        // IMPORTANT: Set access type to offline to get refresh token
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        
        // Load token if exists
        $this->loadToken();
        
        $this->service = new Drive($this->client);
    }
    
    /**
     * Check if authenticated
     */
    public function isAuthenticated(): bool
    {
        if ($this->client->getAccessToken()) {
            // Refresh token if expired
            if ($this->client->isAccessTokenExpired()) {
                $this->refreshToken();
            }
            return true;
        }
        return false;
    }
    
    /**
     * Get authentication URL
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }
    
    /**
     * Handle OAuth callback
     */
    public function handleCallback($code)
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->saveToken($token);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('OAuth callback failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Refresh token
     */
    public function refreshToken(): bool
    {
        try {
            $token = $this->client->getRefreshToken();
            if ($token) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($token);
                $this->saveToken($newToken);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            log_message('error', 'Token refresh failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Upload file to Google Drive
     */
    public function uploadFile($filePath, $fileName, $mimeType = null, $folderId = null)
    {
        if (!$this->isAuthenticated()) {
            throw new \Exception('User not authenticated with Google Drive');
        }
        
        // Determine MIME type
        if (!$mimeType) {
            $mimeType = mime_content_type($filePath);
        }
        
        // Create file metadata
        $fileMetadata = new DriveFile([
            'name' => $fileName,
            'mimeType' => $mimeType
        ]);
        
        // Set parent folder
        $targetFolderId = $folderId ?: ($_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? null);
        if ($targetFolderId) {
            $fileMetadata->setParents([$targetFolderId]);
        }
        
        // Upload file
        $content = file_get_contents($filePath);
        $file = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id,name,webViewLink,webContentLink'
        ]);
        
        // Make file publicly accessible
        $this->makePublic($file->id);
        
        return [
            'id' => $file->id,
            'name' => $file->name,
            'webViewLink' => $file->webViewLink,
            'webContentLink' => $file->webContentLink
        ];
    }
    
    /**
     * Make file publicly accessible
     */
    protected function makePublic($fileId)
    {
        try {
            $permission = new \Google\Service\Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader'
            ]);
            
            $this->service->permissions->create($fileId, $permission);
        } catch (\Exception $e) {
            log_message('warning', 'Could not make file public: ' . $e->getMessage());
        }
    }
    
    /**
     * Get file content
     */
    public function getFileContent($fileId)
    {
        if (!$this->isAuthenticated()) {
            throw new \Exception('Not authenticated');
        }
        
        $response = $this->service->files->get($fileId, [
            'alt' => 'media'
        ]);
        
        // Handle different response types
        if (is_object($response) && method_exists($response, 'getBody')) {
            return $response->getBody()->getContents();
        }
        
        // If response is already the content
        return (string)$response;
    }
    
    /**
     * Get file metadata
     */
    public function getFile($fileId)
    {
        return $this->service->files->get($fileId);
    }
    
    /**
     * Save token to file
     */
    protected function saveToken($token)
    {
        $tokenPath = WRITEPATH . 'google-token.json';
        file_put_contents($tokenPath, json_encode($token));
        log_message('debug', 'Google token saved to: ' . $tokenPath);
    }
    
    /**
     * Load token from file
     */
    protected function loadToken()
    {
        $tokenPath = WRITEPATH . 'google-token.json';
        if (file_exists($tokenPath)) {
            $token = json_decode(file_get_contents($tokenPath), true);
            if ($token && isset($token['access_token'])) {
                $this->client->setAccessToken($token);
                $this->isAuthenticated = true;
                log_message('debug', 'Google token loaded from: ' . $tokenPath);
                return true;
            }
        }
        log_message('debug', 'No Google token found at: ' . $tokenPath);
        return false;
    }
}
