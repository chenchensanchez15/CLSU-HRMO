<?php

namespace App\Libraries;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\File as DriveFile;

class GoogleDriveOAuthService
{
    protected $client;
    protected $driveService;
    protected $isAuthenticated = false;

    public function __construct()
    {
        $this->client = new Client();
        
        // Use Service Account for automatic authentication
        $credentialsPath = $_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? WRITEPATH . 'credentials/google_credentials.json';
        
        log_message('debug', 'Google Drive: Looking for credentials at: ' . $credentialsPath);
        
        if (file_exists($credentialsPath)) {
            try {
                $this->client->setAuthConfig($credentialsPath);
                $this->client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
                
                // Set application name for better identification
                $this->client->setApplicationName('HRMO Document System');
                
                log_message('debug', 'Google Drive: Credentials loaded successfully');
                
                // For service accounts, we don't need to fetch token upfront
                // The token will be fetched automatically when making API calls
                $this->isAuthenticated = true;
                log_message('info', 'Google Drive: Service account initialized successfully');
                
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
            
            // Get Drive service
            $driveService = $this->getDriveService();
            
            // Prepare file metadata
            $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? null;
            
            $fileMetadata = new DriveFile([
                'name' => $fileName,
                'parents' => $folderId ? [$folderId] : []
            ]);
            
            // Get file content
            $content = file_get_contents($filePath);
            
            // Create temp file for upload
            $tempFile = tmpfile();
            fwrite($tempFile, $content);
            fseek($tempFile, 0);
            
            // Upload file
            $uploadedFile = $driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id,name,webViewLink',
                'supportsAllDrives' => true
            ]);
            
            fclose($tempFile);
            
            // Make file publicly accessible
            $permission = new DriveFile([
                'type' => 'anyone',
                'role' => 'reader'
            ]);
            
            $driveService->permissions->create($uploadedFile->id, $permission, [
                'supportsAllDrives' => true
            ]);
            
            log_message('info', 'File uploaded to Google Drive successfully. File ID: ' . $uploadedFile->id);
            
            return $uploadedFile->id;
            
        } catch (\Exception $e) {
            log_message('error', 'Google Drive Upload Error: ' . $e->getMessage());
            throw new \Exception('Google Drive upload failed: ' . $e->getMessage());
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