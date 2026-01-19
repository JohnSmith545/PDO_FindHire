<?php
session_start();
require_once 'core/dbConfig.php'; 

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate form data
    if (empty($username) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        // Check if the username exists in the database
        $sql = "SELECT * FROM user_accounts WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect to the appropriate dashboard based on the role
            if ($user['role'] == 'HR') {
                header('Location: hr-dashboard.php'); // Redirect HR to their dashboard
            } else {
                header('Location: applicant-dashboard.php'); // Redirect Applicant to their dashboard
            }
            exit();
        } else {
            $error = "Invalid credentials.";
        }
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
    <h2>Login</h2>

    <!-- Display error message if login fails -->
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form method="POST" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" placeholder="Enter Username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="Enter Password" required><br>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
