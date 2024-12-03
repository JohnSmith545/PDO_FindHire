<?php
// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header('Location: index.php');
    exit();
}

// Get the user's role from the session
$role = $_SESSION['role'];
?>

<nav style="background-color: #333; overflow: hidden;">
    <a href="index.php" style="color: white; padding: 14px 20px; text-decoration: none;">Home</a>
    
    <?php if ($role === 'HR'): ?>
        <a href="hr-dashboard.php" style="color: white; padding: 14px 20px; text-decoration: none;">Dashboard</a>
        <a href="hr-dashboard.php" style="color: white; padding: 14px 20px; text-decoration: none;">Create Job Post</a>
        <a href="view-applications.php" style="color: white; padding: 14px 20px; text-decoration: none;">View Applications</a>
        <a href="messages.php" style="color: white; padding: 14px 20px; text-decoration: none;">Messages</a>
    <?php elseif ($role === 'applicant'): ?>
        <a href="applicant-dashboard.php" style="color: white; padding: 14px 20px; text-decoration: none;">Dashboard</a>
        <a href="application-status.php" style="color: white; padding: 14px 20px; text-decoration: none;">My Applications</a>
        <a href="messages.php" style="color: white; padding: 14px 20px; text-decoration: none;">Messages</a>
    <?php endif; ?>
    
    <a href="logout.php" style="float: right; color: white; padding: 14px 20px; text-decoration: none;">Logout</a>
</nav>
