<?php
session_start();
include('../config/db.php');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error_message = ""; // Variable for error message

// If the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists and the password is correct
    $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $query->execute(['email' => $email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Save the user ID to session for future use
        $_SESSION['user_id'] = $user['user_id']; // Using user_id
        header('Location: profile.php');
        exit;
    } else {
        $error_message = "Incorrect login credentials."; // Error message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>
    <div class="default-table">
	<h2>Login</h2>

    <?php
    // Display error message if the email or password is incorrect
    if (!empty($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <!-- Login form -->
    <center><form action="login.php" method="POST">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form></center><br>

    <center><p>Don't have an account? <a href="register.php">Register here</a></p></center>
</div>
<?php include_once '../includes/footer.php'; ?>
</html>
