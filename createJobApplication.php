<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $created_by]);

    echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
            <h2 style='color: #ffcc00;'>Job post created successfully.</h2>
            <a href='hr_dashboard.php' style='text-decoration: none; color: #ffcc00; font-size: 18px;'>Return to Menu</a>
          </div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
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
        }

        .form-container {
            background-color: #1e1e1e;  /* Darker container */
            border-radius: 12px;  /* Rounded corners */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 600px;  /* Wider container */
            text-align: center;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #ffcc00;  /* Bright yellow */
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        input, textarea {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #444;  /* Dark border */
            font-size: 16px;
            background-color: #333;  /* Darker background for input fields */
            color: #ffffff;  /* White text */
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus {
            border-color: #ffcc00;  /* Highlight border on focus */
            outline: none;  /* Remove default outline */
        }

        textarea {
            resize: none;
            height: 120px;  /* Taller textarea */
        }

        button {
            padding: 12px 20px;
            background-color: #ffcc00;  /* Bright yellow button */
            color: #121212;  /* Dark text */
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 100%;  /* Full width button */
            margin-top: 10px;  /* Space above the button */
        }

        button:hover {
            background-color: #e6b800;  /* Darker yellow on hover */
            transform: scale(1.05);  /* Slightly enlarge */
        }

        .return-home {
            margin-top: 20px;
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
        <h1>Add a Job Post</h1>
        <form action="" method="POST">
            <input type="text" name="title" required placeholder="Job Title">
            <textarea name="description" required placeholder="Job Description"></textarea>
            <button type="submit">Create Job Post</button>
        </form>
        <div class="return-home">
            <a href="hr_dashboard.php"><button type="button">Return to Home</button></a>
        </div>
    </div>
</body>
</html>