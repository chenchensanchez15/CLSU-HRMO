<?php
// Test Specific Google Drive File
echo "<h1>Test Specific Google Drive File</h1>";

$fileId = '1H6radLjRG23teac59BIKbgZ5h8ZP4OAW';
$url = "https://drive.google.com/uc?id=" . $fileId;

echo "<h2>Testing File ID: $fileId</h2>";
echo "<p>Generated URL: <a href='$url' target='_blank'>$url</a></p>";

// Try to check if the file exists by making a HEAD request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "<h3>File Status Check:</h3>";
echo "<ul>";
echo "<li><strong>HTTP Status Code:</strong> $httpCode</li>";
echo "<li><strong>Content Type:</strong> " . ($contentType ?: 'Unknown') . "</li>";
echo "</ul>";

if ($httpCode == 200) {
    echo "<p style='color: green;'>✅ File is accessible!</p>";
    echo "<p><a href='$url' target='_blank' style='font-size: 18px; padding: 10px; background: #4285f4; color: white; text-decoration: none; border-radius: 5px;'>Click to View File</a></p>";
} elseif ($httpCode == 404) {
    echo "<p style='color: red;'>❌ File not found (404) - The file ID doesn't exist in Google Drive</p>";
} elseif ($httpCode == 403) {
    echo "<p style='color: orange;'>⚠️ Access denied (403) - The file exists but you don't have permission to view it</p>";
    echo "<p>Possible solutions:</p>";
    echo "<ul>";
    echo "<li>Check if the file is shared with 'Anyone with the link' in Google Drive</li>";
    echo "<li>Verify the file exists in your Google Drive folder</li>";
    echo "<li>Re-authenticate with Google OAuth</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Unexpected response: HTTP $httpCode</p>";
}

echo "<h2>How to Fix Access Issues:</h2>";
echo "<ol>";
echo "<li><strong>In Google Drive:</strong> Find the file with ID: $fileId</li>";
echo "<li><strong>Right-click → Share → Get link</strong></li>";
echo "<li><strong>Change permissions to:</strong> 'Anyone with the link can view'</li>";
echo "<li><strong>Copy the sharing link</strong> and test it</li>";
echo "</ol>";

echo "<h2>Alternative Test:</h2>";
echo "<p>If the above doesn't work, try accessing the file directly in Google Drive:</p>";
echo "<p><a href='https://drive.google.com/file/d/$fileId/view' target='_blank'>https://drive.google.com/file/d/$fileId/view</a></p>";