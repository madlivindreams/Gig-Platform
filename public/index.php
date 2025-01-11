<style>
/* Ensure the page has a minimum height of 100% of the window */
html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

/* Main content of the page (between header and footer) */
.main-content {
    flex-grow: 1; /* This block will fill the remaining space on the page */
}

/* Footer will always be at the bottom */
footer {
    background-color: #333;
    color: white;
    padding: 20px;
    text-align: center;
    width: 100%;
    margin-top: auto; /* This ensures the footer is always at the bottom */
}
</style>

<html>
<?php include_once '../includes/header.php'; ?>

<div class="welcome-section">
    <h1>Welcome to Gig Platform!</h1>
    <center><p>Discover the best job opportunities and talents in the music industry.</p></center>
</div>
<div class="welcome-section">
    <center><h2>Looking for the right opportunity?</h2></center>
    <center><p>Whether you're an artist, producer, or other professional, you'll find opportunities on our platform that will open new doors for you.</p></center>
    <form action="job_search.php" method="GET" class="search-form">
        <input type="text" name="search" class="search-input" placeholder="Search for a job..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="search-button">Search</button>
    </form>
</div>

<!-- Footer placement -->
<div class="main-content"></div> <!-- This block will fill the remaining space and keep the footer at the bottom -->
<?php include_once '../includes/footer.php'; ?>
</html>
