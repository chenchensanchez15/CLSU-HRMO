<?php
// Test Google API Client with different approaches
require_once 'vendor/autoload.php';

echo "Testing different class loading approaches:\n\n";

// Try different ways to load the class
$classes_to_test = [
    'Google\Client',
    'Google_Service_Drive',
    'Google_Client',
    'Google_Service_Drive_DriveFile',
    'Google_Service_Drive_Permission'
];

foreach ($classes_to_test as $class) {
    echo "Testing class: $class\n";
    if (class_exists($class)) {
        echo "  ✓ Class exists\n";
    } else {
        echo " ✗ Class does not exist\n";
    }
}

echo "\nTesting autoloader:\n";
$loader = require 'vendor/autoload.php';
echo "Autoloader loaded: " . get_class($loader) . "\n";

echo "\nTesting if we can create a Google_Client instance:\n";
try {
    if (class_exists('Google_Client')) {
        $client = new Google_Client();
        echo "✓ Google_Client instance created successfully!\n";
    } else {
        echo "✗ Google_Client class not found\n";
    }
} catch (Exception $e) {
    echo "Error creating Google_Client: " . $e->getMessage() . "\n";
}