<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;
use setasign\Fpdi\Fpdi;

/**
 * TrainingCertificateCombiner
 * 
 * Combines multiple training certificate PDFs into a single PDF document
 * by importing actual PDF content (not recreating HTML)
 */
class TrainingCertificateCombiner
{
    protected $fpdi;
    protected $uploadPath;
    
    public function __construct()
    {
        $this->uploadPath = WRITEPATH . 'uploads/trainings/';
        
        // Initialize FPDI for PDF manipulation
        $this->fpdi = new Fpdi();
    }
    
    /**
     * Combine multiple training certificates into one PDF
     * Imports actual PDF pages from each certificate file
     * 
     * @param array $trainings Array of training records with certificate_file paths
     * @param string $outputFilename Output PDF filename
     * @return string|false Path to combined PDF or false on failure
     */
    public function combineCertificates(array $trainings, $outputFilename = 'combined_training_certificates.pdf')
    {
        error_log('DEBUG: combineCertificates called with ' . count($trainings) . ' trainings');
        
        if (empty($trainings)) {
            error_log('ERROR: No trainings provided to combineCertificates');
            return false;
        }
        
        // Filter trainings that have certificates
        $trainingsWithCerts = array_filter($trainings, function($training) {
            return !empty($training['certificate_file']);
        });
        
        error_log('DEBUG: Trainings with certificates: ' . count($trainingsWithCerts));
        
        if (empty($trainingsWithCerts)) {
            error_log('ERROR: No trainings with certificates found');
            return false;
        }
        
        // Reset FPDI for new combination
        $this->fpdi = new Fpdi();
        $hasValidCertificates = false;
        
        foreach ($trainingsWithCerts as $index => $training) {
            $certificateFile = $training['certificate_file'];
            $filePath = $this->uploadPath . $certificateFile;
            
            error_log('DEBUG: Processing certificate: ' . $certificateFile);
            
            // Check if it's a Google Drive file ID
            $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $certificateFile) && !preg_match('/^\d{10}_/', $certificateFile);
            
            if ($isGoogleDriveFile) {
                error_log('WARNING: Google Drive files cannot be imported directly. Skipping: ' . $certificateFile);
                continue;
            }
            
            // Import actual PDF file
            if (file_exists($filePath) && is_readable($filePath)) {
                try {
                    $pageCount = $this->fpdi->setSourceFile($filePath);
                    
                    if ($pageCount > 0) {
                        $hasValidCertificates = true;
                        
                        // Import all pages from this certificate
                        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                            $templateId = $this->fpdi->importPage($pageNo);
                            $size = $this->fpdi->getTemplateSize($templateId);
                            
                            // Add new page with same dimensions
                            $this->fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                            $this->fpdi->useTemplate($templateId);
                            
                            error_log('DEBUG: Imported page ' . $pageNo . ' of ' . $pageCount . ' from ' . $certificateFile);
                        }
                        
                        error_log('DEBUG: Successfully imported ' . $pageCount . ' page(s) from ' . $certificateFile);
                    }
                } catch (\Exception $e) {
                    error_log('ERROR: Failed to import certificate ' . $certificateFile . ' - ' . $e->getMessage());
                    continue;
                }
            } else {
                error_log('ERROR: Certificate file not found or not readable: ' . $filePath);
            }
        }
        
        if (!$hasValidCertificates) {
            error_log('ERROR: No valid PDF certificates were imported');
            return false;
        }
        
        // Save combined PDF
        $outputPath = $this->uploadPath . $outputFilename;
        $pdfContent = $this->fpdi->Output('S');
        
        $result = file_put_contents($outputPath, $pdfContent);
        
        if ($result === false) {
            error_log('ERROR: Failed to save combined training certificates PDF');
            return false;
        }
        
        error_log('DEBUG: Combined training certificates saved to: ' . $outputPath . ', Size: ' . $result . ' bytes');
        return $outputFilename;
    }
    
    /**
     * Get combined PDF path for a specific application
     * This method checks if combined PDF already exists, otherwise creates it
     */
    public function getCombinedCertificatePath(int $applicationId, array $trainings)
    {
        error_log('DEBUG: getCombinedCertificatePath called with application ID: ' . $applicationId);
        error_log('DEBUG: Number of trainings passed: ' . count($trainings));
        
        // Generate unique filename based on application ID and timestamp hash
        $trainingIds = array_column($trainings, 'id_application_trainings');
        sort($trainingIds);
        $hash = md5(implode('_', $trainingIds));
        $outputFilename = 'combined_training_' . $applicationId . '_' . substr($hash, 0, 8) . '.pdf';
        
        error_log('DEBUG: Generated output filename: ' . $outputFilename);
        
        $fullPath = $this->uploadPath . $outputFilename;
        
        // Check if combined PDF already exists and is recent (less than 1 hour old)
        if (file_exists($fullPath) && (time() - filemtime($fullPath) < 3600)) {
            error_log('DEBUG: Using cached combined training certificate: ' . $outputFilename);
            return $outputFilename;
        }
        
        // Generate new combined PDF
        error_log('DEBUG: Generating new combined training certificate PDF');
        $result = $this->combineCertificates($trainings, $outputFilename);
        
        if ($result) {
            error_log('DEBUG: Successfully generated: ' . $result);
        } else {
            error_log('ERROR: Failed to generate combined PDF');
        }
        
        return $result;
    }
}
