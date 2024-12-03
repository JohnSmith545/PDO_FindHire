<?php
session_start();
require_once 'core/dbConfig.php'; 

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validate form data
    if (empty($username) || empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "All fields are required.";
    } else if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if the username already exists
        $sql = "SELECT * FROM user_accounts WHERE username = :username OR email = :email";  // Check for both username and email uniqueness
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email); 
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Username or Email already exists. Please choose another one.";
        } else {
            // Hash the password before saving to the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $sql = "INSERT INTO user_accounts (username, first_name, last_name, email, password, role) 
                    VALUES (:username, :first_name, :last_name, :email, :password, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email); 
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                $success = "Registration successful. You can now <a href='login.php'>log in</a>.";
            } else {
                $error = "An error occurred while registering. Please try again.";
            }
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
    <h1>Register</h1>

    <!-- Display success or error message -->
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>

    <form method="POST" action="register.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" placeholder="Enter Username" required><br>

        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" placeholder="Enter First Name" required><br>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" placeholder="Enter Last Name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Enter Email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="Enter Password" required><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required><br>

        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="applicant">Applicant</option>
            <option value="HR">HR</option>
        </select><br>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
