<?php
// Add created_by_admin column manually

// Load CodeIgniter
require_once 'vendor/autoload.php';

try {
    $db = \Config\Database::connect();
    
    // Try to add the column
    $sql = "ALTER TABLE users ADD COLUMN created_by_admin TINYINT(1) DEFAULT 0 COMMENT '0=Self-registered, 1=Created by admin' AFTER first_login";
    
    $db->query($sql);
    
    echo "✓ SUCCESS: Column 'created_by_admin' added to users table!\n\n";
    
    // Verify it was added
    $query = $db->query("SHOW COLUMNS FROM users LIKE 'created_by_admin'");
    $result = $query->getRowArray();
    
    if ($result) {
        echo "Column Details:\n";
        echo "- Field: {$result['Field']}\n";
        echo "- Type: {$result['Type']}\n";
        echo "- Null: {$result['Null']}\n";
        echo "- Default: {$result['Default']}\n";
        echo "- Comment: {$result['Comment']}\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nThis might mean the column already exists.\n";
}

echo "\nDone!\n";
