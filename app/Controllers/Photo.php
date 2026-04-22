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
                log_message('debug', 'Attempting to fetch profile photo from Google Drive for user ' . $userId);
                
                // Try 1: Use OAuth service (primary method)
                $driveService = new \App\Libraries\GoogleDriveOAuthService();
                
                if ($driveService->isEnabled()) {
                    log_message('debug', 'OAuth authentication successful, downloading photo...');
                    
                    $tempPath = sys_get_temp_dir() . '/profile_photo_' . $userId . '.jpg';
                    $driveService->downloadFile($photoValue, $tempPath);
                    
                    if (file_exists($tempPath)) {
                        $content = file_get_contents($tempPath);
                        unlink($tempPath);
                        
                        log_message('info', 'Successfully served profile photo from Google Drive (OAuth) for user ' . $userId);
                        
                        return $this->response
                            ->setHeader('Content-Type', 'image/jpeg')
                            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                            ->setHeader('Pragma', 'no-cache')
                            ->setHeader('Expires', '0')
                            ->setBody($content);
                    }
                }
                
                // Try 2: Fallback to service account if OAuth fails
                log_message('warning', 'OAuth failed, trying service account credentials...');
                
                $serviceAccountPath = WRITEPATH . 'credentials/google_credentials.json';
                if (file_exists($serviceAccountPath)) {
                    $client = new \Google\Client();
                    $client->setAuthConfig($serviceAccountPath);
                    $client->addScope([\Google\Service\Drive::DRIVE]);
                    
                    // Get access token
                    $accessToken = $client->fetchAccessTokenWithAssertion();
                    
                    if (isset($accessToken['access_token'])) {
                        // Use direct API call with cURL
                        $tempPath = sys_get_temp_dir() . '/profile_photo_' . $userId . '.jpg';
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $photoValue . '?alt=media&supportsAllDrives=true');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Authorization: Bearer ' . $accessToken['access_token']
                        ]);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        
                        $content = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        
                        if ($httpCode == 200 && !empty($content)) {
                            file_put_contents($tempPath, $content);
                            
                            if (file_exists($tempPath)) {
                                $finalContent = file_get_contents($tempPath);
                                unlink($tempPath);
                                
                                log_message('info', 'Successfully served profile photo from Google Drive (Service Account) for user ' . $userId);
                                
                                return $this->response
                                    ->setHeader('Content-Type', 'image/jpeg')
                                    ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                                    ->setHeader('Pragma', 'no-cache')
                                    ->setHeader('Expires', '0')
                                    ->setBody($finalContent);
                            }
                        }
                    }
                }
                
                throw new \Exception('Both OAuth and Service Account authentication failed');
                
            } catch (\Exception $e) {
                log_message('error', 'Error fetching Google Drive photo: ' . $e->getMessage());
                log_message('debug', 'Exception trace: ' . $e->getTraceAsString());
                
                // Return placeholder on error - but make it less alarming
                return $this->response->setHeader('Content-Type', 'image/svg+xml')
                    ->setBody('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="100%" height="100%" fill="#f0f0f0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#999" font-size="14">Photo Unavailable</text></svg>');
            }
        } else {
            // Local file
            $photoPath = FCPATH . 'uploads/' . $photoValue;
            
            if (file_exists($photoPath)) {
                return $this->response
                    ->setHeader('Content-Type', mime_content_type($photoPath))
                    ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->setHeader('Pragma', 'no-cache')
                    ->setHeader('Expires', '0')
                    ->setBody(file_get_contents($photoPath));
            }
        }
        
        // File not found
        return $this->response->setHeader('Content-Type', 'image/svg+xml')
            ->setBody('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="100%" height="100%" fill="#e0e0e0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#999">Not Found</text></svg>');
    }
}
