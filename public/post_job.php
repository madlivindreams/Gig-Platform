<?php
// Load configuration file and functions
include_once '../config/db.php';
include_once '../includes/functions.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Start form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $budget = trim($_POST['budget']);
    $status = trim($_POST['status']);
    $tags = isset($_POST['tags']) ? $_POST['tags'] : [];  // Get tags as an array
    $user_id = $_SESSION['user_id']; // User ID from session

    // Validate that all fields are filled out
    if (empty($title) || empty($description) || empty($budget) || empty($status) || empty($tags)) {
        $error = 'All fields are required!';
    } else {
        // Join tags into a space-separated string
        $tags_string = implode(' ', $tags);

        // Save the job post to the database
        $stmt = $pdo->prepare("INSERT INTO jobs (user_id, title, description, budget, status, tags) 
                               VALUES (:user_id, :title, :description, :budget, :status, :tags)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':description' => $description,
            ':budget' => $budget,
            ':status' => $status,
            ':tags' => $tags_string  // Save tags as a space-separated string
        ]);

        header('Location: job_list.php'); // After adding the job post, redirect to the job list
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>
<h1>Add Job Post</h1>
<div class="default-table">
    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="post_job.php">
        <label for="title">Job Title:</label>
        <input type="text" name="title" required>
        <br><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea>
        <br><br>

        <label for="budget">Budget:</label>
        <input type="number" name="budget" required>
        <br><br>

        <label for="status">Status:</label>
        <select name="status" required>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
        </select>
        <br><br>

        <!-- Hidden input for tags -->
        <input type="hidden" name="tags[]" id="tags" value="" />

        <style>
            .tags-container {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                border: none;
                padding: 5px;
                border-radius: 5px;
                background: #f9f9f9;
            }

            .tags-container .tag {
                background: #007bff;
                color: white;
                border-radius: 3px;
                padding: 5px 10px;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .tags-container .tag .remove {
                cursor: pointer;
                background: #ff5c5c;
                border-radius: 50%;
                width: 16px;
                height: 16px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 12px;
                color: white;
            }

            .tags-container input {
                border: none;
                outline: none;
                flex: 1;
                padding: 5px;
                font-size: 14px;
            }
        </style>

    <div class="tags-container" id="tags-container">
        <input type="text" id="tag-input" placeholder="Add a tag..." />
    </div><br><br>

    <script>
        const tagsContainer = document.getElementById('tags-container');
        const tagInput = document.getElementById('tag-input');
        const tagsHiddenInput = document.getElementById('tags');  // Hidden input for tags

        const tags = [];

        tagInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let value = tagInput.value.trim();

                // Handle quotes for multi-word tags
                if (value.startsWith('"') && value.endsWith('"')) {
                    value = value.slice(1, -1);
                }

                if (value && !tags.includes(value)) {
                    tags.push(value);
                    renderTags();
                }

                tagInput.value = '';
            }
        });

        function renderTags() {
            tagsContainer.innerHTML = '';
            tags.forEach(tag => {
                const tagElement = document.createElement('div');
                tagElement.className = 'tag';
                tagElement.innerHTML = `
                    ${tag}
                    <span class="remove" onclick="removeTag('${tag}')">&times;</span>
                `;
                tagsContainer.appendChild(tagElement);
            });

            const inputElement = document.createElement('input');
            inputElement.type = 'text';
            inputElement.id = 'tag-input';
            inputElement.placeholder = 'Add a tag...';
            inputElement.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let value = inputElement.value.trim();

                    if (value.startsWith('"') && value.endsWith('"')) {
                        value = value.slice(1, -1);
                    }

                    if (value && !tags.includes(value)) {
                        tags.push(value);
                        renderTags();
                    }

                    inputElement.value = '';
                }
            });

            tagsContainer.appendChild(inputElement);
            inputElement.focus();

            // Update hidden input with tag values (space-separated)
            tagsHiddenInput.value = tags.join(' ');
        }

        function removeTag(tag) {
            const index = tags.indexOf(tag);
            if (index > -1) {
                tags.splice(index, 1);
                renderTags();
            }
        }
    </script>

    <button type="submit">Add Job Post</button>
    </form>
</div>
<?php include_once '../includes/footer.php'; ?>
</html>
