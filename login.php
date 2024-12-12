<?php
session_start();
require 'core/dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $username;

        if ($user['role'] === 'HR') {
            header("Location: hr_dashboard.php");
        } else {
            header("Location: applicant_dashboard.php");
        }
        exit();
    } else {
        $_SESSION['message'] = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet"> <!-- Poppins Font Link -->

    <style>
        /* Resetting default styles */
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

        /* Main container */
        .login-container {
            background-color: #1e1e1e;  /* Slightly lighter dark for the container */
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.7);
        }

        h1 {
            font-size: 2.5em;
            color: #ffcc00;  /* Bright yellow for text */
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* Form elements styling */
        label {
            font-weight: 500;
            color: #ffffff;  /* White for labels */
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #333;  /* Dark border */
            font-size: 16px;
            color: #ffffff;  /* White text */
            background-color: #424242;  /* Darker input background */
            transition: border 0.3s ease, background-color 0.3s ease;
        }

        input:focus {
            border: 1px solid #ffcc00;  /* Bright yellow focus border */
            background-color: #333;  /* Darker background on focus */
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #ffcc00;  /* Bright yellow button */
            color: #121212;  /* Dark text for contrast */
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #e6b800;  /* Darker yellow hover effect */
            transform: scale(1.03);
        }

        .message {
            color: red;
            margin-bottom: 20px;
        }

        .register-link {
            margin-top: 10px;
            font-size: 14px;
            color: #ffcc00;  /* Bright yellow link */
            text-align: center;
        }

        .register-link a {
            color: #ffcc00;  /* Yellow for the link */
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Applying Poppins font to specific elements */
        h3, p {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>Welcome to FindHire</h1>

        <!-- Display error message if any -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="login.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>

</body>
</html>