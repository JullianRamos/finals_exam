<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: login.php");
    exit();
}

$applicant_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, j.title AS job_title, j.description AS job_description, a.status AS application_status
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    WHERE a.applicant_id = ?
");
$stmt->execute([$applicant_id]);
$applications = $stmt->fetchAll();

$stmtJobPosts = $pdo->query("SELECT * FROM job_posts");
$job_posts = $stmtJobPosts->fetchAll();

$stmtMessages = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmtMessages->execute([$applicant_id]);
$messages = $stmtMessages->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #181818; /* Rich dark background */
            color: #f0f0f0; /* Light text for contrast */
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        header {
            background-color: #2d2d2d; /* Darker header */
            padding: 20px;
            width: 100%;
            text-align: center;
            border-bottom: 2px solid #ffcc00; /* Bright yellow border */
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #ffcc00; /* Bright yellow */
            margin: 0;
            font-size: 2.5em;
        }

        .dashboard-container {
            background-color: #212121; /* Darker container */
            border-radius: 12px;
            padding: 40px;
            width: 90%;
            max-width: 900px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
            margin: 20px 0;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
            background-color: #2a2a2a; /* Dark section background */
        }

        h2 {
            font-family: 'Poppins', sans-serif;
            color: #ffcc00; /* Bright yellow */
            margin-bottom: 15px;
            font-size: 1.8em;
            border-bottom: 2px solid #ffcc00; /* Underline effect */
            padding-bottom: 5px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #333; /* Darker item background */
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        p, strong {
            color: #e0e0e0; /* Soft light text */
            margin: 5px 0;
        }

        a {
            text-decoration: none;
            color: #ffcc00; /* Bright yellow for links */
            display: inline-block;
            margin-top: 10px;
        }

        a:hover {
            text-decoration: underline;
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
            margin-top: 10px;
        }

        button:hover {
            background-color: #e6b800; /* Darker yellow on hover */
            transform: scale(1.05);
        }

        .logout-link {
            text-align: center;
            margin-top: 20px;
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

    <header>
        <h1>Applicant Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <div class="section">
            <h2>Available Job Posts</h2>
            <?php if ($job_posts): ?>
                <ul>
                    <?php foreach ($job_posts as $job): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($job['title']); ?></strong>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
                            <a href="applyJob.php?job_id=<?php echo $job['job_id']; ?>"><button>Apply</button></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No job posts available at the moment.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Your Applications</h2>
            <?php if ($applications): ?>
                <ul>
                    <?php foreach ($applications as $application): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($application['job_title']); ?></strong><br>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($application['job_description']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You haven't applied to any jobs yet.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Your Messages</h2>
            <?php if ($messages): ?>
                <ul>
                    <?php foreach ($messages as $message): ?>
                        <li>
                            <strong>From: <?php echo htmlspecialchars($message['sender_name']); ?></strong><br>
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <p><small>Sent on: <?php echo $message['sent_at']; ?></small></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No messages from HR yet.</p>
            <?php endif; ?>
        </div>

        <p><a href="messageHR.php"><button>Send Message to HR</button></a></p>

        <div class="logout-link">
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>

</body>
</html>