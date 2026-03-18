<?php
// Database File Check Tool
echo "<h1>Database File Check</h1>";

// Database connection (adjust credentials as needed)
$host = 'localhost';
$dbname = 'hrmo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Connected to Database</h2>";
    
    // Check application_documents table
    echo "<h3>Application Documents:</h3>";
    $stmt = $pdo->query("SELECT job_application_id, pds, performance_rating, resume, tor, diploma FROM application_documents LIMIT 10");
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($documents)) {
        echo "<p>No application documents found.</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>App ID</th><th>PDS</th><th>Performance</th><th>Resume</th><th>TOR</th><th>Diploma</th></tr>";
        foreach ($documents as $doc) {
            echo "<tr>";
            echo "<td>{$doc['job_application_id']}</td>";
            echo "<td>" . formatFileCell($doc['pds']) . "</td>";
            echo "<td>" . formatFileCell($doc['performance_rating']) . "</td>";
            echo "<td>" . formatFileCell($doc['resume']) . "</td>";
            echo "<td>" . formatFileCell($doc['tor']) . "</td>";
            echo "<td>" . formatFileCell($doc['diploma']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check applicant_documents table
    echo "<h3>Applicant Documents:</h3>";
    $stmt = $pdo->query("SELECT user_id, document_type_id, filename FROM applicant_documents LIMIT 10");
    $applicantDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($applicantDocs)) {
        echo "<p>No applicant documents found.</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>User ID</th><th>Doc Type</th><th>Filename</th><th>Type</th></tr>";
        foreach ($applicantDocs as $doc) {
            $fileType = isGoogleDriveFile($doc['filename']) ? 'Google Drive' : 'Local';
            $color = isGoogleDriveFile($doc['filename']) ? 'green' : 'blue';
            echo "<tr>";
            echo "<td>{$doc['user_id']}</td>";
            echo "<td>{$doc['document_type_id']}</td>";
            echo "<td>{$doc['filename']}</td>";
            echo "<td style='color:$color'>$fileType</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check training certificates
    echo "<h3>Training Certificates:</h3>";
    $stmt = $pdo->query("SELECT id_applicant_training, certificate_file FROM applicant_trainings WHERE certificate_file IS NOT NULL AND certificate_file != '' LIMIT 10");
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($trainings)) {
        echo "<p>No training certificates found.</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Training ID</th><th>Certificate File</th><th>Type</th></tr>";
        foreach ($trainings as $training) {
            $fileType = isGoogleDriveFile($training['certificate_file']) ? 'Google Drive' : 'Local';
            $color = isGoogleDriveFile($training['certificate_file']) ? 'green' : 'blue';
            echo "<tr>";
            echo "<td>{$training['id_applicant_training']}</td>";
            echo "<td>{$training['certificate_file']}</td>";
            echo "<td style='color:$color'>$fileType</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration.</p>";
}

function isGoogleDriveFile($filename) {
    return preg_match('/^[a-zA-Z0-9_-]{28,33}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
}

function formatFileCell($filename) {
    if (empty($filename)) {
        return "<span style='color: gray;'>No file</span>";
    }
    
    $fileType = isGoogleDriveFile($filename) ? 'Google Drive' : 'Local';
    $color = isGoogleDriveFile($filename) ? 'green' : 'blue';
    $url = isGoogleDriveFile($filename) ? "https://drive.google.com/uc?id=" . $filename : "Local file";
    
    return "<div>
                <div><strong>$filename</strong></div>
                <div style='color:$color; font-size: 0.9em;'>$fileType</div>
                <div><a href='$url' target='_blank' style='font-size: 0.8em;'>View</a></div>
            </div>";
}

echo "<h2>Analysis:</h2>";
echo "<ul>";
echo "<li><strong>Google Drive Files:</strong> 28-33 character IDs without timestamp prefix</li>";
echo "<li><strong>Local Files:</strong> Files with timestamp prefixes or other formats</li>";
echo "<li><strong>Missing Files:</strong> Empty or NULL values</li>";
echo "</ul>";

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Identify which files are Google Drive vs local</li>";
echo "<li>Verify Google Drive file IDs exist in your Google Drive</li>";
echo "<li>Check file sharing permissions in Google Drive</li>";
echo "<li>Test 'View Document' buttons with actual file IDs</li>";
echo "</ol>";