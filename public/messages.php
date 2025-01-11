<?php
// Include configuration file and functions
include_once '../config/db.php';
include_once '../includes/functions.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Process the message sending form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_name']) && isset($_POST['message'])) {
    $receiver_name = trim($_POST['receiver_name']);
    $message = trim($_POST['message']);

    // Check if the message is not empty
    if (empty($message)) {
        $error = 'Message cannot be empty.';
    } else {
        // Check if the receiver exists in the database by username
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = :receiver_name");
        $stmt->execute(['receiver_name' => $receiver_name]);
        $receiver = $stmt->fetch();

        if (!$receiver) {
            $error = 'No user with this username exists.';
        } else {
            // Save the message to the database
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (:sender_id, :receiver_id, :message_text)");
            $stmt->execute([
                ':sender_id' => $user_id,
                ':receiver_id' => $receiver['user_id'],
                ':message_text' => $message
            ]);
            $success = 'Message was successfully sent!';
        }
    }
}

// Load received messages for the user
$stmt = $pdo->prepare("SELECT * FROM messages WHERE receiver_id = :user_id ORDER BY sent_at DESC");
$stmt->execute(['user_id' => $user_id]);
$messages = $stmt->fetchAll();

// Update message status to read (if there's a message ID to be marked as read)
if (isset($_GET['mark_read'])) {
    $message_id = $_GET['mark_read'];
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE message_id = :message_id AND receiver_id = :user_id");
    $stmt->execute(['message_id' => $message_id, 'user_id' => $user_id]);
    header('Location: messages.php'); // Redirect to page after marking the message as read
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/header.php'; ?>
    <h1>Messages</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Message sending form -->
    <div class="sendMessageSection"><h2>Send a Message</h2>
    <center><form method="POST" action="messages.php">
        <label for="receiver_name">Recipient (username):</label>
        <br><br>
        <input type="text" name="receiver_name" required>
        <br><br>
        
        <label for="message">Message:</label>
        <br><br>
        <textarea name="message" required></textarea><br><br>

        <button type="submit">Send Message</button>
    </form></center>
    </div>

    <!-- Display received messages -->
    <div class="messages-section">
    <h2>Received Messages</h2>
    <ul>
        <?php if (empty($messages)): ?>
            <li>No messages.</li>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <?php
                    // Get sender's username
                    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = :sender_id");
                    $stmt->execute(['sender_id' => $message['sender_id']]);
                    $sender = $stmt->fetch();
                ?>
                <li>
                    <strong>From <?php echo htmlspecialchars($sender['username']); ?>:</strong>
                    <p><?php echo nl2br(htmlspecialchars($message['message_text'])); ?></p>
                    <small>Sent: <?php echo $message['sent_at']; ?></small>
                    <!-- If the message is unread, show a link to mark it as read -->
                    <?php if ($message['is_read'] == 0): ?>
                        <a href="messages.php?mark_read=<?php echo $message['message_id']; ?>">Mark as read</a>
                    <?php else: ?>
                        <span>(Read)</span>
                    <?php endif; ?>
                </li><br>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    </div>

<?php include_once '../includes/footer.php'; ?>
</html>
