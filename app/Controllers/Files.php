<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Files extends Controller
{
    public function training($filename = null)
    {
        if (!$filename) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not specified');
        }

        $path = WRITEPATH . 'uploads/trainings/' . $filename;

        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File does not exist');
        }

        // Get the file's MIME type (so browser knows it's PDF)
        $mime = mime_content_type($path);

        return $this->response
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->setBody(file_get_contents($path));
    }
    // In app/Controllers/Files.php
public function document($filename = null)
{
    if (!$filename) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not specified');
    }

    $path = WRITEPATH . 'uploads/' . $filename; // <-- STEP 7 documents are here

    if (!file_exists($path)) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File does not exist');
    }

    $mime = mime_content_type($path);

    return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody(file_get_contents($path));
}

}
