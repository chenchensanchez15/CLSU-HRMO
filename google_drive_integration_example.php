<?php
/**
 * Example implementation of Google Drive Integration for HRMO File Uploads
 * 
 * This file shows how the Account controller's updateFile method would be modified
 * to support Google Drive file storage instead of local storage.
 */

// Modified updateFile method to support Google Drive
public function updateFile()
{
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
    $userId = $session->get('user_id');

    $fileModel = new \App\Models\ApplicantDocumentsModel();
    $documentTypeModel = new \App\Models\DocumentTypeModel();

    $documentTypeId = $this->request->getPost('document_type_id');
    
    // Validate document type ID
    if (!$documentTypeId) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Document type is required.'
        ]);
    }

    $documentType = $documentTypeModel->find($documentTypeId);
    if (!$documentType) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid document type.'
        ]);
    }

    $file = $this->request->getFile('file');
    if (!$file || !$file->isValid() || $file->hasMoved()) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No valid file uploaded.'
        ]);
    }

    // Check file type (PDF only)
    $allowedTypes = ['application/pdf'];
    if (!in_array($file->getMimeType(), $allowedTypes)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Only PDF files are allowed.'
        ]);
    }

    // Check file size (5MB limit)
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    if ($file->getSize() > $maxFileSize) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'File size must not exceed 5 MB.'
        ]);
    }

    // Initialize Google Drive service
    $driveService = new \App\Services\GoogleDriveService();
    
    if ($driveService->isEnabled()) {
        // Use Google Drive for file storage
        try {
            // Save temporary file locally first
            $tempPath = WRITEPATH . 'temp/' . $file->getRandomName();
            $file->move(WRITEPATH . 'temp/', basename($tempPath));
            
            // Upload to Google Drive
            $driveFileId = $driveService->uploadFile(
                $tempPath,
                $file->getName(),
                $file->getMimeType()
            );
            
            // Clean up temporary file
            unlink($tempPath);
            
            // Save Google Drive file ID to database instead of local filename
            $result = $fileModel->saveDocument($userId, $documentTypeId, $driveFileId);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $documentType['document_type_name'].' updated successfully!',
                    'file_id' => $driveFileId,
                    'file_url' => $driveService->getFileUrl($driveFileId)
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save document.'
                ]);
            }
        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Google Drive upload failed: ' . $e->getMessage()
            ]);
        }
    } else {
        // Fallback to local storage if Google Drive is not available
        helper('filesystem');

        $newName = $file->getRandomName();
        $uploadPath = WRITEPATH.'uploads/files';

        if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

        $file->move($uploadPath, $newName);

        // Save or update document using the new model method
        $result = $fileModel->saveDocument($userId, $documentTypeId, $newName);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $documentType['document_type_name'].' updated successfully!',
                'file_name' => $newName,
                'file_url'  => base_url('writable/uploads/files/'.$newName)
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save document.'
            ]);
        }
    }
}

// Modified viewFile method to retrieve from Google Drive
public function viewFile($filename)
{
    // Decode the filename to handle any special characters
    $filename = basename($filename);
    
    // Check if the filename is a Google Drive file ID (typically alphanumeric with some special characters)
    // Google Drive file IDs are typically 28-33 characters long and contain letters, numbers, and hyphens/underscores
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $filename);
    
    if ($isGoogleDriveFile) {
        // File is stored in Google Drive
        $driveService = new \App\Services\GoogleDriveService();
        
        if ($driveService->isEnabled()) {
            try {
                // Create a temporary file to download from Google Drive
                $tempPath = WRITEPATH . 'temp/' . $filename . '.pdf';
                
                $driveService->downloadFile($filename, $tempPath);
                
                $response = $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '.pdf"')
                    ->setBody(file_get_contents($tempPath));
                
                // Clean up temporary file
                unlink($tempPath);
                
                return $response;
            } catch (Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Could not retrieve file from Google Drive: ' . $e->getMessage()
                ])->setStatusCode(404);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Google Drive service not available.'
            ])->setStatusCode(500);
        }
    } else {
        // File is stored locally (fallback for existing files)
        $path = null;
        $possiblePaths = [
            WRITEPATH . 'uploads/files/' . $filename,
            FCPATH . 'uploads/' . $filename,
        ];

        foreach ($possiblePaths as $possiblePath) {
            if (file_exists($possiblePath)) {
                $path = $possiblePath;
                break;
            }
        }

        if (!$path) {
            return $this->response->setJSON([
                'status'  => 'warning',
                'message' => 'No file has been uploaded for this document.'
            ])->setStatusCode(200);
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Content-Disposition', 'inline; filename="'.$filename.'"')
            ->setBody(file_get_contents($path));
    }
}

// Modified deleteFile method to handle Google Drive files
public function deleteFile()
{
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
    $userId = $session->get('user_id');

    $documentTypeId = $this->request->getPost('document_type_id');
    
    if (!$documentTypeId) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Document type is required.'
        ]);
    }

    $fileModel = new \App\Models\ApplicantDocumentsModel();
    $documentTypeModel = new \App\Models\DocumentTypeModel();
    
    $document = $fileModel->getDocumentByType($userId, $documentTypeId);
    
    if (!$document) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No file to delete.'
        ]);
    }
    
    $documentType = $documentTypeModel->find($documentTypeId);
    if (!$documentType) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid document type.'
        ]);
    }

    // Check if file is stored in Google Drive (by checking if filename looks like a drive ID)
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $document['filename']);
    
    if ($isGoogleDriveFile) {
        // Delete from Google Drive
        $driveService = new \App\Services\GoogleDriveService();
        
        if ($driveService->isEnabled()) {
            try {
                $driveService->deleteFile($document['filename']);
            } catch (Exception $e) {
                // Log the error but continue with DB deletion
                log_message('error', 'Could not delete file from Google Drive: ' . $e->getMessage());
            }
        }
    } else {
        // Delete local file
        $filePath = WRITEPATH . 'uploads/files/' . $document['filename'];
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    // Delete from DB
    $result = $fileModel->deleteDocument($userId, $documentTypeId);

    if ($result) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $documentType['document_type_name'].' deleted successfully!'
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete document.'
        ]);
    }
}