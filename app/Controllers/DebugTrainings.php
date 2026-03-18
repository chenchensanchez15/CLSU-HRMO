<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class DebugTrainings extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get all applications with trainings
        $applications = $db->table('job_applications')
                          ->select('id_job_application, job_vacancy_id, user_id')
                          ->get()
                          ->getResultArray();
        
        echo "<h1>Debug: Trainings Data</h1>";
        echo "<p>Checking all applications for training certificates...</p><hr>";
        
        foreach ($applications as $app) {
            $appId = $app['id_job_application'];
            
            // Fetch trainings for this application
            $trainings = $db->table('application_trainings at')
                           ->join('lib_training_category tc', 'at.training_category_id = tc.id_training_category', 'left')
                           ->select('at.id_application_trainings, at.training_name, at.certificate_file, tc.training_category_name')
                           ->where(['at.job_application_id' => $appId])
                           ->get()
                           ->getResultArray();
            
            if (!empty($trainings)) {
                echo "<h2>Application #{$appId}</h2>";
                echo "<p><strong>Found " . count($trainings) . " training(s)</strong></p>";
                
                echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
                echo "<tr><th>#</th><th>Training Name</th><th>Certificate File</th><th>Has File?</th></tr>";
                
                $withFiles = 0;
                foreach ($trainings as $idx => $training) {
                    $hasFile = !empty($training['certificate_file']) ? 'YES' : 'NO';
                    if ($hasFile === 'YES') $withFiles++;
                    
                    echo "<tr>";
                    echo "<td>" . ($idx + 1) . "</td>";
                    echo "<td>" . esc($training['training_name']) . "</td>";
                    echo "<td>" . esc(substr($training['certificate_file'] ?? '', 0, 50)) . "</td>";
                    echo "<td style='text-align: center;'>" . $hasFile . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                echo "<p>Trainings with certificates: <strong>{$withFiles}</strong></p>";
                
                // Test combiner
                echo "<h3>Testing Combiner</h3>";
                require_once APPPATH . 'Libraries/TrainingCertificateCombiner.php';
                $combiner = new \App\Libraries\TrainingCertificateCombiner();
                
                $result = $combiner->combineCertificates($trainings, 'test_combined_app_' . $appId . '.pdf');
                
                if ($result) {
                    echo "<p style='color: green;'>✓ Combined PDF created: {$result}</p>";
                    $fullPath = WRITEPATH . 'uploads/trainings/' . $result;
                    if (file_exists($fullPath)) {
                        echo "<p>✓ File exists (" . filesize($fullPath) . " bytes)</p>";
                        echo "<p><a href='" . site_url('applications/viewTrainingCertificate/' . $appId . '/' . $result) . "' target='_blank'>View Combined PDF</a></p>";
                    } else {
                        echo "<p style='color: red;'>✗ File not found!</p>";
                    }
                } else {
                    echo "<p style='color: red;'>✗ Combiner returned false</p>";
                }
                
                echo "<hr>";
            }
        }
        
        echo "<h2>Summary</h2>";
        echo "<p>If you see applications with multiple trainings but the combiner only creates 1-page PDFs, check:</p>";
        echo "<ol>";
        echo "<li>Are the certificate files actually present in writable/uploads/trainings/?</li>";
        echo "<li>Are the certificate file paths correct in the database?</li>";
        echo "<li>Check the logs at writable/logs/ for detailed error messages</li>";
        echo "</ol>";
    }
}
