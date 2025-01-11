<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Starts session only if it doesn't exist
}

// Database connection and retrieving the count of unread messages for the logged-in user
include_once '../config/db.php'; // Database connection

// Get the total number of job listings
$stmt_jobs = $pdo->prepare("SELECT COUNT(*) AS total_jobs FROM jobs");
$stmt_jobs->execute();
$result_jobs = $stmt_jobs->fetch();
$total_jobs = $result_jobs['total_jobs'];

if (isset($_SESSION['user_id'])) {
    // Get the ID of the logged-in user
    $user_id = $_SESSION['user_id'];

    // Get the count of unread messages
    $stmt_messages = $pdo->prepare("SELECT COUNT(*) AS unread_messages FROM messages WHERE receiver_id = :user_id AND is_read = 0");
    $stmt_messages->execute(['user_id' => $user_id]);
    $result_messages = $stmt_messages->fetch();
    $unread_count = $result_messages['unread_messages'];
}
?>

<nav>
    <ul class="navbar">
        <li><a href="index.php">Home</a></li>
        <li><a href="job_list.php">Job Listings <?php echo "($total_jobs)"; ?></a></li>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        <?php else: ?>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="messages.php">Chat <?php echo ($unread_count > 0) ? "(+$unread_count)" : "(0)"; ?></a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>
