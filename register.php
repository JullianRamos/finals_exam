<?php
require 'core/dbConfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];  // Get confirm password
    $role = $_POST['role']; 

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
    } elseif (!empty($username) && !empty($password) && !empty($role)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $role])) {
                $_SESSION['message'] = "Registration successful!";
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['message'] = "Error inserting user.";
            }
        } else {
            $_SESSION['message'] = "Username already exists.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
}
?>

<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .register-container {
            background-color: #1e1e1e;  /* Slightly lighter dark for the container */
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-container:hover {
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

        input, select {
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

        input:focus, select:focus {
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

        .login-link {
            margin-top: 10px;
            font-size: 14px;
            color: #ffcc00;  /* Bright yellow link */
        }

        .login-link a {
            color: #ffcc00;  /* Yellow for the link */
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Applying Poppins font to specific elements */
        h3, p {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h1>Register Account</h1>

        <!-- Display error message if any -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <!-- Register Form -->
        <form action="register.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div>
                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="Applicant">Applicant</option>
                    <option value="HR">HR</option>
                </select>
            </div>
            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

</body>
</html>