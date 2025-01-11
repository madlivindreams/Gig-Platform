<?php
// Include database connection
require_once '../config/db.php';

// 1. Function to register a user
function registerUser($email, $password, $full_name, $username, $profile_picture) {
    global $pdo;

    // Validate email
    if (!validateEmail($email)) {
        return "Invalid email address!";
    }

    // Check if a user with the given email already exists
    if (checkUserExists($email)) {
        return "A user with this email already exists!";
    }

    // Hash the password before saving it to the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL query to insert the user
    $sql = "INSERT INTO users (email, password, full_name, username, profile_picture) VALUES (:email, :password, :full_name, :username, :profile_picture)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':profile_picture', $profile_picture);

    if ($stmt->execute()) {
        return "Registration successful!";
    } else {
        return "Error during user registration.";
    }
}

// 2. Function to validate email address
function validateEmail($email) {
    // Use PHP filter to validate email
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// 3. Function to check if a user with the given email exists
function checkUserExists($email) {
    global $pdo;

    // SQL query to search for a user by email
    $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    // If the number of records found is greater than 0, the user exists
    return $stmt->fetchColumn() > 0;
}
?>
