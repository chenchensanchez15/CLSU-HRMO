<?php
// Simple Google Drive URL Test
echo "<h1>Google Drive URL Test</h1>";

// Test the URL generation directly
function generateGoogleDriveUrl($fileId) {
    return "https://drive.google.com/uc?id={$fileId}";
}

echo "<h2>Test URL Generation:</h2>";

$testFileIds = [
    '1abcdefghijklmnopqrstuvwxyz123456789',
    '1samplefileid1234567890123456789',
    '1testdrivefile1234567890123456789'
];

foreach ($testFileIds as $fileId) {
    $url = generateGoogleDriveUrl($fileId);
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<strong>File ID:</strong> $fileId<br>";
    echo "<strong>Generated URL:</strong> <a href='$url' target='_blank'>$url</a>";
    echo "</div>";
}

echo "<h2>File Detection Logic Test:</h2>";

function isGoogleDriveFile($filename) {
    // Check if the filename is a Google Drive file ID (typically 28-33 characters long)
    // Local uploaded files have timestamp prefixes like "1772469100_filename.pdf"
    return preg_match('/^[a-zA-Z0-9_-]{28,33}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
}

$testFilenames = [
    '1abcdefghijklmnopqrstuvwxyz123456789', // Google Drive file ID
    '1772469100_fe188c0a64f1abd7742c.pdf', // Local file
    '1samplefileid1234567890123456789',    // Google Drive file ID
    'test_document.pdf',                   // Local file
    '1testdrivefile1234567890123456789'    // Google Drive file ID
];

foreach ($testFilenames as $filename) {
    $isGDrive = isGoogleDriveFile($filename);
    $type = $isGDrive ? 'Google Drive File' : 'Local File';
    $color = $isGDrive ? 'green' : 'blue';
    echo "<div style='margin: 5px 0; padding: 5px; border-left: 3px solid $color;'>";
    echo "<strong>$filename</strong> → <span style='color: $color;'>$type</span>";
    echo "</div>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Check your database to see what file IDs are actually stored</li>";
echo "<li>Verify these file IDs exist in your Google Drive</li>";
echo "<li>Test the 'View Document' buttons in your application</li>";
echo "</ol>";

echo "<h2>Common Issues:</h2>";
echo "<ul>";
echo "<li><strong>'Unable to Open File'</strong> - Usually means the file ID doesn't exist in Google Drive</li>";
echo "<li><strong>Authentication Required</strong> - Google Drive files may need proper sharing permissions</li>";
echo "<li><strong>Invalid File ID</strong> - The stored ID might be incorrect or corrupted</li>";
echo "</ul>";