<?php
// Test environment loading
require_once 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Environment Variables Check:\n";
echo "=============================\n\n";

echo "GOOGLE_OAUTH_CREDENTIALS_PATH: " . ($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? 'NOT SET') . "\n";
echo "GOOGLE_REDIRECT_URI: " . ($_ENV['GOOGLE_REDIRECT_URI'] ?? 'NOT SET') . "\n";
echo "GOOGLE_DRIVE_FOLDER_ID: " . ($_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? 'NOT SET') . "\n\n";

echo "Credentials file exists: " . (file_exists($_ENV['GOOGLE_OAUTH_CREDENTIALS_PATH'] ?? '') ? 'YES' : 'NO') . "\n";
