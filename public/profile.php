<?php
session_start();
include('../config/db.php');
include('../includes/navbar.php');  // Include navbar

// Get user_id from URL (if viewing another user's profile)
$user_id_from_url = isset($_GET['id']) ? $_GET['id'] : null;

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// If no ID in URL, use the logged-in user's ID
$user_id = $user_id_from_url ? $user_id_from_url : $_SESSION['user_id'];

// Load user data from the database
$query = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
$query->execute(['user_id' => $user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// If the user does not exist, redirect to login
if (!$user) {
    header('Location: login.php');
    exit;
}

// Load user data
$username = $user['username'];
$full_name = $user['full_name'];
$profile_picture = $user['profile_picture'] ?: 'default-profile.png'; // Default picture if not set

// Load jobs created by the user
$query_jobs = $pdo->prepare("SELECT * FROM jobs WHERE user_id = :user_id");
$query_jobs->execute(['user_id' => $user_id]);
$jobs = $query_jobs->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gig Platform</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

<div class="default-table">
    <h2>User Profile</h2>

    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
    <p><strong>Profile Picture:</strong></p>
    <img src="../uploads/profile_pictures/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" width="150"><br><br>

    <!-- Buttons will appear only if the user is logged in and viewing their own profile -->
    <?php if ($user_id == $_SESSION['user_id']): ?>
        <a href="edit_profile.php">Edit Profile</a><br><br>
        <a href="logout.php">Logout</a>
    <?php endif; ?>

    <h3>Created Job Listings</h3>

    <?php if (count($jobs) > 0): ?>
        <ul>
            <?php foreach ($jobs as $job): ?>
                <li>
                    <strong><?php echo htmlspecialchars($job['title']); ?></strong><br>
                    Description: <?php echo htmlspecialchars($job['description']); ?><br>
                    Price: <?php echo htmlspecialchars($job['budget']); ?> EUR<br>
                    Status: <?php echo htmlspecialchars($job['status']); ?><br>
                    <a href="job_detail.php?job_id=<?php echo $job['job_id']; ?>">View Details</a>
                    <!-- Show "Edit" and "Delete" only if the user is the author of the job listing -->
                    <?php if ($user_id == $_SESSION['user_id']): ?>
                        | <a href="edit_job.php?job_id=<?php echo $job['job_id']; ?>">Edit</a> |
                        <a href="delete_job.php?job_id=<?php echo $job['job_id']; ?>" onclick="return confirm('Are you sure you want to delete this job listing?')">Delete</a><br><br>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No job listings created.</p>
        <?php if ($user_id == $_SESSION['user_id']): ?>
        <a class="default-button" href="post_job.php">Post a New Job Listing</a>
        <?php endif; ?>
    <?php endif; ?>

</div>

<?php include_once '../includes/footer.php'; ?>

<script src="../assets/js/main.js"></script>

</body>
</html>
