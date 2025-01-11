<?php
// Load configuration file
include_once '../config/db.php';

// Load all job listings
$stmt = $pdo->prepare("SELECT * FROM jobs");
$stmt->execute();
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>
<body>

<h1>Job Listings</h1>
<div class="default-table">
<h2>Want to offer something?</h2>
<center><a class="default-button" href="post_job.php">Post a new job</a></center><br>
</div>
<div class="default-table">
<div class="job-container">
<h2>Current job listings:</h2>
    <?php foreach ($jobs as $job): ?>
        <div class="job-card">
            <h2><a href="job_detail.php?job_id=<?php echo $job['job_id']; ?>"><?php echo htmlspecialchars($job['title']); ?></a></h2>
            <p><?php echo htmlspecialchars($job['description']); ?></p>
            <p><strong>Price:</strong> <?php echo htmlspecialchars($job['budget']); ?> €</p>
            <center><a class="jobInterest" href="apply_job.php?job_id=<?php echo $job['job_id']; ?>">I'm interested</a></center>
        </div>
    <?php endforeach; ?>
</div>
</div>
<?php include_once '../includes/footer.php'; ?>
</body>
</html>
