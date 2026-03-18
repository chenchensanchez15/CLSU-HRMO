<?php
// Simple Google Drive Test
// This is a simplified version that doesn't rely on the full Google API client

echo "Testing if we can use Google Drive API with a simple HTTP request approach\n\n";

// Check if we have the required extensions
$required_extensions = ['curl', 'json'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo "Missing required PHP extensions: " . implode(', ', $missing_extensions) . "\n";
    echo "Please enable these extensions in your php.ini file\n";
    exit(1);
}

echo "All required extensions are available\n";

// Test if we can make a simple HTTP request to Google Drive API
$test_url = "https://www.googleapis.com/discovery/v1/apis/drive/v3/rest";

echo "Testing connection to Google Drive API...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $test_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    echo "✓ Successfully connected to Google Drive API\n";
    echo "✓ Basic connectivity test passed\n";
    echo "\nYou can proceed with setting up Google Drive integration!\n";
} else {
    echo "✗ Failed to connect to Google Drive API\n";
    echo "HTTP Code: $http_code\n";
    echo "Response: $response\n";
}

echo "\nNext steps:\n";
echo "1. Create a Google Cloud Project\n";
echo "2. Enable the Google Drive API\n";
echo "3. Create a Service Account\n";
echo "4. Download the JSON key file\n";
echo "5. Place it in: writable/credentials/google_credentials.json\n";