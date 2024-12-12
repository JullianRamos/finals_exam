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

$job = getJobPostByID($pdo, $job_id);

if (!$job) {
    echo "Job not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE job_posts SET title = ?, description = ? WHERE job_id = ?");
    $updated = $stmt->execute([$title, $description, $job_id]);

    if ($updated) {
        echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Job post updated successfully.</h2>
                <a href='hr_dashboard.php' style='text-decoration: none; color: #ffcc00; font-size: 18px;'>Go back to HR Dashboard</a>
              </div>";
        exit();
    } else {
        echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Error updating job post.</h2>
                <a href='editJobApplication.php?job_id=$job_id' style='text-decoration: none; color: #ffcc00; font-size: 18px;'>Try Again</a>
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
    <title>Edit Job Post</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #121212; /* Dark background */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background-color: #1e1e1e; /* Darker box background */
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            text-align: left;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #ffcc00; /* Bright yellow */
            font-size: 2.5em;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #ffcc00; /* Underline effect */
            padding-bottom: 10px;
        }

        label {
            color: #ffffff; /* White text for labels */
            font-weight: bold;
            margin-top: 20px;
            display: block;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #444; /* Dark border */
            font-size: 16px;
            color: #ffffff; /* White text */
            background-color: #333; /* Darker background for inputs */
        }

        textarea {
            resize: none;
            height: 120px;
        }

        button {
            padding: 12px 20px;
            background-color: #ffcc00; /* Bright yellow */
            color: #121212; /* Dark text */
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 20px;
            width: 100%;
        }

        button:hover {
            background-color: #e6b800; /* Darker yellow on hover */
            transform: translateY(-2px);
        }

        a {
            text-decoration: none;
            color: #ffcc00; /* Bright yellow for links */
            display: block;
            text-align: center;
            margin-top: 15px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Job Post</h1>
        <form action="" method="POST">
            <label for="title">Job Title</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($job['title']); ?>" required placeholder="Enter Job Title">
            
            <label for="description">Job Description</label>
            <textarea name="description" id="description" required placeholder="Enter Job Description"><?php echo htmlspecialchars($job['description']); ?></textarea>
            
            <button type="submit">Update Job</button>
        </form>
        <a href="hr_dashboard.php">Cancel and Go Back</a>
    </div>
</body>
</html>