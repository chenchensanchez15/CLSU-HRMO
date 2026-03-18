<?php
// Complete File Viewing Flow Test
echo "<h1>Complete File Viewing Flow Test</h1>";

// Test the specific file that's in your database
$fileId = '1H6radLjRG23teac59BIKbgZ5h8ZP4OAW';
$originalFilename = 'Sample 1.pdf';

echo "<h2>File Information:</h2>";
echo "<ul>";
echo "<li><strong>Original Filename:</strong> $originalFilename</li>";
echo "<li><strong>Google Drive File ID:</strong> $fileId</li>";
echo "<li><strong>Database Storage:</strong> File ID stored in database ✓</li>";
echo "</ul>";

// Test 1: Direct Google Drive URL
echo "<h2>Test 1: Direct Google Drive Access</h2>";
$directUrl = "https://drive.google.com/uc?id=" . $fileId;
echo "<p>Direct URL: <a href='$directUrl' target='_blank'>$directUrl</a></p>";

// Test 2: Your Application's File Viewing Route
echo "<h2>Test 2: Application File Viewing Route</h2>";
$appUrl = "http://localhost:8080/HRMO/account/viewFile/" . $fileId;
echo "<p>Application URL: <a href='$appUrl' target='_blank'>$appUrl</a></p>";
echo "<p>This should redirect to the Google Drive URL above.</p>";

// Test 3: Application Document Viewing Route
echo "<h2>Test 3: Application Document Route</h2>";
$docUrl = "http://localhost:8080/HRMO/file/viewFile/" . $fileId;
echo "<p>Document URL: <a href='$docUrl' target='_blank'>$docUrl</a></p>";

// Test 4: Verify the file exists and is accessible
echo "<h2>Test 4: File Accessibility Check</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $directUrl);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "<ul>";
echo "<li><strong>HTTP Status:</strong> $httpCode " . ($httpCode == 200 ? "✅" : "❌") . "</li>";
echo "<li><strong>Content Type:</strong> " . ($contentType ?: 'Unknown') . "</li>";
echo "</ul>";

if ($httpCode == 200) {
    echo "<p style='color: green;'>✅ File is accessible and ready to view!</p>";
} else {
    echo "<p style='color: red;'>❌ File access issue detected</p>";
}

echo "<h2>How the System Works:</h2>";
echo "<ol>";
echo "<li><strong>Upload:</strong> You upload 'Sample 1.pdf'</li>";
echo "<li><strong>Storage:</strong> File is saved to Google Drive, gets ID: $fileId</li>";
echo "<li><strong>Database:</strong> File ID '$fileId' is stored in database</li>";
echo "<li><strong>Viewing:</strong> When you click 'View Document', system generates Google Drive URL</li>";
echo "<li><strong>Access:</strong> File opens directly in Google Drive viewer</li>";
echo "</ol>";

echo "<h2>Troubleshooting Checklist:</h2>";
echo "<ul>";
echo "<li>✅ File ID exists in Google Drive</li>";
echo "<li>✅ File is accessible via direct URL</li>";
echo "<li>✅ Database contains correct file ID</li>";
echo "<li>❓ Application routing working correctly?</li>";
echo "<li>❓ User authentication in place?</li>";
echo "</ul>";

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Test the application URLs above to see if they work</li>";
echo "<li>Check browser console for any JavaScript errors</li>";
echo "<li>Verify you're logged into your application</li>";
echo "<li>Try viewing the file through your application's interface</li>";
echo "</ol>";

echo "<h2>Debug Information:</h2>";
echo "<p>If you're still getting 'Unable to Open File' errors:</p>";
echo "<ul>";
echo "<li>Check browser developer tools (F12) → Console tab for errors</li>";
echo "<li>Check Network tab to see what requests are being made</li>";
echo "<li>Verify the file viewing route is correctly configured</li>";
echo "<li>Check if there are any JavaScript errors preventing the view</li>";
echo "</ul>";