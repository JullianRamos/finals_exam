<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: login.php");
    exit();
}

$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    echo "Job ID is missing.";
    exit();
}

$job = getJobPostByID($pdo, $job_id);

if (!$job) {
    echo "Job not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicant_id = $_SESSION['user_id'];
    $resume = $_FILES['resume'];

    if (!empty($resume['name'])) {
        // Check if the file is a PDF
        $fileType = pathinfo($resume['name'], PATHINFO_EXTENSION);
        if (strtolower($fileType) !== 'pdf') {
            echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                    <h2>Only PDF files are allowed.</h2>
                    <a href='apply_job.php?job_id=$job_id' style='text-decoration: none; color: #ffcc00; font-size: 18px;'>Go back</a>
                  </div>";
            exit();
        }

        $resumePath = 'uploads/' . basename($resume['name']);
        
        if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, resume) VALUES (?, ?, ?)");
            $stmt->execute([$job_id, $applicant_id, $resumePath]);

            echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                    <h2>Application submitted successfully.</h2>
                    <a href='applicant_dashboard.php' style='text-decoration: none; color: #ffcc00; font-size: 18px;'>Return to Dashboard</a>
                  </div>";
        } else {
            echo "Error uploading resume.";
        }
    } else {
        echo "Please upload a resume.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #121212; /* Dark background */
            color: #f0f0f0; /* Light text for contrast */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #ffcc00; /* Bright yellow */
            font-size: 2.5em;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-container {
            background-color: #1e1e1e; /* Darker container */
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #ffcc00; /* Bright yellow for labels */
            font-weight: 500;
        }

        input[type="file"] {
            background-color: #333; /* Dark input background */
            border: 1px solid #444; /* Darker border */
            color: #f0f0f0; /* Light text */
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 10px;
        }

        button {
            padding: 12px 20px;
            background-color: #ffcc00; /* Bright yellow */
            color: #121212; /* Dark text for contrast */
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 15px;
        }

        button:hover {
            background-color: #e6b800; /* Darker yellow on hover */
            transform: scale(1.05);
        }

        .return-home {
            margin-top: 20px;
        }

        a {
            text-decoration: none;
            color: #ffcc00; /* Bright yellow */
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Apply for Job: <?php echo htmlspecialchars($job['title']); ?></h1>
        <form action="applyJob.php?job_id=<?php echo $job_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="resume">Submit Your Resume (PDF only):</label>
            <input type="file" name="resume" accept=".pdf" required>
            <button type="submit">Apply</button>
        </form>
        <div class="return-home">
            <a href="applicant_dashboard.php"><button type="button">Return to Dashboard</button></a>
        </div>
    </div>
</body>
</html>