<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'HR'");
$stmt->execute();
$hr_users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $receiver_id = $_POST['receiver_id'];  
    $sender_id = $_SESSION['user_id'];  

    if (!empty($message) && !empty($receiver_id)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$sender_id, $receiver_id, $message])) {
            $_SESSION['message'] = "Message sent successfully!";
            header("Location: messageHR.php");
            exit();
        } else {
            $_SESSION['message'] = "Error sending message.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message to HR</title>
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
            width: 90%;
            max-width: 600px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #ffcc00; /* Bright yellow */
            font-weight: 500;
        }

        select, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #444; /* Darker border */
            border-radius: 8px;
            background-color: #333; /* Dark input background */
            color: #f0f0f0; /* Light text */
            font-size: 1em;
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

        .message {
            color: #ffcc00; /* Bright yellow for messages */
            font-size: 1em;
            margin-bottom: 20px;
        }

        .logout-link {
            margin-top: 20px;
            text-align: center;
        }

        .logout-link a {
            color: #ffcc00; /* Bright yellow for logout link */
            font-size: 16px;
        }

        .logout-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Send Message to HR</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <form action="messageHR.php" method="POST">
            <label for="receiver_id">Select HR:</label>
            <select name="receiver_id" required>
                <?php foreach ($hr_users as $hr): ?>
                    <option value="<?php echo $hr['user_id']; ?>"><?php echo htmlspecialchars($hr['username']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="message">Message:</label>
            <textarea name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>

        <div class="logout-link">
            <p><a href="applicant_dashboard.php">Go back to Dashboard</a></p>
        </div>
    </div>

</body>
</html>