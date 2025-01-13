<?php
// Define connection parameters
$host = 'localhost';
$dbname = 'gig_platform';
$username = 'root';
$password = '';

// Settings for PDO connection
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4"; // DSN (Data Source Name)
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Set to report errors as exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Results will be fetched as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false, // Disable emulated prepared statements (increases security)
];

// Create the database connection
$pdo = new PDO($dsn, $username, $password, $options);
