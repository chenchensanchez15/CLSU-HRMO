<?php

namespace App\Controllers;

use App\Models\ApplicantModel;

class Photo extends BaseController
{
    /**
     * Serve profile photo from Google Drive or local storage
     */
    public function getProfilePhoto($userId = null)
    {
        $session = session();
        
        // Use provided user ID or get from session
        if (!$userId) {
            $userId = $session->get('user_id');
        }
        
        if (!$userId) {
            return redirect()->to('/login');
        }
        
        // Get profile from database
        $applicantModel = new ApplicantModel();
        $profile = $applicantModel->where('user_id', $userId)->first();
        
        if (!$profile || empty($profile['photo'])) {
            // Return placeholder image
            return $this->response->setHeader('Content-Type', 'image/svg+xml')
                ->setBody('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="100%" height="100%" fill="#e0e0e0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#999">No Photo</text></svg>');
        }
        
        $photoValue = $profile['photo'];
        
        // Check if it's a Google Drive file ID
        $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $photoValue) && !preg_match('/^\d{10}_/', $photoValue);
        
        if ($isGoogleDriveFile) {
            try {
                // Fetch from Google Drive
                $driveService = new \App\Libraries\GoogleDriveOAuthService();
                
                if ($driveService->isAuthenticated()) {
                    log_message('debug', 'Fetching profile photo from Google Drive for user ' . $userId);
                    
                    $tempPath = sys_get_temp_dir() . '/profile_photo_' . $userId . '.jpg';
                    $driveService->downloadFile($photoValue, $tempPath);
                    
                    if (file_exists($tempPath)) {
                        $content = file_get_contents($tempPath);
                        unlink($tempPath);
                        
                        return $this->response
                            ->setHeader('Content-Type', 'image/jpeg')
                            ->setHeader('Cache-Control', 'public, max-age=3600')
                            ->setBody($content);
                    }
                }
                
                throw new \Exception('Could not authenticate with Google Drive');
                
            } catch (\Exception $e) {
                log_message('error', 'Error fetching Google Drive photo: ' . $e->getMessage());
                
                // Return placeholder on error
                return $this->response->setHeader('Content-Type', 'image/svg+xml')
                    ->setBody('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="100%" height="100%" fill="#ffe0e0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#c00">Error</text></svg>');
            }
        } else {
            // Local file
            $photoPath = FCPATH . 'uploads/' . $photoValue;
            
            if (file_exists($photoPath)) {
                return $this->response
                    ->setHeader('Content-Type', mime_content_type($photoPath))
                    ->setHeader('Cache-Control', 'public, max-age=3600')
                    ->setBody(file_get_contents($photoPath));
            }
        }
        
        // File not found
        return $this->response->setHeader('Content-Type', 'image/svg+xml')
            ->setBody('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="100%" height="100%" fill="#e0e0e0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#999">Not Found</text></svg>');
    }
}
