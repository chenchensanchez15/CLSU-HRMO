<?php
// Test Google API Client
require_once 'vendor/autoload.php';

try {
    // Test if Google Client class exists
    if (class_exists('Google\Client')) {
        echo "Google API Client is available!\n";
        
        // Test creating a client instance
        $client = new Google\Client();
        echo "Google Client instance created successfully!\n";
        
        // Test if Drive service class exists
        if (class_exists('Google\Service\Drive')) {
            echo "Google Drive service is available!\n";
        } else {
            echo "Google Drive service NOT available\n";
        }
        
    } else {
        echo "Google API Client is NOT available\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}