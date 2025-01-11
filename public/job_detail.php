<?php
// Load configuration file
include_once '../config/db.php';

// Get the job ID from the URL
$job_id = $_GET['job_id'];

// Load job details from the database
$stmt = $pdo->prepare("SELECT jobs.*, users.username, users.user_id FROM jobs 
                       JOIN users ON jobs.user_id = users.user_id 
                       WHERE job_id = :job_id");
$stmt->execute(['job_id' => $job_id]);
$job = $stmt->fetch();

// Verify if the job exists
if (!$job) {
    die('Job does not exist.');
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>
<body>
<div class="default-table">
    <h1><?php echo htmlspecialchars($job['title']); ?></h1>
    <p><strong>Price:</strong> <?php echo htmlspecialchars($job['budget']); ?> €</p>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
    <p><strong>Status:</strong> <?php echo nl2br(htmlspecialchars($job['status'])); ?></p>
    
    <p><strong>Created by:</strong> <a href="profile.php?id=<?php echo htmlspecialchars($job['user_id']); ?>">
        <?php echo htmlspecialchars($job['username']); ?>
    </a></p>
	<p><strong>Tags:</strong> <?php echo nl2br(htmlspecialchars($job['tags'])); ?></p>
	<a class="default-button" href="apply_job.php?job_id=<?php echo $job['job_id']; ?>">I am interested</a><br><br>
    <a class="default-button" href="job_list.php">Back to job listings</a>
</div>
<?php include_once '../includes/footer.php'; ?>
</body>
</html>
