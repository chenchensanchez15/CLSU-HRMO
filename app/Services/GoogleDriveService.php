<?php

namespace App\Services;

/**
 * Google Drive Service
 * 
 * This service handles file uploads/downloads to/from Google Drive
 * Note: Requires Google API Client library to be installed
 * Run: composer require google/apiclient
 */
class GoogleDriveService
{
    private $enabled = false;
    
    public function __construct()
    {
        // Check if Google API client is available
        if (class_exists('\Google\Client')) {
            $this->enabled = true;
        } else {
            // Log that Google API client is not installed
            log_message('warning', 'Google API Client not found. Please install with: composer require google/apiclient');
        }
    }

    /**
     * Check if Google Drive service is available
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Upload a file to Google Drive
     */
    public function uploadFile($filePath, $fileName, $mimeType = 'application/pdf')
    {
        if (!$this->enabled) {
            throw new \Exception('Google Drive service not enabled. Install Google API Client first.');
        }

        // This is a placeholder - the actual implementation would go here
        // once the Google API client is installed and configured
        try {
            // Initialize Google Client
            $client = new \Google\Client();
            $client->setAuthConfig($_ENV['GOOGLE_CREDENTIALS_PATH'] ?? WRITEPATH . 'credentials/google_credentials.json');
            $client->addScope(\Google\Service\Drive::DRIVE_FILE);
            $client->setAccessType('offline');
            
            $service = new \Google\Service\Drive($client);
            
            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => $fileName,
                'parents' => $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ? [$_ENV['GOOGLE_DRIVE_FOLDER_ID']] : []
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
        if (!$this->enabled) {
            throw new \Exception('Google Drive service not enabled. Install Google API Client first.');
        }

        try {
            $client = new \Google\Client();
            $client->setAuthConfig($_ENV['GOOGLE_CREDENTIALS_PATH'] ?? WRITEPATH . 'credentials/google_credentials.json');
            $client->addScope(\Google\Service\Drive::DRIVE_FILE);
            
            $service = new \Google\Service\Drive($client);
            
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
        if (!$this->enabled) {
            throw new \Exception('Google Drive service not enabled. Install Google API Client first.');
        }

        try {
            $client = new \Google\Client();
            $client->setAuthConfig($_ENV['GOOGLE_CREDENTIALS_PATH'] ?? WRITEPATH . 'credentials/google_credentials.json');
            $client->addScope(\Google\Service\Drive::DRIVE_FILE);
            
            $service = new \Google\Service\Drive($client);
            
            $service->files->delete($fileId);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Google Drive delete failed: ' . $e->getMessage());
        }
    }
}