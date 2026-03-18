<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class GoogleAuth extends Controller
{
    public function redirectToGoogle()
    {
        // Initialize Google Client
        $client = new \Google\Client();
        $client->setAuthConfig($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH']);
        $client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
        $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();

        return redirect()->to($authUrl);
    }

    public function handleCallback()
    {
        // Check for errors in the callback
        if (isset($_GET['error'])) {
            return redirect()->to('/account/personal')->with('error', 'Google authentication error: ' . $_GET['error']);
        }
        
        if (!isset($_GET['code'])) {
            return redirect()->to('/account/personal')->with('error', 'No authorization code received from Google.');
        }

        $code = $_GET['code'];

        try {
            // Initialize Google Client
            $client = new \Google\Client();
            $client->setAuthConfig($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH']);
            $client->addScope(['https://www.googleapis.com/auth/drive.file', 'https://www.googleapis.com/auth/drive']);
            $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
            $client->setAccessType('offline');
            $client->setPrompt('consent');

            // Exchange authorization code for access token
            $token = $client->fetchAccessTokenWithAuthCode($code);
            
            // Check if token retrieval was successful
            if (isset($token['error'])) {
                throw new \Exception('Failed to get access token: ' . $token['error']);
            }
            
            // Store the token in session for future use
            session()->set('google_access_token', $token);
            
            log_message('info', 'Google Drive authentication successful for user: ' . session()->get('user_id'));

            return redirect()->to('/account/personal')->with('success', 'Successfully authenticated with Google Drive! You can now view and upload files to Google Drive.');
        } catch (\Exception $e) {
            log_message('error', 'Google authentication failed: ' . $e->getMessage());
            return redirect()->to('/account/personal')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }

    public function revokeAccess()
    {
        $token = session('google_access_token');
        if ($token) {
            $client = new \Google\Client();
            $client->setAccessToken($token);

            try {
                $client->revokeToken();
                session()->remove('google_access_token');
                return redirect()->to('/')->with('success', 'Access revoked successfully.');
            } catch (\Exception $e) {
                return redirect()->to('/')->with('error', 'Failed to revoke access: ' . $e->getMessage());
            }
        }

        return redirect()->to('/');
    }
}