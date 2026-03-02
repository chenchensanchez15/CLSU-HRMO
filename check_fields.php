<?php
require_once 'app/Config/Paths.php';
require_once 'vendor/autoload.php';

// Load environment variables
$env = parse_ini_file('.env');

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'hrmo');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Check the plantilla_items table structure in hrmis-template
$query = "SHOW COLUMNS FROM `hrmis-template`.plantilla_items LIKE 'monthly_salary'";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    echo "Field 'monthly_salary' exists in hrmis-template.plantilla_items\n";
    $row = $result->fetch_assoc();
    print_r($row);
} else {
    echo "Field 'monthly_salary' does NOT exist in hrmis-template.plantilla_items\n";
    
    // Show all fields in the table
    $columnsQuery = "SHOW COLUMNS FROM `hrmis-template`.plantilla_items";
    $columnsResult = $mysqli->query($columnsQuery);
    
    echo "All fields in hrmis-template.plantilla_items:\n";
    while ($colRow = $columnsResult->fetch_assoc()) {
        echo "- " . $colRow['Field'] . " (" . $colRow['Type'] . ")\n";
    }
}

$mysqli->close();
?>