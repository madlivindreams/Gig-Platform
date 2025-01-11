<?php
// Define connection parameters
$host = 'localhost';
$dbname = 'gig_platform';
$username = 'root';
$password = '';

// Settings for PDO connection
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8"; // DSN (Data Source Name)
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Set to report errors as exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Results will be fetched as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false, // Disable emulated prepared statements (increases security)
];

try {
    // Create the database connection
    $pdo = new PDO($dsn, $username, $password, $options);
    // Connection is successful, we can proceed with further operations
    // echo "Database connection was successful!";
} catch (PDOException $e) {
    // If an error occurred, output the message and terminate the script
    echo "Database connection error: " . $e->getMessage();
    die(); // Terminate the script to prevent further issues
}
?>
