<?php
session_start();
require_once 'core/models.php'; 
require_once 'core/dbConfig.php'; 

if ($_SESSION['role'] !== 'HR') {
    header('Location: index.php');
    exit();
}

// Handle form submission for creating job posts
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_post'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    // Create job post in the database
    $result = create_job_post($pdo, $title, $description, $created_by);

    if ($result) {
        header('Location: hr-dashboard.php');
        exit();
    } else {
        $error_message = "Failed to create the job post.";
    }
}

$posts = get_all_job_posts($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <!-- Include the shared Navbar -->
    <?php include('navbar.php'); ?>

    <h1>HR Dashboard</h1>

    <!-- Display an error message if there was an issue creating the post -->
    <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

    <form method="POST" action="hr-dashboard.php">
        <input type="text" name="title" placeholder="Job Title" required>
        <textarea name="description" placeholder="Job Description" required></textarea>
        <button type="submit" name="create_post">Create Job Post</button>
    </form>

    <h2>Job Posts</h2>
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= htmlspecialchars($post['description']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No job posts available.</p>
    <?php endif; ?>
</body>
</html>