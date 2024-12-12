<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$hr_id = $_SESSION['user_id'];

$stmtJobPosts = $pdo->prepare("SELECT * FROM job_posts WHERE created_by = ?");
$stmtJobPosts->execute([$hr_id]);
$job_posts = $stmtJobPosts->fetchAll();

$stmtApplications = $pdo->prepare("
    SELECT a.*, j.title AS job_title, u.username AS applicant_name, a.status AS application_status
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    JOIN users u ON a.applicant_id = u.user_id
    WHERE j.created_by = ?
");
$stmtApplications->execute([$hr_id]);
$applications = $stmtApplications->fetchAll();

$stmtMessages = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmtMessages->execute([$hr_id]);
$messages = $stmtMessages->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $receiver_id = $_POST['receiver_id'];  
    $sender_id = $_SESSION['user_id']; 

    if (!empty($message) && !empty($receiver_id)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type) VALUES (?, ?, ?, 'reply')");
        if ($stmt->execute([$sender_id, $receiver_id, $message])) {
            $_SESSION['message'] = "Reply sent successfully!";
            header("Location: hr_dashboard.php");  
            exit();
        } else {
            $_SESSION['message'] = "Error sending reply.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #121212;  /* Dark background */
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align items at the start */
            overflow-y: auto; /* Allow scrolling if needed */
        }

        .container {
            max-width: 1000px;
            width: 100%;
            padding: 20px; 
            display: flex;
            flex-direction: column;
            align-items: stretch; /* Stretch items to fill */
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #ffcc00;  /* Bright yellow for text */
            font-size: 2.5em;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(255, 204, 0, 0.5);
        }

        .section {
            background-color: #1e1e1e;  /* Darker box background */
            border-radius: 12px; /* Rounded corners */
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        .section h2 {
            font-family: 'Poppins', sans-serif;
            color: #ffcc00;  /* Bright yellow for section titles */
            font-size: 1.8em;
            margin-bottom: 10px;
            border-bottom: 2px solid #ffcc00; /* Underline effect */
            padding-bottom: 10px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            background-color: #333;  /* Darker background for list items */
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;  /* Rounded corners */
            box-shadow: 0 2px 5px rgba(255, 255, 255, 0.1);
        }

        p {
            font-size: 1em;
            color: #ffffff;  /* White text */
            margin: 5px 0;
        }

        a {
            text-decoration: none;
            color: #ffcc00;  /* Bright yellow for links */
            font-weight: bold; /* Bold links */
        }

        a:hover {
            text-decoration: underline;
        }

        button {
            padding: 10px 15px;
            background-color: #ffcc00;  /* Bright yellow button */
            color: #121212;  /* Dark text for contrast */
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 10px; /* Space between button and text */
        }

        button:hover {
            background-color: #e6b800;  /* Darker yellow hover effect */
            transform: scale(1.05);
        }

        .logout-link {
            margin-top: 20px;
            text-align: center;
        }

        .logout-link a {
            color: #ffcc00;  /* Bright yellow link */
            font-size: 16px;
        }

        .logout-link a:hover {
            text-decoration: underline;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #444;  /* Dark border for textarea */
            font-size: 16px;
            background-color: #333;  /* Darker background for textarea */
            color: #ffffff;  /* White text for textarea */
        }

        form {
            margin-top: 15px;
        }

        /* Media query for responsiveness */
        @media (max-width: 600px) {
            h1 {
                font-size: 2em;
            }
            .section h2 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>HR Dashboard</h1>

        <div class="section">
            <p><a href="createJobApplication.php"><button>Add New Job Post</button></a></p>
        </div>

        <div class="section">
            <h2>Manage Job Posts</h2>
            <?php if ($job_posts): ?>
                <ul>
                    <?php foreach ($job_posts as $job): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($job['title']); ?></strong><br>
                            <p><?php echo htmlspecialchars($job['description']); ?></p>
                            <a href="editJobApplication.php?job_id=<?php echo $job['job_id']; ?>">Edit</a> |
                            <a href="deleteJobApplication.php?job_id=<?php echo $job['job_id']; ?>">Delete</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No job posts available.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Applications</h2>
            <?php if ($applications): ?>
                <ul>
                    <?php foreach ($applications as $application): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($application['job_title']); ?></strong><br>
                            <p><strong>Applicant:</strong> <?php echo htmlspecialchars($application['applicant_name']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
                            <a href="update.php?application_id=<?php echo $application['application_id']; ?>">Update Status</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No applications yet.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Messages from Applicants</h2>
            <?php if ($messages): ?>
                <ul>
                    <?php foreach ($messages as $message): ?>
                        <li>
                            <strong>From: <?php echo htmlspecialchars($message['sender_name']); ?></strong><br>
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <p><small style="color: #bbb;">Sent on: <?php echo $message['sent_at']; ?></small></p>

                            <form action="hr_dashboard.php" method="POST">
                                <input type="hidden" name="receiver_id" value="<?php echo $message['sender_id']; ?>">
                                <textarea name="message" rows="3" required></textarea><br>
                                <button type="submit">Reply</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No messages from applicants yet.</p>
            <?php endif; ?>
        </div>

        <div class="logout-link">
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>

</body>
</html>