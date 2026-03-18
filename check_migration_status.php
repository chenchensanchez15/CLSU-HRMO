<?php
// Check migration status and table structure

$db = \Config\Database::connect();

echo "=== Checking Migration Table ===\n\n";

// Check if migration table exists
try {
    $query = $db->query("SELECT * FROM migrations ORDER BY batch");
    $migrations = $query->getResultArray();
    
    echo "Migrations in database:\n";
    foreach ($migrations as $mig) {
        echo "- Version: {$mig['version']}, File: {$mig['filename']}, Batch: {$mig['batch']}\n";
    }
} catch (\Exception $e) {
    echo "Error checking migrations: " . $e->getMessage() . "\n";
}

echo "\n=== Checking Users Table Structure ===\n\n";

try {
    $query = $db->query("DESCRIBE users");
    $columns = $query->getResultArray();
    
    echo "Columns in 'users' table:\n";
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']}) - Null: {$col['Null']}, Key: {$col['Key']}, Default: {$col['Default']}\n";
    }
} catch (\Exception $e) {
    echo "Error checking users table: " . $e->getMessage() . "\n";
}

echo "\n=== Checking Application Personal Table Structure ===\n\n";

try {
    $query = $db->query("DESCRIBE application_personal");
    $columns = $query->getResultArray();
    
    echo "Columns in 'application_personal' table:\n";
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']}) - Null: {$col['Null']}, Key: {$col['Key']}, Default: {$col['Default']}\n";
    }
} catch (\Exception $e) {
    echo "Error checking application_personal table: " . $e->getMessage() . "\n";
}

echo "\n=== Checking if created_by_admin column exists ===\n\n";

try {
    $query = $db->query("SHOW COLUMNS FROM users LIKE 'created_by_admin'");
    $result = $query->getRowArray();
    
    if ($result) {
        echo "✓ Column 'created_by_admin' already exists!\n";
        echo "  Field: {$result['Field']}\n";
        echo "  Type: {$result['Type']}\n";
        echo "  Null: {$result['Null']}\n";
        echo "  Default: {$result['Default']}\n";
    } else {
        echo "✗ Column 'created_by_admin' does NOT exist. Need to run migration.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
