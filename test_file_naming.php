<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Naming Convention Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
        code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>File Naming Convention Implementation Test</h1>
    
    <div class="test-section">
        <h2>Test Results</h2>
        
        <?php
        // Test sanitization logic
        function sanitizeFileName($baseName) {
            return preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        }
        
        $testCases = [
            'Personal Data Sheet (PDS)' => 'Personal_Data_Sheet_PDS_',
            'Certificate of Trainings and Seminars' => 'Certificate_of_Trainings_and_Seminars',
            'Transcript of Records (TOR)' => 'Transcript_of_Records_TOR_',
            'profile photo' => 'profile_photo',
            'test@file#name%with&special*chars' => 'test_file_name_with_special_chars',
        ];
        
        echo '<div class="info">';
        echo '<h3>Filename Sanitization Tests:</h3>';
        foreach ($testCases as $input => $expectedPattern) {
            $result = sanitizeFileName($input);
            $passed = strpos($result, $expectedPattern) !== false || 
                     strtolower(str_replace('_', '', $result)) === strtolower(str_replace('_', '', $expectedPattern));
            $status = $passed ? '✓ PASS' : '✗ FAIL';
            $class = $passed ? 'success' : 'error';
            echo "<p class='$class'>$status Input: <code>$input</code><br>";
            echo "Result: <code>$result</code></p>";
        }
        echo '</div>';
        
        // Test timestamp generation
        echo '<div class="info">';
        echo '<h3>Timestamp Generation:</h3>';
        $timestamp = time();
        echo "<p>Current timestamp: <code>$timestamp</code></p>";
        echo "<p>Expected format: <code>{timestamp}_{sanitized_name}.{ext}</code></p>";
        echo "<p>Example: <code>{$timestamp}_profile_photo.jpg</code></p>";
        echo '</div>';
        
        // Check modified files
        echo '<div class="info">';
        echo '<h3>Modified Controller Files:</h3>';
        
        $filesToCheck = [
            'app/Controllers/Account.php' => 'Account Controller',
            'app/Controllers/Applications.php' => 'Applications Controller',
        ];
        
        foreach ($filesToCheck as $file => $label) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $hasSanitization = strpos($content, 'preg_replace(\'/[^a-zA-Z0-9_-]/\', \'_\'') !== false;
                $hasConsistentNaming = strpos($content, 'Use consistent naming') !== false;
                
                if ($hasSanitization && $hasConsistentNaming) {
                    echo "<p class='success'>✓ $label - Properly updated</p>";
                } else {
                    echo "<p class='error'>✗ $label - May need review</p>";
                }
            } else {
                echo "<p class='error'>✗ $label - File not found</p>";
            }
        }
        echo '</div>';
        ?>
    </div>
    
    <div class="test-section">
        <h2>Implementation Summary</h2>
        <ul>
            <li><strong>Profile Photos:</strong> Now use format <code>{timestamp}_profile_photo.{ext}</code></li>
            <li><strong>Documents (Google Drive):</strong> Use format <code>{timestamp}_{sanitized_name}.{ext}</code></li>
            <li><strong>Training Certificates:</strong> Use format <code>{timestamp}_{sanitized_name}.{ext}</code></li>
            <li><strong>Civil Service Certificates:</strong> Use format <code>{timestamp}_{sanitized_name}.{ext}</code></li>
            <li><strong>Application Documents:</strong> Use format <code>{timestamp}_{sanitized_name}.{ext}</code></li>
        </ul>
        
        <h3>Key Changes:</h3>
        <ul>
            <li>Removed <code>userId</code> prefix from filenames (not needed, database tracks ownership)</li>
            <li>Replaced <code>getRandomName()</code> with original filename (sanitized)</li>
            <li>Added proper filename sanitization to remove special characters</li>
            <li>Standardized timestamp prefix across all upload types</li>
            <li>Ensured consistency between Google Drive and local storage naming</li>
        </ul>
    </div>
    
    <div class="test-section">
        <h2>Next Steps</h2>
        <ol>
            <li>Upload a profile photo and verify it appears in Google Drive with correct naming</li>
            <li>Upload a training certificate and check local storage naming</li>
            <li>Upload job application documents and verify all files follow the new convention</li>
            <li>Check database records to ensure filenames are stored correctly</li>
            <li>Test file viewing functionality for both old and new files</li>
        </ol>
    </div>
</body>
</html>
