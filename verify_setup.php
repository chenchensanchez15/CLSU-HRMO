<?php
// Test script to verify Google Drive setup
echo "Google Drive Setup Verification\n";
echo "=================================\n\n";

// Load environment
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

echo "Current Configuration:\n";
echo "Folder ID: '" . ($_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? 'NOT SET') . "'\n";
echo "Credentials file: " . ($_ENV['GOOGLE_CREDENTIALS_PATH'] ?? 'NOT SET') . "\n";

// Check if credentials file exists
$credPath = $_ENV['GOOGLE_CREDENTIALS_PATH'] ?? 'writable/credentials/google_credentials.json';
echo "Credentials file exists: " . (file_exists($credPath) ? "YES" : "NO") . "\n";

if (file_exists($credPath)) {
    $creds = json_decode(file_get_contents($credPath), true);
    echo "Service Account Email: " . ($creds['client_email'] ?? 'NOT FOUND') . "\n";
}

echo "\nInstructions:\n";
echo "1. Go to Google Drive and create a new folder\n";
echo "2. Right-click the folder → Share\n";
echo "3. Add this email as Editor: hrmo-drive-service@clsu-online-job-application.iam.gserviceaccount.com\n";
echo "4. Copy the folder ID from the URL (after /folders/)\n";
echo "5. Update GOOGLE_DRIVE_FOLDER_ID in .env file with the correct folder ID\n";
echo "6. Run this test again\n";

echo "\nTest file for upload verification:\n";
$testFile = 'test_upload.txt';
$testContent = "Test file created at: " . date('Y-m-d H:i:s');
file_put_contents($testFile, $testContent);
echo "Created test file: $testFile\n";
echo "File size: " . filesize($testFile) . " bytes\n";
unlink($testFile);
?>