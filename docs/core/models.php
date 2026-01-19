<?php
// Function to create a job post
function create_job_post($pdo, $title, $description, $created_by) {
    // Insert job post into the database
    $sql = "INSERT INTO job_posts (title, description, created_by) VALUES (:title, :description, :created_by)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':created_by', $created_by);  // Bind created_by as well

    return $stmt->execute();
}

// Function to apply to a job
function apply_to_job($pdo, $job_id, $first_name, $last_name, $email, $resume_path) {
    $stmt = $pdo->prepare("INSERT INTO applications (job_id, first_name, last_name, email, resume_path, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$job_id, $first_name, $last_name, $email, $resume_path]);
}

// Function to fetch job posts
function get_all_job_posts($pdo) {
    $sql = "SELECT * FROM job_posts";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to log user activity
function log_activity($pdo, $operation, $user_id, $username) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (operation, user_id, username) VALUES (?, ?, ?)");
    $stmt->execute([$operation, $user_id, $username]);
}

// Function to get all applications for a specific applicant
function get_applications_by_applicant_id($pdo, $applicant_id) {
    $sql = "SELECT a.*, j.title 
            FROM applications a
            JOIN job_posts j ON a.job_id = j.id
            WHERE a.applicant_id = :applicant_id";  // Use applicant_id instead of user_id
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicant_id', $applicant_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch applications for a specific job (for HR to view)
function get_applications_for_job($pdo, $job_id) {
    // Modify the SQL to include user data
    $sql = "SELECT a.application_id, a.job_id, a.applicant_id, a.cover_letter, a.resume_path, a.status, 
                   u.first_name, u.last_name, u.email
            FROM applications a
            INNER JOIN user_accounts u ON a.applicant_id = u.user_id
            WHERE a.job_id = :job_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function create_job_application($pdo, $applicant_id, $job_id, $cover_letter, $resume_path, $email) {
    // Prepare the SQL statement to insert the application
    $sql = "INSERT INTO applications (job_id, applicant_id, cover_letter, resume_path, status, email) 
            VALUES (:job_id, :applicant_id, :cover_letter, :resume_path, 'pending', :email)";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind the parameters to prevent SQL injection
    $stmt->bindParam(':job_id', $job_id);
    $stmt->bindParam(':applicant_id', $applicant_id);
    $stmt->bindParam(':cover_letter', $cover_letter);
    $stmt->bindParam(':resume_path', $resume_path);
    $stmt->bindParam(':email', $email);

    // Execute the statement and check if the insertion was successful
    if ($stmt->execute()) {
        return true; // Application was submitted successfully
    } else {
        return false; // There was an error submitting the application
    }
}

function get_messages_for_applicant($pdo, $user_id) {
    $sql = "SELECT 
                messages.*, 
                CONCAT(user_accounts.first_name, ' ', user_accounts.last_name) AS sender_name, 
                messages.date_sent
            FROM messages
            JOIN user_accounts ON messages.sender_id = user_accounts.user_id
            WHERE messages.sender_id = ? OR messages.receiver_id = ?
            ORDER BY messages.date_sent ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $user_id]); // Fetch messages where the user is either sender or receiver
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_messages_for_hr($pdo, $user_id) {
    $sql = "SELECT 
                messages.*, 
                CONCAT(user_accounts.first_name, ' ', user_accounts.last_name) AS sender_name, 
                messages.date_sent
            FROM messages
            JOIN user_accounts ON messages.sender_id = user_accounts.user_id
            WHERE messages.sender_id = ? OR messages.receiver_id = ?
            ORDER BY messages.date_sent ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $user_id]); // Fetch messages where the user is either sender or receiver
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function send_message($pdo, $sender_id, $receiver_id, $message) {
    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, date_sent) VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sender_id, $receiver_id, $message]);

        // Log success for debugging
        error_log("Message inserted successfully: sender_id = $sender_id, receiver_id = $receiver_id, message = $message");
    } catch (Exception $e) {
        // Log error if insertion fails
        error_log("Error inserting message: " . $e->getMessage());
    }
}


?>