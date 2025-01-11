<?php
session_start();
include('../config/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user data from the database
$user_id = $_SESSION['user_id'];
$query = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
$query->execute(['user_id' => $user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// If the user does not exist, redirect to login
if (!$user) {
    header('Location: login.php');
    exit;
}

// Initial values for the form
$username = $user['username'];
$full_name = $user['full_name'];
$profile_picture = $user['profile_picture'] ?: 'default-profile.jpg'; // Default picture if not set

// Process the form to save changes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $new_full_name = trim($_POST['full_name']);
    $new_username = trim($_POST['username']);
    
    // Process and save new profile picture (if any)
    $new_profile_picture = $profile_picture; // Default picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "../uploads/profile_pictures/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        if (getimagesize($_FILES['profile_picture']['tmp_name'])) {
            // Move the image to the correct folder
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $new_profile_picture = basename($_FILES['profile_picture']['name']);
            } else {
                $error = "Error uploading the image.";
            }
        } else {
            $error = "The file is not a valid image.";
        }
    }

    // Check if all required fields are filled
    if (empty($new_full_name) || empty($new_username)) {
        $error = 'All fields are required!';
    }

    // If no error, save the data to the database
    if (!isset($error)) {
        $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, username = :username, profile_picture = :profile_picture WHERE user_id = :user_id");
        $stmt->execute([
            ':full_name' => $new_full_name,
            ':username' => $new_username,
            ':profile_picture' => $new_profile_picture,
            ':user_id' => $user_id
        ]);

        header('Location: profile.php'); // After successful update, redirect to profile
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>

<body>
    <div class="profile-section">
        <h2>Edit Profile</h2>

        <?php if (isset($error)) : ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="edit_profile.php" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            <br>

            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
            <br>

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" name="profile_picture" accept="image/*">
            <br>

            <button type="submit">Save Changes</button>
        </form>
    </div>

    <?php include_once '../includes/footer.php'; ?>
</body>

</html>
