<?php
session_start();
require_once 'core/models.php'; 
require_once 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header('Location: index.php');
    exit();
}

$posts = get_all_job_posts($pdo);

// Check if Accept/Reject buttons are pressed
if (isset($_POST['action']) && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['action'] == 'accept' ? 'accepted' : 'rejected';

    // Update application status
    $sql = "UPDATE applications SET status = :status WHERE application_id = :application_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $new_status);
    $stmt->bindParam(':application_id', $application_id);
    if ($stmt->execute()) {
        $status_message = "Application status updated to $new_status.";
        // After updating status, redirect to refresh the page
        header('Location: view-applications.php');
        exit();
    } else {
        $status_message = "Failed to update application status.";
    }
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

    <!-- Include the shared Navbar -->
    <?php include('navbar.php'); ?>

    <h1>View Job Applications</h1>

    <!-- Display status message -->
    <?php if (isset($status_message)) { echo "<p>$status_message</p>"; } ?>

    <!-- List all job posts -->
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= htmlspecialchars($post['description']) ?></p>

                <!-- Display applicants for this job post, including status -->
                <h4>Applicants:</h4>
                <?php
                // Fetch all applications for this job post using its ID, including status
                $applications = get_applications_for_job($pdo, $post['id'], ['pending']);
                if (!empty($applications)): ?>
                    <ul>
                        <?php foreach ($applications as $application): ?>
                            <li>
                                <!-- Display applicant name and status in parentheses -->
                                <span><?= htmlspecialchars($application['first_name']) . ' ' . htmlspecialchars($application['last_name']) ?>
                                    (<?= htmlspecialchars($application['status']) ?>)
                                </span>
                                
                                <br>

                                <!-- View Resume Link -->
                                <a href="<?= htmlspecialchars($application['resume_path']) ?>" target="_blank">View Resume</a>

                                <!-- Accept/Reject Form -->
                                <?php if ($application['status'] === 'pending'): ?>
                                    <form method="POST" action="view-applications.php" style="display:inline;">
                                        <input type="hidden" name="application_id" value="<?= $application['application_id'] ?>">
                                        <button type="submit" name="action" value="accept">Accept</button>
                                        <button type="submit" name="action" value="reject">Reject</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No applicants for this job post yet.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No job posts available.</p>
    <?php endif; ?>
</body>
</html>
