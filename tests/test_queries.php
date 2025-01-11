<?php
// Loading configuration files and database connection
include_once '../config/db.php';
include_once '../includes/functions.php';

// Function to test user registration
function testRegistration($email, $password, $username) {
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, username) VALUES (:email, :password, :username)");
        $stmt->execute([
            ':email' => $email,
            ':password' => $hashedPassword,
            ':username' => $username
        ]);
        echo "User with email $email has been successfully registered.<br>";
    } catch (PDOException $e) {
        echo "Registration error: " . $e->getMessage() . "<br>";
    }
}

// Function to test job posting creation
function testJobPosting($userId, $title, $description, $budget, $status) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO jobs (user_id, title, description, budget, status) VALUES (:user_id, :title, :description, :budget, :status)");
        $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':description' => $description,
            ':budget' => $budget,
            ':status' => $status
        ]);
        echo "Job offer '$title' has been successfully added.<br>";
    } catch (PDOException $e) {
        echo "Error adding the offer: " . $e->getMessage() . "<br>";
    }
}

// Function to test sending a message
function testSendMessage($senderId, $receiverId, $message_text) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (:sender_id, :receiver_id, :message_text)");
        $stmt->execute([
            ':sender_id' => $senderId,
            ':receiver_id' => $receiverId,
            ':message_text' => $message_text
        ]);
        echo "Message from user $senderId has been successfully sent to user $receiverId.<br>";
    } catch (PDOException $e) {
        echo "Error sending the message: " . $e->getMessage() . "<br>";
    }
}

// Running the test functions
echo "<h2>Testing Database Operations</h2>";

// Test registration (attempting to add a user)
testRegistration('testuser@example.com', 'password123', 'Test User');

// Test job posting (attempting to add a job offer for user with ID 1)
testJobPosting(1, 'Test Job', 'Description of test job offer', 1500, 'open');

// Test sending a message (attempting to send a message from user with ID 1 to user with ID 2)
testSendMessage(1, 2, 'Test message');
?>
