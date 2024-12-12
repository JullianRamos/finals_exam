<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    echo "Job ID is missing.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE job_id = ?");
    $deleted = $stmt->execute([$job_id]);

    if ($deleted) {
        echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Job post deleted successfully.</h2>
                <a href='hr_dashboard.php' style='text-decoration: none; color: #ffcc00; font-size: 18px;'>Go back to HR Dashboard</a>
              </div>";
        exit();
    } else {
        echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Error deleting job post.</h2>
                <a href='deleteJobApplication.php?job_id=$job_id' style='text-decoration: none; color: #ffcc00; font-size: 18px;'>Try Again</a>
              </div>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Job Post</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #121212;  /* Dark background */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #ffcc00;  /* Bright yellow */
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: #1e1e1e;  /* Darker container */
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        p {
            font-size: 1.2em;
            color: #ffffff;  /* White text */
            margin-bottom: 20px;
        }

        button {
            padding: 12px 20px;
            background-color: #d32f2f;  /* Red button */
            color: #fff;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #b71c1c;  /* Darker red on hover */
        }

        a {
            text-decoration: none;
            color: #ffcc00;  /* Bright yellow for links */
            font-size: 16px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Delete Job Post</h1>
        <p>Are you sure you want to delete this job post?</p>
        <form action="" method="POST">
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
            <button type="submit" name="confirm_delete">Delete Job</button>
        </form>
        <p><a href="hr_dashboard.php">Cancel and Go Back</a></p>
    </div>
</body>
</html>