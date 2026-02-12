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

    // Application documents (PDS, Performance, Resume, TOR, Diploma)
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
