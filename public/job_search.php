<?php
include_once '../config/db.php';

$search_query = ''; // Default empty value for search
$jobs = [];

if (isset($_GET['search'])) {
    // Save the search query
    $search_query = htmlspecialchars($_GET['search']);

    // Prepare SQL query to search job listings
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE title LIKE ? OR description LIKE ? OR tags LIKE ?");
    $stmt->execute(['%' . $search_query . '%', '%' . $search_query . '%', '%' . $search_query . '%']);
    $jobs = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>
<body>

    <h1>Job Search</h1>
    
    <!-- Search form -->
    <form method="GET" action="job_search.php" style="text-align: center; margin-top: 20px;">
        <input type="text" name="search" value="<?php echo $search_query; ?>" placeholder="Search by title, description, or tags..." style="padding: 10px; width: 300px;">
        <button type="submit" style="padding: 10px 20px; background-color: #007BFF; color: white;">Search</button>
    </form>

    <?php if ($search_query): ?>
        <h3>Results for: "<?php echo $search_query; ?>"</h3>
    <?php endif; ?>
    
    <!-- Display job listings -->
    <div class="job-container">
        <?php if (!empty($jobs)): ?>
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h2><a href="job_detail.php?job_id=<?php echo $job['job_id']; ?>"><?php echo htmlspecialchars($job['title']); ?></a></h2>
                    <p><?php echo htmlspecialchars($job['description']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($job['budget']); ?> €</p>
                    <p>Tags: <?php echo htmlspecialchars($job['tags']); ?></p>
                    <a class="jobInterest" href="apply_job.php?job_id=<?php echo $job['job_id']; ?>">I'm interested</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No listings found.</p>
        <?php endif; ?>
    </div>

<?php include_once '../includes/footer.php'; ?>
</body>
</html>
