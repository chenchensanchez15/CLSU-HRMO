<?php

namespace App\Services;

/**
 * OAuth Google Drive Service
 * 
 * This service handles file uploads/downloads to/from Google Drive
 * using OAuth user authentication instead of service account
 */
class OAuthGoogleDriveService
{
    private $client;
    private $accessToken;
    
    public function __construct()
    {
        // Initialize Google Client
        $this->client = new \Google\Client();
        
        // Load OAuth credentials
        $this->client->setAuthConfig($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? WRITEPATH . 'credentials/oauth_credential.json');
        
        // Set scopes for Drive access
        $this->client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
        
        // Set redirect URI
        $this->client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost/HRMO/public/google/callback');
        
        // Set access type
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        $accessToken = $this->getStoredAccessToken();
        if ($accessToken) {
            $this->client->setAccessToken($accessToken);
            
            // Refresh token if expired
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    $this->storeAccessToken($this->client->getAccessToken());
                } else {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    
    /**
     * Get authentication URL for user to authorize
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }
    
    /**
     * Handle OAuth callback and store access token
     */
    public function handleCallback($code)
    {
        try {
            // Exchange authorization code for access token
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($token);
            
            // Store the token for future use
            $this->storeAccessToken($token);
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception('OAuth callback failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Upload a file to Google Drive
     */
    public function uploadFile($filePath, $fileName, $mimeType = 'application/pdf', $folderId = null)
    {
        if (!$this->isAuthenticated()) {
            throw new \Exception('User not authenticated. Please authenticate first.');
        }
        
        try {
            $service = new \Google\Service\Drive($this->client);
            
            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => $fileName,
                'parents' => $folderId ? [$folderId] : []
            ]);

            $content = file_get_contents($filePath);
            
            $file = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart'
            ]);

            // Set permissions to allow access
            $permission = new \Google\Service\Drive\Permission([
                'role' => 'reader',
                'type' => 'anyone'
            ]);
            
            $service->permissions->create($file->id, $permission);

            return $file->id;
        } catch (\Exception $e) {
            throw new \Exception('Google Drive upload failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Download a file from Google Drive
     */
    public function downloadFile($fileId, $destinationPath)
    {
        if (!$this->isAuthenticated()) {
            throw new \Exception('User not authenticated. Please authenticate first.');
        }
        
        try {
            $service = new \Google\Service\Drive($this->client);
            
            $response = $service->files->get($fileId, [
                'alt' => 'media'
            ]);

            file_put_contents($destinationPath, $response->getBody());

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Google Drive download failed: ' . $e->getMessage());
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
     * Delete a file from Google Drive
     */
    public function deleteFile($fileId)
    {
        if (!$this->isAuthenticated()) {
            throw new \Exception('User not authenticated. Please authenticate first.');
        }
        
        try {
            $service = new \Google\Service\Drive($this->client);
            $service->files->delete($fileId);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Google Drive delete failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get stored access token from session or database
     */
    private function getStoredAccessToken()
    {
        // In a real implementation, this would come from session or database
        // For now, returning null to force re-authentication
        return null;
    }
    
    /**
     * Store access token in session or database
     */
    private function storeAccessToken($token)
    {
        // In a real implementation, this would be stored in session or database
        // For now, just storing in a temporary file
        file_put_contents(WRITEPATH . 'temp/access_token.json', json_encode($token));
    }
}