<?php
// Simple test to check trainings data
$db = \Config\Database::connect();

// Test with a specific application ID (change this)
$application_id = 1; // CHANGE THIS TO YOUR APPLICATION ID

echo "<h2>Testing Trainings Data for Application #{$application_id}</h2>";

// Fetch trainings
$trainings = $db->table('application_trainings at')
                ->join('lib_training_category tc', 'at.training_category_id = tc.id_training_category', 'left')
                ->select('at.id_application_trainings, at.training_name, at.date_from, at.date_to, at.training_facilitator, at.training_hours, at.training_sponsor, at.training_remarks, at.certificate_file, tc.training_category_name')
                ->where(['at.job_application_id' => $application_id])
                ->orderBy('at.date_from', 'DESC')
                ->get()
                ->getResultArray();

echo "<h3>Found " . count($trainings) . " training(s)</h3>";

if (!empty($trainings)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>
            <th>#</th>
            <th>ID</th>
            <th>Training Name</th>
            <th>Certificate File</th>
            <th>Category</th>
            <th>Date From</th>
            <th>Date To</th>
          </tr>";
    
    foreach ($trainings as $idx => $training) {
        echo "<tr>";
        echo "<td>" . ($idx + 1) . "</td>";
        echo "<td>" . ($training['id_application_trainings'] ?? 'N/A') . "</td>";
        echo "<td>" . esc($training['training_name']) . "</td>";
        echo "<td>" . esc($training['certificate_file'] ?? 'NO FILE') . "</td>";
        echo "<td>" . esc($training['training_category_name'] ?? 'N/A') . "</td>";
        echo "<td>" . esc($training['date_from']) . "</td>";
        echo "<td>" . esc($training['date_to']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test combiner
    echo "<h3>Testing TrainingCertificateCombiner</h3>";
    require_once 'vendor/autoload.php';
    $combiner = new \App\Libraries\TrainingCertificateCombiner();
    $result = $combiner->getCombinedCertificatePath($application_id, $trainings);
    
    if ($result) {
        echo "<p style='color: green;'>✓ Combined PDF generated successfully: <strong>{$result}</strong></p>";
        
        $fullPath = WRITEPATH . 'uploads/trainings/' . $result;
        if (file_exists($fullPath)) {
            echo "<p style='color: green;'>✓ File exists at: {$fullPath}</p>";
            echo "<p>File size: " . filesize($fullPath) . " bytes</p>";
            
            // Provide download link
            echo "<p><a href='" . site_url('applications/viewTrainingCertificate/' . $application_id . '/' . $result) . "' target='_blank' style='background: #0B6B3A; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Combined PDF</a></p>";
        } else {
            echo "<p style='color: red;'>✗ File NOT FOUND at expected path</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Failed to generate combined PDF</p>";
    }
} else {
    echo "<p style='color: red;'>No trainings found for this application!</p>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> Check logs at <code>writable/logs/log-*.php</code> for detailed debugging information.</p>";
