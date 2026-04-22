<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Drive Authentication</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        h1 { color: #333; margin-bottom: 20px; }
        .info { background: #e3f2fd; padding: 20px; border-left: 4px solid #2196f3; margin: 20px 0; }
        .success { background: #d4edda; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0; }
        .button { display: inline-block; padding: 15px 30px; background: #4285f4; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; transition: background 0.3s; }
        .button:hover { background: #357ae8; }
        ol { line-height: 2; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Google Drive Authentication</h1>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="success">
                <strong>✅ <?= session()->getFlashdata('success') ?></strong>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="info" style="background: #f8d7da; border-color: #dc3545;">
                <strong>❌ <?= session()->getFlashdata('error') ?></strong>
            </div>
        <?php endif; ?>
        
        <div class="info">
            <strong>ℹ️ Why Authentication is Required:</strong><br>
            To upload your documents and photos to Google Drive, you need to authorize the HRMO system to access your Google Drive folder.
        </div>
        
        <h2>How to Authenticate:</h2>
        <ol>
            <li>Click the "Connect Google Drive" button below</li>
            <li>You'll be redirected to Google's OAuth consent screen</li>
            <li>Select your Google account (<strong>sanchez.chen-chen@clsu2.edu.ph</strong>)</li>
            <li>Grant permissions to access Google Drive</li>
            <li>You'll be redirected back with a success message</li>
        </ol>
        
        <a href="<?= site_url('google/drive') ?>" class="button">📁 Connect Google Drive</a>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>What Happens After Authentication?</h3>
            <ul>
                <li>✅ Your OAuth token is saved securely</li>
                <li>✅ Documents will be uploaded directly to your Google Drive</li>
                <li>✅ Files are stored in: <code>CLSU_Applicant_Files</code> folder</li>
                <li>✅ Each file gets a unique timestamp-based name</li>
                <li>✅ Files are automatically made publicly accessible for viewing</li>
            </ul>
        </div>
        
        <div style="margin-top: 20px; font-size: 14px; color: #666;">
            <strong>Note:</strong> This is a one-time setup. Once authenticated, you can upload documents without re-authenticating. 
            The token will automatically refresh when needed.
        </div>
    </div>
</body>
</html>
