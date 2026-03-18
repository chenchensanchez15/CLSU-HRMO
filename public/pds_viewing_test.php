<?php
// PDS Document Viewing Test
echo "<h1>PDS Document Viewing Test</h1>";

// Your specific PDS document information
$userId = 47;
$documentTypeId = 1; // PDS
$fileId = '1bZ85fngmCXNYdri5MvZRGO6NF1L-sp0x';
$documentName = 'Personal Data Sheet (PDS)';

echo "<h2>Document Information:</h2>";
echo "<ul>";
echo "<li><strong>User ID:</strong> $userId</li>";
echo "<li><strong>Document Type:</strong> $documentTypeId ($documentName)</li>";
echo "<li><strong>File ID:</strong> $fileId</li>";
echo "<li><strong>Storage Type:</strong> Google Drive</li>";
echo "</ul>";

// Test URLs for this specific document
$testUrls = [
    "Direct Google Drive URL" => "https://drive.google.com/uc?id=" . $fileId,
    "Account View Route" => "http://localhost:8080/HRMO/account/viewFile/" . $fileId,
    "File View Route" => "http://localhost:8080/HRMO/file/viewFile/" . $fileId
];

echo "<h2>Test URLs for Your PDS:</h2>";
foreach ($testUrls as $name => $url) {
    echo "<div style='margin: 15px 0; padding: 15px; border: 2px solid #4285f4; border-radius: 8px; background: #f8f9fa;'>";
    echo "<strong style='color: #4285f4;'>$name:</strong><br>";
    echo "<a href='$url' target='_blank' style='font-size: 16px; color: #4285f4; text-decoration: underline; font-weight: bold;'>$url</a>";
    echo "</div>";
}

// Verify the file exists
echo "<h2>File Verification:</h2>";
$directUrl = "https://drive.google.com/uc?id=" . $fileId;
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
echo "<li><strong>HTTP Status:</strong> $httpCode " . ($httpCode == 200 ? "✅ Accessible" : "❌ Issue") . "</li>";
echo "<li><strong>Content Type:</strong> " . ($contentType ?: 'Unknown') . "</li>";
echo "</ul>";

if ($httpCode == 200) {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 15px 0;'>";
    echo "<h3 style='color: #155724;'>✅ File is Accessible!</h3>";
    echo "<p>The PDS file exists and is accessible via Google Drive.</p>";
    echo "<p><strong>Direct Access Link:</strong> <a href='$directUrl' target='_blank' style='color: #4285f4; font-weight: bold;'>View PDS Directly</a></p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 15px 0;'>";
    echo "<h3 style='color: #721c24;'>❌ File Access Issue</h3>";
    echo "<p>There's a problem accessing the file. HTTP Status: $httpCode</p>";
    echo "</div>";
}

echo "<h2>Debug Checklist:</h2>";
echo "<ol>";
echo "<li><strong>✅ Database has PDS record</strong> - Confirmed</li>";
echo "<li><strong>✅ File ID exists</strong> - $fileId</li>";
echo "<li><strong>❓ Application routing working?</strong> - Test the URLs above</li>";
echo "<li><strong>❓ User authentication?</strong> - Make sure you're logged in</li>";
echo "</ol>";

echo "<h2>Debugging Steps:</h2>";
echo "<ol>";
echo "<li><strong>Test the direct URLs above</strong> - Click each one to see which works</li>";
echo "<li><strong>Open browser developer tools</strong> (F12) when clicking 'View Document' in your app</li>";
echo "<li><strong>Check Console tab</strong> for JavaScript errors</li>";
echo "<li><strong>Check Network tab</strong> to see what requests are made</li>";
echo "<li><strong>Verify you're logged in</strong> as user ID 47</li>";
echo "</ol>";

echo "<h2>Expected Behavior:</h2>";
echo "<p>When you click 'View Document' for PDS:</p>";
echo "<ol>";
echo "<li>Application should find document type 1 for user 47</li>";
echo "<li>Retrieve file ID: 1bZ85fngmCXNYdri5MvZRGO6NF1L-sp0x</li>";
echo "<li>Generate Google Drive URL</li>";
echo "<li>Redirect to: https://drive.google.com/uc?id=1bZ85fngmCXNYdri5MvZRGO6NF1L-sp0x</li>";
echo "<li>File should display in browser</li>";
echo "</ol>";

echo "<h2>If Still Getting 'Unable to Open File':</h2>";
echo "<p>The issue is likely in your application's frontend code. Check:</p>";
echo "<ul>";
echo "<li>JavaScript error handling</li>";
echo "<li>User authentication context</li>";
echo "<li>Route parameter passing</li>";
echo "<li>File ID retrieval logic</li>";
echo "</ul>";