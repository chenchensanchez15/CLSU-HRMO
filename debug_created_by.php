<?php
// Debug script to check created_by value

$db = \Config\Database::connect();

echo "=== Debugging created_by Feature ===\n\n";

// 1. Check if column exists
try {
    $query = $db->query("SHOW COLUMNS FROM users LIKE 'created_by'");
    $result = $query->getRowArray();
    
    if ($result) {
        echo "✓ Column 'created_by' EXISTS:\n";
        echo "  - Field: {$result['Field']}\n";
        echo "  - Type: {$result['Type']}\n";
        echo "  - Default: {$result['Default']}\n";
        echo "  - Comment: {$result['Comment']}\n\n";
    } else {
        echo "✗ Column 'created_by' does NOT exist!\n";
        echo "  You need to run the SQL first.\n\n";
    }
} catch (\Exception $e) {
    echo "Error checking column: " . $e->getMessage() . "\n\n";
}

// 2. Check user ID 78
try {
    $query = $db->table('users')
        ->select('id, email, first_name, last_name, first_login, created_by')
        ->where('id', 78)
        ->get()
        ->getRowArray();
    
    if ($query) {
        echo "✓ User ID 78 found:\n";
        echo "  - Email: {$query['email']}\n";
        echo "  - Name: {$query['first_name']} {$query['last_name']}\n";
        echo "  - first_login: {$query['first_login']}\n";
        echo "  - created_by: {$query['created_by']}\n";
        
        if ($query['created_by'] == 1) {
            echo "  ✅ created_by = 1 (Should NOT show verification)\n\n";
        } else {
            echo "  ❌ created_by = {$query['created_by']} (Will show verification)\n\n";
        }
    } else {
        echo "✗ User ID 78 not found!\n\n";
    }
} catch (\Exception $e) {
    echo "Error checking user: " . $e->getMessage() . "\n\n";
}

// 3. Check session data
$session = session();
echo "Current Session Data:\n";
echo "  - user_id: " . ($session->get('user_id') ?? 'NOT SET') . "\n";
echo "  - email: " . ($session->get('email') ?? 'NOT SET') . "\n";
echo "  - created_by: " . ($session->get('created_by') ?? 'NOT SET') . "\n\n";

// 4. If logged in, check the actual user
if ($session->get('user_id')) {
    $userId = $session->get('user_id');
    $query = $db->table('users')
        ->select('id, email, first_name, last_name, created_by')
        ->where('id', $userId)
        ->get()
        ->getRowArray();
    
    if ($query) {
        echo "Currently logged in user (ID: $userId):\n";
        echo "  - Email: {$query['email']}\n";
        echo "  - Name: {$query['first_name']} {$query['last_name']}\n";
        echo "  - created_by: {$query['created_by']}\n";
        
        if ($query['created_by'] == 1) {
            echo "  ✅ Should NOT see verification boxes\n\n";
        } else {
            echo "  ❌ Will see verification boxes\n\n";
        }
    }
}

echo "\n=== Possible Issues ===\n";
echo "1. Column doesn't exist yet (run SQL)\n";
echo "2. User 78 has created_by = 0 or NULL\n";
echo "3. You're logged in as a DIFFERENT user (not ID 78)\n";
echo "4. Session wasn't updated after login (logout and login again)\n";
?>
