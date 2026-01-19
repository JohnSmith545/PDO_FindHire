CREATE TABLE user_accounts (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    first_name VARCHAR(255),
    last_name VARCHAR(255),
email VARCHAR(255),
    password TEXT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('applicant', 'HR') NOT NULL
);

CREATE TABLE job_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_by INT NOT NULL
);

CREATE TABLE applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_id INT NOT NULL,  -- Link to user_accounts table (applicant)
    cover_letter TEXT,          -- Store cover letter content
    resume_path VARCHAR(255) NOT NULL,  -- Path to the applicant's resume file
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',  -- Application status
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES job_posts(id),  -- Links job_id to the job_posts table
    FOREIGN KEY (applicant_id) REFERENCES user_accounts(user_id)  -- Link to user_accounts table for the applicant
);


CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE activity_logs (
    activity_log_id INT AUTO_INCREMENT PRIMARY KEY,
    operation VARCHAR(255),
    user_id INT,
    username VARCHAR(255),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);