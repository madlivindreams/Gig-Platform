<?php
// Loading the configuration file and connecting to the database
include_once '../config/db.php';

// Check if the user is logged in and has the correct user_id
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 19) {
    // If the user is not logged in or does not have the correct user_id, redirect them to their profile
    header('Location: profile.php');
    exit;
}

// Function to delete a job
if (isset($_GET['delete_job_id'])) {
    $delete_job_id = $_GET['delete_job_id'];

    // First, delete the applications associated with this job
    $stmt_applications = $pdo->prepare("DELETE FROM applications WHERE job_id = :job_id");
    $stmt_applications->execute(['job_id' => $delete_job_id]);

    // Then delete the job itself
    $stmt_job = $pdo->prepare("DELETE FROM jobs WHERE job_id = :job_id");
    $stmt_job->execute(['job_id' => $delete_job_id]);

    header('Location: admin.php');
    exit;
}

// Function to delete a user
if (isset($_GET['delete_user_id'])) {
    $delete_user_id = $_GET['delete_user_id'];
    // Before deleting the user, check if they are an admin
    if ($delete_user_id != 19) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $delete_user_id]);
    }
    header('Location: admin.php');
    exit;
}

// Function to edit a job
if (isset($_POST['edit_job_id'])) {
    $job_id = $_POST['edit_job_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE jobs SET title = :title, description = :description, budget = :budget, status = :status WHERE job_id = :job_id");
    $stmt->execute([
        'job_id' => $job_id,
        'title' => $title,
        'description' => $description,
        'budget' => $budget,
        'status' => $status
    ]);

    header('Location: admin.php');
    exit;
}

// Function to edit a user
if (isset($_POST['edit_user_id'])) {
    $user_id = $_POST['edit_user_id'];
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE users SET username = :username, full_name = :full_name, email = :email WHERE user_id = :user_id");
    $stmt->execute([
        'user_id' => $user_id,
        'username' => $username,
        'full_name' => $full_name,
        'email' => $email
    ]);

    header('Location: admin.php');
    exit;
}

// Load all jobs
$stmt_jobs = $pdo->prepare("SELECT * FROM jobs");
$stmt_jobs->execute();
$jobs = $stmt_jobs->fetchAll();

// Load all users
$stmt_users = $pdo->prepare("SELECT * FROM users");
$stmt_users->execute();
$users = $stmt_users->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>

<body>

<h1>Admin Panel</h1>

<h2>Job Management</h2>
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($jobs as $job): ?>
            <tr>
                <td><?php echo $job['job_id']; ?></td>
                <td><?php echo htmlspecialchars($job['title']); ?></td>
                <td><?php echo htmlspecialchars($job['description']); ?></td>
                <td><?php echo htmlspecialchars($job['budget']); ?> €</td>
                <td><?php echo htmlspecialchars($job['status']); ?></td>
                <td>
                    <a href="admin.php?edit_job_id=<?php echo $job['job_id']; ?>">Edit</a> |
                    <a href="admin.php?delete_job_id=<?php echo $job['job_id']; ?>" onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>User Account Management</h2>
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['user_id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <?php if ($user['user_id'] != 19): // Do not allow deletion of the admin account ?>
                        <a href="admin.php?edit_user_id=<?php echo $user['user_id']; ?>">Edit</a> |
                        <a href="admin.php?delete_user_id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this account? This action is irreversible.')">Delete</a>
                    <?php else: ?>
                        (Admin)
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
// If "Edit" is clicked for a job
if (isset($_GET['edit_job_id'])) {
    $edit_job_id = $_GET['edit_job_id'];
    $stmt_edit_job = $pdo->prepare("SELECT * FROM jobs WHERE job_id = :job_id");
    $stmt_edit_job->execute(['job_id' => $edit_job_id]);
    $job = $stmt_edit_job->fetch();
?>
    <h3>Edit Job</h3>
    <form action="admin.php" method="POST">
        <input type="hidden" name="edit_job_id" value="<?php echo $job['job_id']; ?>">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required><br><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($job['description']); ?></textarea><br><br>
        <label for="budget">Price:</label><br>
        <input type="text" id="budget" name="budget" value="<?php echo htmlspecialchars($job['budget']); ?>"><br><br>
        <label for="status">Status:</label><br>
        <select id="status" name="status">
            <option value="open" <?php echo $job['status'] == 'open' ? 'selected' : ''; ?>>Open</option>
            <option value="closed" <?php echo $job['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
            <option value="in_progress" <?php echo $job['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
        </select><br><br>
        <button type="submit">Save Changes</button>
    </form>
<?php
}

// If "Edit" is clicked for a user
if (isset($_GET['edit_user_id'])) {
    $edit_user_id = $_GET['edit_user_id'];
    $stmt_edit_user = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt_edit_user->execute(['user_id' => $edit_user_id]);
    $user = $stmt_edit_user->fetch();
?>
    <h3>Edit User</h3>
    <form action="admin.php" method="POST">
        <input type="hidden" name="edit_user_id" value="<?php echo $user['user_id']; ?>">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>
        <label for="full_name">Full Name:</label><br>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>
        <button type="submit">Save Changes</button>
    </form>
<?php
}
?>

</body>
</html>
