<?php
session_start();
require_once 'core/dbConfig.php'; 
require_once 'core/models.php';

// Ensure the user is logged in as an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: index.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $cover_letter = $_POST['cover_letter'];

    // Insert the application into the database
    $sql = "INSERT INTO applications (job_id, first_name, last_name, email, cover_letter) 
            VALUES (:job_id, :first_name, :last_name, :email, :cover_letter)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':job_id', $job_id);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':cover_letter', $cover_letter);
    $stmt->execute();

    // Redirect to a success page or back to the job posts page
    header("Location: application_success.php");
    exit();
}
