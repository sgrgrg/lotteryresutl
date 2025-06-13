<?php
require_once 'includes/database.php';

try {
    // Create database if it doesn't exist
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "Database created successfully<br>";
    
    // Initialize tables
    $db = new Database();
    if ($db->initializeTables()) {
        echo "Tables created successfully<br>";
    }
    
    // Insert sample data
    if ($db->insertSampleData()) {
        echo "Sample data inserted successfully<br>";
    }
    
    echo "<br>Initialization completed successfully!<br>";
    echo "<a href='index.php'>Go to Homepage</a>";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
