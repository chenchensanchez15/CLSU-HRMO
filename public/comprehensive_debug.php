<?php
// Comprehensive Debug - Current Database State
echo "<h1>Comprehensive Debug - Current Database State</h1>";

// Database connection
$host = 'localhost';
$dbname = 'hrmo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Connection: ✅ Successful</h2>";
    
    // Check all tables for file references
    echo "<h3>All File References in Database:</h3>";
    
    // Application Documents
    echo "<h4>1. Application Documents Table:</h4>";
    $stmt = $pdo->query("SELECT * FROM application_documents");
    $appDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($appDocs)) {
        echo "<p>No records found in application_documents table</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>App ID</th><th>PDS</th><th>Performance</th><th>Resume</th><th>TOR</th><th>Diploma</th></tr>";
        foreach ($appDocs as $doc) {
            echo "<tr>";
            echo "<td>{$doc['job_application_id']}</td>";
            echo "<td>" . formatFileDisplay($doc['pds']) . "</td>";
            echo "<td>" . formatFileDisplay($doc['performance_rating']) . "</td>";
            echo "<td>" . formatFileDisplay($doc['resume']) . "</td>";
            echo "<td>" . formatFileDisplay($doc['tor']) . "</td>";
            echo "<td>" . formatFileDisplay($doc['diploma']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Applicant Documents
    echo "<h4>2. Applicant Documents Table:</h4>";
    $stmt = $pdo->query("SELECT * FROM applicant_documents");
    $applicantDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($applicantDocs)) {
        echo "<p>No records found in applicant_documents table</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>User ID</th><th>Doc Type</th><th>Filename</th><th>Type</th><th>Test URL</th></tr>";
        foreach ($applicantDocs as $doc) {
            $fileType = isGoogleDriveFile($doc['filename']) ? 'Google Drive' : 'Local';
            $color = isGoogleDriveFile($doc['filename']) ? 'green' : 'blue';
            $testUrl = isGoogleDriveFile($doc['filename']) ? 
                "https://drive.google.com/uc?id=" . $doc['filename'] : 
                "Local file - no direct URL";
            
            echo "<tr>";
            echo "<td>{$doc['user_id']}</td>";
            echo "<td>{$doc['document_type_id']}</td>";
            echo "<td>{$doc['filename']}</td>";
            echo "<td style='color:$color'>$fileType</td>";
            echo "<td><a href='$testUrl' target='_blank'>Test Link</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Training Certificates
    echo "<h4>3. Training Certificates:</h4>";
    $stmt = $pdo->query("SELECT * FROM applicant_trainings WHERE certificate_file IS NOT NULL AND certificate_file != ''");
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($trainings)) {
        echo "<p>No training certificates found</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Training ID</th><th>Certificate File</th><th>Type</th><th>Test URL</th></tr>";
        foreach ($trainings as $training) {
            $fileType = isGoogleDriveFile($training['certificate_file']) ? 'Google Drive' : 'Local';
            $color = isGoogleDriveFile($training['certificate_file']) ? 'green' : 'blue';
            $testUrl = isGoogleDriveFile($training['certificate_file']) ? 
                "https://drive.google.com/uc?id=" . $training['certificate_file'] : 
                "Local file - no direct URL";
            
            echo "<tr>";
            echo "<td>{$training['id_applicant_training']}</td>";
            echo "<td>{$training['certificate_file']}</td>";
            echo "<td style='color:$color'>$fileType</td>";
            echo "<td><a href='$testUrl' target='_blank'>Test Link</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check if there are any PDS documents specifically
    echo "<h4>4. PDS (Personal Data Sheet) Documents:</h4>";
    $stmt = $pdo->query("SELECT * FROM application_documents WHERE pds IS NOT NULL AND pds != ''");
    $pdsDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($pdsDocs)) {
        echo "<p>No PDS documents found in application_documents table</p>";
    } else {
        echo "<p>Found PDS documents:</p>";
        foreach ($pdsDocs as $doc) {
            echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd;'>";
            echo "<strong>Application ID:</strong> {$doc['job_application_id']}<br>";
            echo "<strong>PDS File:</strong> {$doc['pds']}<br>";
            echo "<strong>Type:</strong> " . (isGoogleDriveFile($doc['pds']) ? 'Google Drive' : 'Local') . "<br>";
            if (isGoogleDriveFile($doc['pds'])) {
                $pdsUrl = "https://drive.google.com/uc?id=" . $doc['pds'];
                echo "<strong>Test URL:</strong> <a href='$pdsUrl' target='_blank'>$pdsUrl</a>";
            }
            echo "</div>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

function isGoogleDriveFile($filename) {
    return preg_match('/^[a-zA-Z0-9_-]{28,33}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
}

function formatFileDisplay($filename) {
    if (empty($filename)) {
        return "<span style='color: gray;'>No file</span>";
    }
    
    $fileType = isGoogleDriveFile($filename) ? 'Google Drive' : 'Local';
    $color = isGoogleDriveFile($filename) ? 'green' : 'blue';
    
    return "<div>
                <div><strong>$filename</strong></div>
                <div style='color:$color; font-size: 0.9em;'>$fileType</div>
            </div>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Check if there are any PDS documents in your database</li>";
echo "<li>Test the direct Google Drive URLs shown above</li>";
echo "<li>If direct URLs work but 'View Document' doesn't, the issue is in your application's frontend</li>";
echo "<li>Check browser console for JavaScript errors when clicking 'View Document'</li>";
echo "</ol>";