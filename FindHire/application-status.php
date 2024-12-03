<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "SELECT 
                applications.application_id, 
                job_posts.title AS job_title, 
                applications.status, 
                applications.resume_path 
            FROM applications
            INNER JOIN job_posts ON applications.job_id = job_posts.id
            WHERE applications.applicant_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching applications: " . $e->getMessage());
}

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

    <?php include('navbar.php'); ?>

    <h1>Application Status</h1>

    <?php if (!empty($applications)): ?>
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Status</th>
                    <th>Resume</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><?= htmlspecialchars($application['job_title']) ?></td>
                        <td><?= htmlspecialchars($application['status']) ?></td>
                        <td>
                            <?php if (!empty($application['resume_path'])): ?>
                                <a href="<?= htmlspecialchars($application['resume_path']) ?>" target="_blank">View Resume</a>
                            <?php else: ?>
                                No Resume Uploaded
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You haven't applied to any jobs yet.</p>
    <?php endif; ?>

</body>
</html>