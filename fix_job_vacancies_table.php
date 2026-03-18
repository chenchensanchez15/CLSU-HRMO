<?php
// Add missing is_posted column to job_vacancies table

require_once 'vendor/autoload.php';

$db = \Config\Database::connect();

try {
    // Check if column exists
    $fields = $db->getFieldNames('job_vacancies');
    
    if (!in_array('is_posted', $fields)) {
        echo "Adding 'is_posted' column to job_vacancies table...\n";
        
        $db->query("ALTER TABLE job_vacancies ADD COLUMN is_posted TINYINT(1) DEFAULT 0");
        
        // Update existing records to be posted
        $db->table('job_vacancies')->update(['is_posted' => 1]);
        
        echo "✓ Column added successfully!\n";
        echo "✓ Updated all existing jobs to 'posted' status\n";
    } else {
        echo "✓ Column 'is_posted' already exists\n";
    }
    
    echo "\nDone! Refresh your page now.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
