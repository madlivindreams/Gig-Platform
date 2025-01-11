<?php
// Load configuration file and functions
include_once '../config/db.php';
include_once '../includes/functions.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Check if job ID is provided
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header('Location: job_list.php'); // If not, redirect to the job list
    exit;
}

$job_id = $_GET['job_id'];

// Get job details including the creator's name
$stmt = $pdo->prepare("
    SELECT jobs.*, users.username 
    FROM jobs 
    JOIN users ON jobs.user_id = users.user_id 
    WHERE jobs.job_id = :job_id
");
$stmt->execute(['job_id' => $job_id]);
$job = $stmt->fetch();

if (!$job) {
    header('Location: job_list.php'); // If the job does not exist, redirect to the job list
    exit;
}

// Process the form to show interest
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);

    // Validate that the message is not empty
    if (empty($message)) {
        $error = 'Message cannot be empty.';
    } else {
        // Automatically add job information to the message
        $full_message = "I am interested in your job offer titled '{$job['title']}'.\n\n";
        $full_message .= $message;

        // Save the message to the database
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (:sender_id, :receiver_id, :message_text)");
        $stmt->execute([
            ':sender_id' => $user_id,
            ':receiver_id' => $job['user_id'], // Job creator is the recipient of the message
            ':message_text' => $full_message
        ]);
        $success = 'Message successfully sent!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>

<h1>Show Interest in the Job Offer</h1>
<div class="default-table">
<h2>Job Details</h2>
<p><strong>Job Title:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
<p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
<p><strong>Budget:</strong> €<?php echo htmlspecialchars($job['budget']); ?></p>
<p><strong>Created by:</strong> <a href="profile.php?id=<?php echo htmlspecialchars($job['user_id']); ?>">
        <?php echo htmlspecialchars($job['username']); ?>
    </a></p>
<p><strong>Tags:</strong> <?php echo nl2br(htmlspecialchars($job['tags'])); ?></p>
	
<!-- Form to send a message to the job creator -->
<h2>Send a Message to the Job Creator</h2>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" action="apply_job.php?job_id=<?php echo $job_id; ?>">
    <label for="message">Message:</label><br>
    <textarea name="message" required></textarea><br><br>
    <button type="submit">I am interested</button>
</form>
</div>
<?php include_once '../includes/footer.php'; ?>

</html>
