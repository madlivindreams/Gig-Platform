<?php
session_start();
include('../config/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the job ID from the URL
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header('Location: job_list.php');
    exit;
}

$job_id = $_GET['job_id'];

// Check if the job belongs to the logged-in user
$query = $pdo->prepare("SELECT * FROM jobs WHERE job_id = :job_id AND user_id = :user_id");
$query->execute(['job_id' => $job_id, 'user_id' => $_SESSION['user_id']]);
$job = $query->fetch(PDO::FETCH_ASSOC);

// If the job doesn't exist or doesn't belong to the logged-in user, redirect to the job list
if (!$job) {
    header('Location: job_list.php');
    exit;
}

// Delete the job from the database
$stmt = $pdo->prepare("DELETE FROM jobs WHERE job_id = :job_id AND user_id = :user_id");
$stmt->execute(['job_id' => $job_id, 'user_id' => $_SESSION['user_id']]);

// After successful deletion, redirect to the job list
header('Location: job_list.php');
exit;
?>
