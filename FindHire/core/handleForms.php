<?php
// Include the models file to access functions
require_once 'core/models.php';

// Handle login form submission
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM user_accounts WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $username;

        log_activity($pdo, 'User logged in', $user['user_id'], $username);
        header('Location: dashboard.php');
    } else {
        $error = 'Invalid credentials';
    }
}

// Handle job post creation form
if (isset($_POST['create_post'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    create_job_post($pdo, $title, $description, $_SESSION['user_id']);
    log_activity($pdo, 'Job post created', $_SESSION['user_id'], $_SESSION['username']);
    header('Location: hr-dashboard.php');
}

// Handle job application form submission
if (isset($_POST['apply'])) {
    $job_id = $_POST['job_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $resume = $_FILES['resume'];

    // Handle file upload (resume)
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($resume["name"]);
    move_uploaded_file($resume["tmp_name"], $target_file);

    apply_to_job($pdo, $job_id, $first_name, $last_name, $email, $target_file);
    log_activity($pdo, 'Applied to job', $_SESSION['user_id'], $_SESSION['username']);
    header('Location: applicant-dashboard.php');
}

// Handle messaging form submission (applicant to HR)
if (isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    send_message($pdo, $_SESSION['user_id'], $receiver_id, $message);
    log_activity($pdo, 'Message sent', $_SESSION['user_id'], $_SESSION['username']);
    header('Location: messages.php');
}

// Handle applicant follow-up on application status
if (isset($_POST['follow_up'])) {
    $application_id = $_POST['application_id'];
    $message = $_POST['message'];

    send_message($pdo, $_SESSION['user_id'], $application_id, $message);
    log_activity($pdo, 'Follow-up message sent', $_SESSION['user_id'], $_SESSION['username']);
    header('Location: messages.php');
}
?>