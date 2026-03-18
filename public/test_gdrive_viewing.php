<?php
// Test Google Drive Document Viewing
require_once __DIR__ . '/../vendor/autoload.php';

echo "<h1>Google Drive Document Viewing Test</h1>";

// Load environment
if (file_exists(__DIR__ . '/../.env')) {
    $env = file_get_contents(__DIR__ . '/../.env');
    $lines = explode("\n", $env);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

echo "<h2>Test Configuration:</h2>";
echo "GOOGLE_DRIVE_FOLDER_ID: <strong>" . ($_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? 'NOT SET') . "</strong><br>";

// Test Google Drive service
echo "<h2>Google Drive Service Test:</h2>";

try {
    $driveService = new \App\Libraries\GoogleDriveOAuthService();
    
    if ($driveService->isEnabled()) {
        echo "<p style='color: green;'>✅ Google Drive service is enabled and authenticated!</p>";
        
        // Test getFileUrl method
        $testFileId = '1abcdefghijklmnopqrstuvwxyz123456789'; // Sample file ID
        $testUrl = $driveService->getFileUrl($testFileId);
        echo "<p>Test Google Drive URL: <a href='$testUrl' target='_blank'>$testUrl</a></p>";
        
        echo "<h3>Test Links for Your Application:</h3>";
        echo "<p>These links will redirect to Google Drive if the file ID is valid:</p>";
        
        $testFiles = [
            '1abcdefghijklmnopqrstuvwxyz123456789',
            '1samplefileid1234567890123456789',
            '1testdrivefile1234567890123456789'
        ];
        
        foreach ($testFiles as $fileId) {
            $url = $driveService->getFileUrl($fileId);
            echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
            echo "<strong>File ID:</strong> $fileId<br>";
            echo "<strong>URL:</strong> <a href='$url' target='_blank'>$url</a>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Google Drive service is not enabled or authenticated.</p>";
        echo "<p>Please authenticate with Google first using the OAuth flow.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>How It Works:</h2>";
echo "<ol>";
echo "<li><strong>File Detection:</strong> The system detects if a stored filename is a Google Drive file ID (28-33 characters, alphanumeric with hyphens/underscores)</li>";
echo "<li><strong>Local Files:</strong> Files with timestamp prefixes like '1772469100_filename.pdf' are treated as local files</li>";
echo "<li><strong>Google Drive Files:</strong> Pure file IDs are redirected to Google Drive URLs</li>";
echo "<li><strong>URL Generation:</strong> Uses format: https://drive.google.com/uc?id=FILE_ID</li>";
echo "</ol>";

echo "<h2>Test Your Application:</h2>";
echo "<p>Visit your application and click 'View Document' buttons. If the files are stored in Google Drive, they should open directly in Google Drive.</p>";
echo "<p>If you see 'No file found' messages, the file IDs may not exist in your Google Drive or the authentication may have expired.</p>";