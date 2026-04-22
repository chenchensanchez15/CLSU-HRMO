<?php

namespace App\Controllers;

use App\Services\GoogleDriveService;

class Google extends BaseController
{
    /**
     * Redirect to Google OAuth authentication
     */
    public function drive()
    {
        $driveService = new GoogleDriveService();
        
        // Get authentication URL
        $authUrl = $driveService->getAuthUrl();
        
        // Redirect user to Google OAuth consent screen
        return redirect()->to($authUrl);
    }
    
    /**
     * Handle OAuth callback
     */
    public function callback()
    {
        $driveService = new GoogleDriveService();
        
        try {
            // Get authorization code from query string
            $code = $this->request->getGet('code');
            
            if (!$code) {
                throw new \Exception('No authorization code received');
            }
            
            // Exchange code for token
            $driveService->handleCallback($code);
            
            log_message('info', 'Google Drive OAuth authentication successful');
            
            // Redirect to profile page with success message
            return redirect()->to('/account/personal')->with('success', 'Google Drive connected successfully!');
            
        } catch (\Exception $e) {
            log_message('error', 'OAuth callback error: ' . $e->getMessage());
            return redirect()->to('/account/personal')->with('error', 'Failed to connect Google Drive: ' . $e->getMessage());
        }
    }
    
    /**
     * Check connection status
     */
    public function status()
    {
        $driveService = new GoogleDriveService();
        
        if ($driveService->isAuthenticated()) {
            return $this->response->setJSON([
                'status' => 'connected',
                'message' => 'Google Drive is connected'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'disconnected',
                'message' => 'Google Drive is not connected'
            ]);
        }
    }
}
