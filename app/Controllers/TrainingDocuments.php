<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TrainingDocuments extends BaseController
{
    /**
     * View multiple training certificates in sequence (actual files, NOT combined)
     * Displays all certificate files from trainings one after another in a modal
     */
    public function viewMultiple($application_id)
    {
        $db = \Config\Database::connect();
        
        // Fetch trainings for this application
        $trainings = $db->table('application_trainings at')
            ->join('lib_training_category tc', 'at.training_category_id = tc.id_training_category', 'left')
            ->select('at.id_application_trainings, at.training_name, at.date_from, at.date_to, at.training_facilitator, at.training_hours, at.training_sponsor, at.training_remarks, at.certificate_file, tc.training_category_name')
            ->where(['at.job_application_id' => $application_id])
            ->orderBy('at.date_from', 'DESC')
            ->get()
            ->getResultArray();
        
       if (empty($trainings)) {
           return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'No trainings found for this application.'
                ]);
        }
        
        // Collect all certificate files
        $certificateFiles = [];
        foreach ($trainings as $training) {
           if (!empty($training['certificate_file'])) {
                $certificateFiles[] = [
                    'file' => $training['certificate_file'],
                    'training_name' => $training['training_name'] ?? 'Training',
                    'date_from' => !empty($training['date_from']) ? date('F d, Y', strtotime($training['date_from'])) : '-',
                    'date_to' => !empty($training['date_to']) ? date('F d, Y', strtotime($training['date_to'])) : '-',
                    'facilitator' => $training['training_facilitator'] ?? '-',
                    'hours' => $training['training_hours'] ?? '-',
                ];
            }
        }
        
       if (empty($certificateFiles)) {
           return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => 'warning',
                    'message' => 'No training certificates found.'
                ]);
        }
        
        // Return list of certificate files for the modal to display
       return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setJSON([
                'status' => 'success',
                'certificates' => $certificateFiles,
                'count' => count($certificateFiles)
            ]);
    }
    
    /**
     * Serve individual training certificate file
     */
    public function getCertificate($filename)
    {
        // Check if it's a Google Drive file ID
        $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
        
       if ($isGoogleDriveFile) {
            log_message('debug', 'Serving training certificate from Google Drive: ' . $filename);
            
            try {
                $driveService = new \App\Libraries\GoogleDriveOAuthService();
                
               if ($driveService->isEnabled()) {
                    $tempPath = sys_get_temp_dir() . '/training_cert_' . $filename;
                    
                    // Download file from Google Drive
                    $result = $driveService->downloadFile($filename, $tempPath);
                    
                   if ($result && file_exists($tempPath)) {
                        $mime = mime_content_type($tempPath);
                        
                       return $this->response
                            ->setHeader('Content-Type', $mime)
                            ->setHeader('Content-Disposition', 'inline; filename="training_certificate.pdf"')
                            ->setBody(file_get_contents($tempPath));
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Error serving training certificate: ' . $e->getMessage());
            }
            
           return $this->response->setStatusCode(404)
                ->setJSON([
                    'status' => 'warning',
                    'message' => 'Unable to retrieve training certificate.'
                ]);
        }
        
        // Local file handling
        $filename = basename($filename);
        $filePath = WRITEPATH . 'uploads/trainings/' . $filename;
        
       if (!file_exists($filePath)) {
           return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => 'warning',
                    'message' => 'Training certificate not found.'
                ]);
        }
        
        $mime = mime_content_type($filePath) ?: 'application/pdf';
        
       return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename. '"')
            ->setBody(file_get_contents($filePath));
    }
}
