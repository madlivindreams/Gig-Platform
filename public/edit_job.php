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

// Get job details from the database
$query = $pdo->prepare("SELECT * FROM jobs WHERE job_id = :job_id AND user_id = :user_id");
$query->execute(['job_id' => $job_id, 'user_id' => $_SESSION['user_id']]);
$job = $query->fetch(PDO::FETCH_ASSOC);

// If the job does not exist, redirect to the job list
if (!$job) {
    header('Location: job_list.php');
    exit;
}

// Save the job details into variables
$title = $job['title'];
$description = $job['description'];
$budget = $job['budget'];
$status = $job['status'];

// Process the form to update the job
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $budget = trim($_POST['budget']);
    $status = trim($_POST['status']);

    // Check that all fields are filled
    if (empty($title) || empty($description) || empty($budget) || empty($status)) {
        $error = 'All fields are required!';
    } else {
        // Update the job in the database
        try {
            $stmt = $pdo->prepare("UPDATE jobs SET title = :title, description = :description, budget = :budget, status = :status WHERE job_id = :job_id AND user_id = :user_id");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':budget' => $budget,
                ':status' => $status,
                ':job_id' => $job_id,
                ':user_id' => $_SESSION['user_id']
            ]);

            // After successful update, redirect to the job list
            header('Location: job_list.php');
            exit;
        } catch (PDOException $e) {
            $error = 'An error occurred while updating the job: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include_once '../includes/header.php'; ?>
<body>

<div class="edit-job-section">
    <h2>Edit Job Listing</h2>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="edit_job.php?job_id=<?php echo $job_id; ?>">
        <label for="title">Job Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        <br>

        <label for="description">Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea>
        <br>

        <label for="budget">Budget:</label>
        <input type="number" name="budget" value="<?php echo htmlspecialchars($budget); ?>" required>
        <br>

        <label for="status">Status:</label>
        <select name="status" required>
            <option value="open" <?php if ($status == 'open') echo 'selected'; ?>>Open</option>
            <option value="closed" <?php if ($status == 'closed') echo 'selected'; ?>>Closed</option>
        </select>
        <br>

        <button type="submit">Save Changes</button>
    </form>
    
    <br>
    <a href="job_list.php">Back to Job Listings</a>
</div>

<?php include_once '../includes/footer.php'; ?>

</body>
</html>
