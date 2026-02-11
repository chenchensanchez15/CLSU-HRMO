<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;

class File extends Controller
{
    // Existing method for training certificates
    public function viewTrainingCertificate($id, $filename)
    {
        $filename = basename($filename); // sanitize to prevent path traversal
        $filePath = WRITEPATH . 'uploads/trainings/' . $filename;

        if (!file_exists($filePath)) {
            throw PageNotFoundException::forPageNotFound('Certificate not found.');
        }

        return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->setBody(file_get_contents($filePath));
    }

    // New method for general files (PDS, Performance, Resume, TOR, Diploma)
    public function viewFile($filename)
    {
        if (!$filename) {
            throw PageNotFoundException::forPageNotFound('File not specified.');
        }

        $filename = basename($filename); // sanitize to prevent path traversal
        $filePath = WRITEPATH . 'uploads/files/' . $filename;

        if (!file_exists($filePath)) {
            throw PageNotFoundException::forPageNotFound('File not found.');
        }

        return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->setBody(file_get_contents($filePath));
    }
}
