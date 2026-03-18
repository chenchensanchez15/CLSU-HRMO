<?php
// Debug File Viewing - Step by Step
echo "<h1>Debug File Viewing - Step by Step</h1>";

// Get the specific file information from your database
$fileId = '1H6radLjRG23teac59BIKbgZ5h8ZP4OAW';
$testUrls = [
    "Direct Google Drive URL" => "https://drive.google.com/uc?id=" . $fileId,
    "Account View Route" => "http://localhost:8080/HRMO/account/viewFile/" . $fileId,
    "File View Route" => "http://localhost:8080/HRMO/file/viewFile/" . $fileId
];

echo "<h2>File Information:</h2>";
echo "<p><strong>File ID:</strong> $fileId</p>";
echo "<p><strong>Original Name:</strong> Sample 1.pdf</p>";

echo "<h2>Test URLs:</h2>";
foreach ($testUrls as $name => $url) {
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<strong>$name:</strong><br>";
    echo "<a href='$url' target='_blank' style='color: #4285f4;'>$url</a>";
    echo "</div>";
}

echo "<h2>Debug Steps:</h2>";
echo "<ol>";
echo "<li><strong>Test each URL above individually</strong> - Click each link to see which one works</li>";
echo "<li><strong>Check browser console</strong> - Press F12 → Console tab when clicking 'View Document' in your app</li>";
echo "<li><strong>Check network requests</strong> - Press F12 → Network tab to see what requests are made</li>";
echo "</ol>";

echo "<h2>Common Issues and Solutions:</h2>";

echo "<h3>1. JavaScript Error</h3>";
echo "<p>If direct URLs work but 'View Document' button doesn't:</p>";
echo "<ul>";
echo "<li>Open browser developer tools (F12)</li>";
echo "<li>Click the 'View Document' button</li>";
echo "<li>Check Console tab for error messages</li>";
echo "<li>Look for JavaScript errors or failed requests</li>";
echo "</ul>";

echo "<h3>2. Authentication Issue</h3>";
echo "<p>Make sure you're logged into your application when testing.</p>";

echo "<h3>3. Route Configuration</h3>";
echo "<p>Verify these routes exist in your application:</p>";
echo "<ul>";
echo "<li><code>/account/viewFile/{fileId}</code></li>";
echo "<li><code>/file/viewFile/{fileId}</code></li>";
echo "</ul>";

echo "<h2>What to Report Back:</h2>";
echo "<p>After testing, please let me know:</p>";
echo "<ol>";
echo "<li>Which of the test URLs above work?</li>";
echo "<li>What error messages appear in the browser console?</li>";
echo "<li>What network requests are shown in the Network tab?</li>";
echo "<li>Are you logged into your application when testing?</li>";
echo "</ol>";

echo "<h2>Quick Test:</h2>";
echo "<p>Click this link to verify the file is accessible:</p>";
echo "<p><a href='https://drive.google.com/uc?id=$fileId' target='_blank' style='font-size: 18px; padding: 10px; background: #4285f4; color: white; text-decoration: none; border-radius: 5px;'>View Sample 1.pdf Directly</a></p>";
echo "<p>If this works but 'View Document' doesn't, the issue is definitely in your application's frontend code.</p>";