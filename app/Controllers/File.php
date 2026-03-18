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
        
        // Check if the filename is a Google Drive file ID (typically 28-33 characters long)
        // Local uploaded files have timestamp prefixes like "1772469100_filename.pdf"
        $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{28,33}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
        
        if ($isGoogleDriveFile) {
            // File is stored in Google Drive
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            if ($driveService->isEnabled()) {
                // Redirect to Google Drive public URL instead of downloading
                $publicUrl = $driveService->getFileUrl($filename);
                return redirect()->to($publicUrl);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Google Drive service not available.'
                ])->setStatusCode(500);
            }
        } else {
            // File is stored locally (fallback for existing files)
            $filePath = WRITEPATH . 'uploads/files/' . $filename;

            if (!file_exists($filePath)) {
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
            // Redirect to Google Drive public URL
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            if ($driveService->isEnabled()) {
                $publicUrl = $driveService->getFileUrl($filename);
                return redirect()->to($publicUrl);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Google Drive service not available.'
                ])->setStatusCode(500);
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
