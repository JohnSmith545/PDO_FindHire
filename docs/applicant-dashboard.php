<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: index.php');
    exit();
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

</head>
<body>

    <!-- Include the navbar -->
    <?php include('navbar.php'); ?>

    <h1>Welcome to Your Dashboard, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>

    <h2>Available Job Posts</h2>

    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= htmlspecialchars($post['description']) ?></p>
                <a href="apply_for_job.php?job_id=<?= $post['id'] ?>">Apply</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No job posts available at the moment.</p>
    <?php endif; ?>

</body>
</html>
