<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Fetch messages for both roles
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch messages between applicant and HR
if ($role === 'applicant') {
    
    $messages = get_messages_for_applicant($pdo, $user_id);
    $hr_users = $pdo->query("SELECT * FROM user_accounts WHERE role = 'HR'")->fetchAll();
} elseif ($role === 'HR') {
    $messages = get_messages_for_hr($pdo, $user_id);
    $applicants = $pdo->query("SELECT * FROM user_accounts WHERE role = 'applicant'")->fetchAll();
}

// Send message form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $message = trim($_POST['message']);
    $receiver_id = $_POST['receiver_id'];

    if (!empty($message)) {
        send_message($pdo, $user_id, $receiver_id, $message);
        header('Location: messages.php');
        exit();
    } else {
        $error = "Message cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <!-- Include the shared navbar -->
    <?php include('navbar.php'); ?>

    <h1>Messages</h1>

    <!-- Message form for applicants -->
    <?php if ($role === 'applicant'): ?>
        <form method="POST" action="messages.php">
            <textarea name="message" placeholder="Write a message..." required></textarea>
            <label for="receiver_id">Send to:</label>
            <select name="receiver_id" required>
                <?php foreach ($hr_users as $hr): ?>
                    <option value="<?= $hr['user_id'] ?>"><?= htmlspecialchars($hr['first_name'] . ' ' . $hr['last_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="send_message">Send Message</button>
        </form>
    <?php endif; ?>

    <!-- Message form for HR -->
    <?php if ($role === 'HR'): ?>
        <form method="POST" action="messages.php">
            <textarea name="message" placeholder="Write a message..." required></textarea>
            <label for="receiver_id">Send to:</label>
            <select name="receiver_id" required>
                <?php foreach ($applicants as $applicant): ?>
                    <option value="<?= $applicant['user_id'] ?>"><?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="send_message">Send Message</button>
        </form>
    <?php endif; ?>

    <!-- Display messages -->
    <?php if (!empty($messages)): ?>
        <div class="messages">
            <?php foreach ($messages as $message): ?>
                <div class="<?= $message['sender_id'] == $user_id ? 'message-outgoing' : 'message-incoming' ?>">
                    <p><strong><?= htmlspecialchars($message['sender_name']) ?>:</strong> <?= htmlspecialchars($message['message']) ?></p>
                    <small><?= htmlspecialchars($message['date_sent']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No messages yet.</p>
    <?php endif; ?>

</body>
</html>