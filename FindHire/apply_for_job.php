<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: index.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_id = $_POST['job_id'];
    $cover_letter = $_POST['cover_letter'];
    $email = $_POST['email'];
    $resume_path = '';

    // Handle file upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $resume_path = 'uploads/' . $_FILES['resume']['name'];
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    }

    // Create the job application
    if (create_job_application($pdo, $_SESSION['user_id'], $job_id, $cover_letter, $resume_path, $email)) {
        header('Location: applicant-dashboard.php');
        exit();
    } else {
        $error_message = 'Error submitting application. Please try again.';
    }
}

// Get the job post details for display
$job_id = $_GET['job_id'];
$sql = "SELECT * FROM job_posts WHERE id = :job_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':job_id', $job_id);
$stmt->execute();
$job_post = $stmt->fetch(PDO::FETCH_ASSOC);
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

<h1>Apply for Job: <?= htmlspecialchars($job_post['title']) ?></h1>

<!-- Display error message if application submission fails -->
<?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

<form method="POST" action="apply_for_job.php?job_id=<?= $job_id ?>" enctype="multipart/form-data">
    <input type="hidden" name="job_id" value="<?= $job_id ?>">

    <textarea name="cover_letter" placeholder="Write your cover letter here" required></textarea><br>

    <label for="email">Email:</label>
    <input type="email" name="email" value="" required><br>

    <label for="resume">Upload Resume (PDF):</label>
    <input type="file" name="resume" id="resume" required><br>

    <button type="submit">Submit Application</button>
</form>

</body>
</html>
