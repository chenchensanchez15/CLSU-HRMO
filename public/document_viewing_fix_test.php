<?php
// Test page to verify the document viewing fix
echo "<h1>Document Viewing Fix Test</h1>";
echo "<p>This page tests the updated 'View Document' functionality with Google Drive integration.</p>";

// Test the specific PDS file we know exists
$fileId = '1bZ85fngmCXNYdri5MvZRGO6NF1L-sp0x';

echo "<h2>Test File Information:</h2>";
echo "<p><strong>File ID:</strong> $fileId</p>";
echo "<p><strong>Document Type:</strong> PDS (Personal Data Sheet)</p>";
echo "<p><strong>User ID:</strong> 47</p>";

echo "<h2>Test Cases:</h2>";

// Test 1: Direct Google Drive URL
echo "<h3>Test 1: Direct Google Drive Preview</h3>";
$directUrl = "https://drive.google.com/file/d/$fileId/preview";
echo "<p><a href='$directUrl' target='_blank' style='font-size: 16px; padding: 10px; background: #4285f4; color: white; text-decoration: none; border-radius: 5px;'>View PDS Directly in Google Drive</a></p>";
echo "<p>This should open the document in Google Drive's preview mode.</p>";

// Test 2: Simulate the apply.php view button behavior
echo "<h3>Test 2: Apply Page View Button Simulation</h3>";
echo "<button id='testApplyBtn' class='viewFileBtn' data-file='http://localhost:8080/HRMO/file/viewFile/$fileId' style='font-size: 16px; padding: 10px; background: #34a853; color: white; border: none; border-radius: 5px; cursor: pointer;'>Test Apply View Button</button>";
echo "<p>Clicking this should detect the Google Drive file and open it in a new tab.</p>";

// Test 3: Simulate the account view behavior
echo "<h3>Test 3: Account Page View Button Simulation</h3>";
echo "<button id='testAccountBtn' class='viewFileBtn' data-file='$fileId' style='font-size: 16px; padding: 10px; background: #ea4335; color: white; border: none; border-radius: 5px; cursor: pointer;'>Test Account View Button</button>";
echo "<p>Clicking this should detect the Google Drive file and open it in a new tab.</p>";

// Test 4: Simulate the applications view behavior
echo "<h3>Test 4: Applications Page View Button Simulation</h3>";
echo "<button id='testApplicationsBtn' class='view-document-btn' data-file='http://localhost:8080/HRMO/file/viewDocument/123/pds' style='font-size: 16px; padding: 10px; background: #fbbc04; color: white; border: none; border-radius: 5px; cursor: pointer;'>Test Applications View Button</button>";
echo "<p>Clicking this should detect the Google Drive file and open it in a new tab.</p>";

echo "<h2>Expected Behavior After Fix:</h2>";
echo "<ul>";
echo "<li>All 'View Document' buttons should detect Google Drive file IDs</li>";
echo "<li>Google Drive files should open in modals with Google Drive preview</li>";
echo "<li>No more 'Unable to Open File' errors for existing documents</li>";
echo "<li>Local files should continue to work as before (in modal)</li>";
echo "<li>Modal should display Google Drive document preview inline</li>";
echo "</ul>";

echo "<h2>JavaScript Implementation Test:</h2>";
?>

<script>
// Test the Google Drive file detection logic
function testGoogleDriveDetection() {
    const testCases = [
        { filename: '1bZ85fngmCXNYdri5MvZRGO6NF1L-sp0x', expected: true, description: 'Valid Google Drive file ID' },
        { filename: '1H6radLjRG23teac59BIKbgZ5h8ZP4OAW', expected: true, description: 'Another valid Google Drive file ID' },
        { filename: '1770865265_c5120c2df87a56ce2513.png', expected: false, description: 'Local file with timestamp prefix' },
        { filename: 'test.pdf', expected: false, description: 'Regular filename' },
        { filename: 'abc123', expected: false, description: 'Too short' }
    ];

    console.log('=== Google Drive File Detection Test ===');
    testCases.forEach(test => {
        const isGoogleDriveFile = /^[a-zA-Z0-9_-]{28,33}$/.test(test.filename) && !/^\d{10}_/.test(test.filename);
        const result = isGoogleDriveFile === test.expected ? '✅ PASS' : '❌ FAIL';
        console.log(`${result} ${test.description}: ${test.filename} -> ${isGoogleDriveFile}`);
    });
}

// Run the test when page loads
document.addEventListener('DOMContentLoaded', function() {
    testGoogleDriveDetection();
    
    // Test Apply page button
    document.getElementById('testApplyBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const fileUrl = this.dataset.file;
        const fileName = fileUrl.split('/').pop();
        const isGoogleDriveFile = /^[a-zA-Z0-9_-]{28,33}$/.test(fileName) && !/^\d{10}_/.test(fileName);
        
        if(isGoogleDriveFile) {
            const googleDriveUrl = `https://drive.google.com/file/d/${fileName}/preview`;
            console.log('Apply button detected Google Drive file, showing in modal:', googleDriveUrl);
            // Simulate modal behavior
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000;">
                    <div style="background: white; width: 90%; height: 90%; border-radius: 8px; overflow: hidden;">
                        <div style="padding: 15px; background: #f5f5f5; display: flex; justify-content: space-between; align-items: center;">
                            <h3>Document Preview</h3>
                            <button onclick="this.closest('div').parentElement.remove()" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">×</button>
                        </div>
                        <iframe src="${googleDriveUrl}" style="width: 100%; height: calc(100% - 60px); border: none;"></iframe>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            console.log('Apply button detected local file');
        }
    });
    
    // Test Account page button
    document.getElementById('testAccountBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const filename = this.dataset.file;
        const isGoogleDriveFile = /^[a-zA-Z0-9_-]{28,33}$/.test(filename) && !/^\d{10}_/.test(filename);
        
        if(isGoogleDriveFile) {
            const googleDriveUrl = `https://drive.google.com/file/d/${filename}/preview`;
            console.log('Account button detected Google Drive file, showing in modal:', googleDriveUrl);
            // Simulate modal behavior
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000;">
                    <div style="background: white; width: 90%; height: 90%; border-radius: 8px; overflow: hidden;">
                        <div style="padding: 15px; background: #f5f5f5; display: flex; justify-content: space-between; align-items: center;">
                            <h3>Document Preview</h3>
                            <button onclick="this.closest('div').parentElement.remove()" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">×</button>
                        </div>
                        <iframe src="${googleDriveUrl}" style="width: 100%; height: calc(100% - 60px); border: none;"></iframe>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            console.log('Account button detected local file');
        }
    });
    
    // Test Applications page button
    document.getElementById('testApplicationsBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const fileUrl = this.dataset.file;
        const urlParts = fileUrl.split('/');
        const filename = urlParts[urlParts.length - 1];
        const isGoogleDriveFile = /^[a-zA-Z0-9_-]{28,33}$/.test(filename) && !/^\d{10}_/.test(filename);
        
        if(isGoogleDriveFile) {
            const googleDriveUrl = `https://drive.google.com/file/d/${filename}/preview`;
            console.log('Applications button detected Google Drive file, showing in modal:', googleDriveUrl);
            // Simulate modal behavior
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000;">
                    <div style="background: white; width: 90%; height: 90%; border-radius: 8px; overflow: hidden;">
                        <div style="padding: 15px; background: #f5f5f5; display: flex; justify-content: space-between; align-items: center;">
                            <h3>Document Preview</h3>
                            <button onclick="this.closest('div').parentElement.remove()" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">×</button>
                        </div>
                        <iframe src="${googleDriveUrl}" style="width: 100%; height: calc(100% - 60px); border: none;"></iframe>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            console.log('Applications button detected local file');
        }
    });
});
</script>

<?php
echo "<h2>How to Test in Your Application:</h2>";
echo "<ol>";
echo "<li>Log into your HRMO application as user ID 47</li>";
echo "<li>Navigate to any page with 'View Document' buttons</li>";
echo "<li>Click the 'View Document' button for your PDS</li>";
echo "<li>It should now open the document in a modal with Google Drive preview</li>";
echo "<li>Check browser console (F12) for any JavaScript errors</li>";
echo "<li>Close the modal using the X button or by clicking outside</li>";
echo "</ol>";

echo "<h2>Debug Information:</h2>";
echo "<p>Open browser developer tools (F12) and check the Console tab for test results.</p>";
echo "<p>If everything is working correctly, you should see 'PASS' results for all test cases.</p>";
?>