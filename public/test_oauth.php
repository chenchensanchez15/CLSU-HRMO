<?php
// Simple OAuth test page
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
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

echo "<h1>Google OAuth Configuration Test</h1>";

// Check configuration
echo "<h2>Configuration Check:</h2>";
echo "GOOGLE_REDIRECT_URI: <strong>" . ($_ENV['GOOGLE_REDIRECT_URI'] ?? 'NOT SET') . "</strong><br>";
echo "GOOGLE_OAUTH_CREDENTIALS_PATH: <strong>" . ($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? 'NOT SET') . "</strong><br>";

// Check credentials file
$credPath = $_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? '';
if (file_exists($credPath)) {
    echo "Credentials file exists: <strong style='color: green;'>YES ✓</strong><br>";
    
    $creds = json_decode(file_get_contents($credPath), true);
    if ($creds && isset($creds['web']['redirect_uris'])) {
        echo "Configured redirect URIs:<br>";
        foreach ($creds['web']['redirect_uris'] as $uri) {
            echo "- <strong>$uri</strong><br>";
        }
        
        // Check if our configured URI matches
        $configuredUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? '';
        if (in_array($configuredUri, $creds['web']['redirect_uris'])) {
            echo "<br><strong style='color: green;'>✓ Configuration matches!</strong><br>";
        } else {
            echo "<br><strong style='color: red;'>✗ Configuration mismatch!</strong><br>";
            echo "Your configured URI ($configuredUri) is not in the allowed redirect URIs.<br>";
        }
    }
} else {
    echo "Credentials file exists: <strong style='color: red;'>NO✗</strong><br>";
}

echo "<h2>Test Links:</h2>";
echo "<a href='/HRMO/google/redirectToGoogle' style='font-size: 18px; padding: 10px; background: #4285f4; color: white; text-decoration: none; border-radius: 5px;'>Test Google OAuth Login</a><br><br>";

echo "<a href='/HRMO/dashboard' style='font-size: 16px; padding: 8px; background: #34a853; color: white; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a><br><br>";

echo "<h2>Debug Information:</h2>";
echo "Base URL: <strong>" . (isset($_ENV['app.baseURL']) ? $_ENV['app.baseURL'] : 'Not set in .env') . "</strong><br>";
echo "Full redirect URL should be: <strong>" . ($_ENV['GOOGLE_REDIRECT_URI'] ?? 'Not configured') . "</strong><br>";