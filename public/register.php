<?php
// Load configuration file and functions
include_once '../config/db.php';
include_once '../includes/functions.php';

// Start session for later use
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);

    // Process the profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $uploadedFile = $_FILES['profile_picture'];
        $fileName = $uploadedFile['name'];
        $fileTmpName = $uploadedFile['tmp_name'];
        $fileSize = $uploadedFile['size'];
        $fileError = $uploadedFile['error'];

        // Check if the file is an image (e.g., JPG, PNG)
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Create a unique file name to avoid issues with duplicate names
            $newFileName = uniqid('', true) . '.' . $fileExtension;
            $fileDestination = '../uploads/profile_pictures/' . $newFileName;

            // Move the file to the server
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $profilePicturePath = $newFileName; // Path to the image
            } else {
                $error = 'Error uploading the image.';
            }
        } else {
            $error = 'Unsupported file format.';
        }
    } else {
        $profilePicturePath = 'default-profile.jpg'; // Default image if none was uploaded
    }

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email.';
    } else {
        // Check if the user already exists
        $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $error = 'This email is already registered.';
        } else {
            // Encrypt the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Register the user
            $query = $pdo->prepare("INSERT INTO users (email, password, full_name, username, profile_picture) VALUES (:email, :password, :full_name, :username, :profile_picture)");
            $query->execute([
                'email' => $email,
                'password' => $hashedPassword,
                'full_name' => $full_name,
                'username' => $username,
                'profile_picture' => $profilePicturePath
            ]);

            // Get the ID of the new user
            $user_id = $pdo->lastInsertId();

            // Store the user ID in the session for later use
            $_SESSION['user_id'] = $user_id;

            // Redirect to the profile page
            header('Location: profile.php');
            exit;
        }
    }
}
?>

<?php include_once '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
<div class="default-table">
    <h1>Registration</h1>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php" enctype="multipart/form-data">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br><br>
        
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" required>
        <br><br>
        
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <br><br>
        
        <label for="profile_picture">Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*">
        <br><br>
        
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in</a></p>
</div>
</body>
</html>

<?php include_once '../includes/footer.php'; ?>
