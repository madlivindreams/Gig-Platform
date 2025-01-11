<?php
// Set the correct HTTP code for a 404 error page
http_response_code(404);
?>

<!DOCTYPE html>
<html lang="en">

<?php include_once '../includes/header.php'; ?>
<body>

<div class="error-page">
    <h1>Oops! Page not found.</h1>
    <p>Sorry, the requested page does not exist or has been moved.</p>
    <a href="index.php">Back to homepage</a>
</div>

<?php include_once '../includes/footer.php'; ?>

</body>
</html>
