<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * TrainingCertificateCombiner
 * 
 * Combines multiple training certificate images/PDFs into a single PDF document
 */
class TrainingCertificateCombiner
{
    protected $dompdf;
    protected $uploadPath;
    
    public function __construct()
    {
        $this->uploadPath = WRITEPATH . 'uploads/trainings/';
        
        // Configure DOMPDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultPaperSize', 'letter');
        $options->set('defaultOrientation', 'portrait');
        
        $this->dompdf = new Dompdf($options);
    }
    
    /**
     * Combine multiple training certificates into one PDF
     * 
     * @param array $trainings Array of training records with certificate_file paths
     * @param string $outputFilename Output PDF filename
     * @return string|false Path to combined PDF or false on failure
     */
    public function combineCertificates(array $trainings, $outputFilename = 'combined_training_certificates.pdf')
    {
        log_message('debug', 'combineCertificates called with ' . count($trainings) . ' trainings');
        
        if (empty($trainings)) {
            log_message('error', 'No trainings provided to combineCertificates');
            return false;
        }
        
        // Filter trainings that have certificates
        $trainingsWithCerts = array_filter($trainings, function($training) {
            return !empty($training['certificate_file']);
        });
        
        log_message('debug', 'Trainings with certificates: ' . count($trainingsWithCerts));
        
        if (empty($trainingsWithCerts)) {
            log_message('error', 'No trainings with certificates found');
            return false;
        }
        
        // Build HTML content for combined PDF
        log_message('debug', 'Building HTML for ' . count($trainingsWithCerts) . ' certificate(s)');
        $html = $this->buildCombinedHTML($trainingsWithCerts);
        
        // Load HTML into DOMPDF
        $this->dompdf->loadHtml($html);
        
        // Render PDF
        log_message('debug', 'Rendering PDF...');
        $this->dompdf->render();
        
        // Save to file
        $outputPath = $this->uploadPath . $outputFilename;
        $pdfContent = $this->dompdf->output();
        log_message('debug', 'PDF content size: ' . strlen($pdfContent) . ' bytes');
        
        $result = file_put_contents($outputPath, $pdfContent);
        
        if ($result === false) {
            log_message('error', 'Failed to save combined training certificates PDF');
            return false;
        }
        
        log_message('debug', 'Combined training certificates saved to: ' . $outputPath . ', Size: ' . $result . ' bytes');
        return $outputFilename;
    }
    
    /**
     * Build HTML content for combined certificates
     */
    protected function buildCombinedHTML(array $trainings)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .certificate-page {
            page-break-after: always;
            margin-bottom: 30px;
        }
        .certificate-page:last-child {
            page-break-after: auto;
        }
        .certificate-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0B6B3A;
            padding-bottom: 10px;
        }
        .certificate-title {
            color: #0B6B3A;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .certificate-info {
            margin: 15px 0;
            font-size: 14px;
        }
        .info-row {
            margin: 8px 0;
        }
        .info-label {
            font-weight: bold;
            color: #333;
        }
        .certificate-image {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
            border: 1px solid #ddd;
        }
        .remarks {
            margin-top: 15px;
            font-style: italic;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>';

        $totalPages = count($trainings);
        $currentPage = 0;
        
        foreach ($trainings as $training) {
            $currentPage++;
            
            $html .= '<div class="certificate-page">';
            
            // Header
            $html .= '<div class="certificate-header">';
            $html .= '<h1 class="certificate-title">Certificate of Training</h1>';
            $html .= '</div>';
            
            // Training Information
            $html .= '<div class="certificate-info">';
            $html .= '<div class="info-row"><span class="info-label">Training Name:</span> ' . esc($training['training_name']) . '</div>';
            $html .= '<div class="info-row"><span class="info-label">Category:</span> ' . esc($training['training_category_name'] ?? 'N/A') . '</div>';
            $html .= '<div class="info-row"><span class="info-label">Date:</span> ' . esc($training['date_from']) . ' - ' . esc($training['date_to']) . '</div>';
            $html .= '<div class="info-row"><span class="info-label">Venue:</span> ' . esc($training['training_venue'] ?? 'N/A') . '</div>';
            $html .= '<div class="info-row"><span class="info-label">Facilitator:</span> ' . esc($training['training_facilitator'] ?? 'N/A') . '</div>';
            $html .= '<div class="info-row"><span class="info-label">Hours:</span> ' . esc($training['training_hours']) . ' hours</div>';
            $html .= '<div class="info-row"><span class="info-label">Sponsor:</span> ' . esc($training['training_sponsor'] ?? 'N/A') . '</div>';
            $html .= '</div>';
            
            // Certificate Image/File
            $certificateFile = $training['certificate_file'];
            if (!empty($certificateFile)) {
                // Check if it's a Google Drive file ID
                $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $certificateFile) && !preg_match('/^\d{10}_/', $certificateFile);
                
                if ($isGoogleDriveFile) {
                    // For Google Drive files, we'll show a placeholder
                    $html .= '<div style="text-align: center; padding: 40px; border: 2px dashed #ccc; margin: 20px 0;">';
                    $html .= '<p style="color: #666; font-style: italic;">Certificate stored in Google Drive</p>';
                    $html .= '<p style="font-size: 12px; color: #999;">File: ' . esc($certificateFile) . '</p>';
                    $html .= '</div>';
                } else {
                    // Local file - try to embed as base64
                    $filePath = $this->uploadPath . $certificateFile;
                    if (file_exists($filePath)) {
                        $imageData = base64_encode(file_get_contents($filePath));
                        $mimeType = mime_content_type($filePath);
                        $html .= '<img src="data:' . $mimeType . ';base64,' . $imageData . '" alt="Training Certificate" class="certificate-image" />';
                    } else {
                        $html .= '<div style="text-align: center; padding: 40px; border: 2px dashed #ccc; margin: 20px 0;">';
                        $html .= '<p style="color: #666; font-style: italic;">Certificate file not found: ' . esc($certificateFile) . '</p>';
                        $html .= '</div>';
                    }
                }
            }
            
            // Remarks if any
            if (!empty($training['training_remarks']) && strtoupper($training['training_remarks']) !== 'N/A') {
                $html .= '<div class="remarks">';
                $html .= '<strong>Remarks:</strong> ' . esc($training['training_remarks']);
                $html .= '</div>';
            }
            
            // Page break except for last page
            if ($currentPage < $totalPages) {
                $html .= '<div style="page-break-after: always;"></div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * Get combined PDF path for a specific application
     * This method checks if combined PDF already exists, otherwise creates it
     */
    public function getCombinedCertificatePath(int $applicationId, array $trainings)
    {
        log_message('debug', 'getCombinedCertificatePath called with application ID: ' . $applicationId);
        log_message('debug', 'Number of trainings passed: ' . count($trainings));
        
        // Generate unique filename based on application ID and timestamp hash
        $trainingIds = array_column($trainings, 'id_application_trainings');
        sort($trainingIds);
        $hash = md5(implode('_', $trainingIds));
        $outputFilename = 'combined_training_' . $applicationId . '_' . substr($hash, 0, 8) . '.pdf';
        
        log_message('debug', 'Generated output filename: ' . $outputFilename);
        
        $fullPath = $this->uploadPath . $outputFilename;
        
        // Check if combined PDF already exists and is recent (less than 1 hour old)
        if (file_exists($fullPath) && (time() - filemtime($fullPath) < 3600)) {
            log_message('debug', 'Using cached combined training certificate: ' . $outputFilename);
            return $outputFilename;
        }
        
        // Generate new combined PDF
        log_message('debug', 'Generating new combined training certificate PDF');
        $result = $this->combineCertificates($trainings, $outputFilename);
        
        if ($result) {
            log_message('debug', 'Successfully generated: ' . $result);
        } else {
            log_message('error', 'Failed to generate combined PDF');
        }
        
        return $result;
    }
}
