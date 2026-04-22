<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class File extends Controller
{
    // Training certificate
    public function viewTrainingCertificate($id, $filename)
    {
        $filename = basename($filename);
        $filePath = WRITEPATH . 'uploads/trainings/' . $filename;

        if (!file_exists($filePath)) {
            // Return JSON error
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No training certificate has been uploaded for this record.'
            ])->setStatusCode(404);
        }

        return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->setBody(file_get_contents($filePath));
    }

    // General files (PDS, Resume, TOR, Diploma, etc.)
    public function viewFile($filename)
    {
        if (!$filename) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No file has been uploaded for this document.'
            ])->setStatusCode(404);
        }

        $filename = basename($filename);
        
        // Check if the filename is a Google Drive file ID (typically 20-44 characters long)
        // Local uploaded files have timestamp prefixes like "1772469100_filename.pdf"
        $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
        
        if ($isGoogleDriveFile) {
            // File is stored in Google Drive - download and serve it for full toolbar
            $content = null;
            
            // Try 1: OAuth service
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            if ($driveService->isEnabled()) {
                try {
                    log_message('debug', 'Downloading Google Drive file using OAuth: ' . $filename);
                    
                    // Create temp file
                    $tempFile = sys_get_temp_dir() . '/' . uniqid('gdrive_') . '.pdf';
                    
                    // Download from Google Drive
                    $driveService->downloadFile($filename, $tempFile);
                    
                    if (file_exists($tempFile)) {
                        $content = file_get_contents($tempFile);
                        unlink($tempFile);
                        log_message('info', 'Successfully served Google Drive file (OAuth): ' . $filename);
                    }
                } catch (\Exception $e) {
                    log_message('warning', 'OAuth download failed: ' . $e->getMessage());
                }
            }
            
            // Try 2: Service account fallback
            if (!$content) {
                try {
                    log_message('debug', 'Trying service account for Google Drive file: ' . $filename);
                    
                    $serviceAccountPath = WRITEPATH . 'credentials/google_credentials.json';
                    if (file_exists($serviceAccountPath)) {
                        $client = new \Google\Client();
                        $client->setAuthConfig($serviceAccountPath);
                        $client->addScope([\Google\Service\Drive::DRIVE]);
                        
                        $accessToken = $client->fetchAccessTokenWithAssertion();
                        
                        if (isset($accessToken['access_token'])) {
                            // Use cURL to download
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $filename . '?alt=media&supportsAllDrives=true');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Authorization: Bearer ' . $accessToken['access_token']
                            ]);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            
                            $content = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);
                            
                            if ($httpCode == 200 && !empty($content)) {
                                log_message('info', 'Successfully served Google Drive file (Service Account): ' . $filename);
                            } else {
                                $content = null;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Service account download failed: ' . $e->getMessage());
                }
            }
            
            // Serve file if we got content
            if ($content) {
                return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '.pdf"')
                            ->setHeader('Accept-Ranges', 'bytes')
                            ->setBody($content);
            } else {
                // Fallback: redirect to preview URL
                log_message('warning', 'All download methods failed, redirecting to preview: ' . $filename);
                $previewUrl = "https://drive.google.com/file/d/{$filename}/preview";
                return redirect()->to($previewUrl);
            }
        } else {
            // File is stored locally (fallback for existing files)
            // Check multiple possible locations
            $possiblePaths = [
                WRITEPATH . 'uploads/files/' . $filename,           // General uploads
                WRITEPATH . 'uploads/civil_service/' . $filename,   // Civil service certificates
                WRITEPATH . 'uploads/trainings/' . $filename,       // Training certificates
                WRITEPATH . 'uploads/' . $filename,                 // Root uploads folder
            ];
            
            $filePath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $filePath = $path;
                    break;
                }
            }

            if (!$filePath) {
                log_message('warning', 'File not found in any location: ' . $filename);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No file has been uploaded for this document.'
                ])->setStatusCode(404);
            }

            return $this->response
                        ->setHeader('Content-Type', mime_content_type($filePath))
                        ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                        ->setHeader('Accept-Ranges', 'bytes')
                        ->setBody(file_get_contents($filePath));
        }
    }

    public function viewDocument($applicationId, $docType)
    {
        $db = \Config\Database::connect();
        $record = $db->table('application_documents')
                     ->where('job_application_id', $applicationId)
                     ->get()
                     ->getRowArray();

        if (!$record || empty($record[$docType])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No file has been uploaded for this document.'
            ])->setStatusCode(404);
        }

        $filename = $record[$docType];
        
        // Check if this is a Google Drive file ID
        $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{28,33}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
        
        if ($isGoogleDriveFile) {
            // Try to serve file content for full toolbar (same as viewFile method)
            $content = null;
            
            // Try 1: OAuth service
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            if ($driveService->isEnabled()) {
                try {
                    log_message('debug', 'Downloading Google Drive file using OAuth: ' . $filename);
                    
                    $tempFile = sys_get_temp_dir() . '/' . uniqid('gdrive_') . '.pdf';
                    $driveService->downloadFile($filename, $tempFile);
                    
                    if (file_exists($tempFile)) {
                        $content = file_get_contents($tempFile);
                        unlink($tempFile);
                        log_message('info', 'Successfully served Google Drive file (OAuth): ' . $filename);
                    }
                } catch (\Exception $e) {
                    log_message('warning', 'OAuth download failed: ' . $e->getMessage());
                }
            }
            
            // Try 2: Service account fallback
            if (!$content) {
                try {
                    $serviceAccountPath = WRITEPATH . 'credentials/google_credentials.json';
                    if (file_exists($serviceAccountPath)) {
                        $client = new \Google\Client();
                        $client->setAuthConfig($serviceAccountPath);
                        $client->addScope([\Google\Service\Drive::DRIVE]);
                        
                        $accessToken = $client->fetchAccessTokenWithAssertion();
                        
                        if (isset($accessToken['access_token'])) {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $filename . '?alt=media&supportsAllDrives=true');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Authorization: Bearer ' . $accessToken['access_token']
                            ]);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            
                            $content = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);
                            
                            if ($httpCode == 200 && !empty($content)) {
                                log_message('info', 'Successfully served Google Drive file (Service Account): ' . $filename);
                            } else {
                                $content = null;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Service account download failed: ' . $e->getMessage());
                }
            }
            
            // Serve file if we got content
            if ($content) {
                return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '.pdf"')
                            ->setHeader('Accept-Ranges', 'bytes')
                            ->setBody($content);
            } else {
                // Last resort: redirect to Google Drive URL
                log_message('warning', 'All download methods failed, redirecting to Google Drive: ' . $filename);
                $driveService = new \App\Libraries\GoogleDriveOAuthService();
                if ($driveService->isEnabled()) {
                    $publicUrl = $driveService->getFileUrl($filename);
                    return redirect()->to($publicUrl);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Unable to access file. Please try again later.'
                    ])->setStatusCode(500);
                }
            }
        } else {
            // Handle local file (fallback for existing files)
            $filePath = WRITEPATH . 'uploads/files/' . $filename;

            if (!file_exists($filePath)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No file has been uploaded for this document.'
                ])->setStatusCode(404);
            }

            return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                        ->setBody(file_get_contents($filePath));
        }
    }
}
