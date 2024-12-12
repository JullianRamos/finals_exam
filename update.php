<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];

    $stmt = $pdo->prepare("SELECT * FROM applications WHERE application_id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        $_SESSION['message'] = "Application not found.";
        header("Location: hr_dashboard.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = $_POST['status'];

        $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE application_id = ?");
        if ($stmt->execute([$status, $application_id])) {
            $_SESSION['message'] = "Application status updated to $status.";
        } else {
            $_SESSION['message'] = "Error updating status.";
        }

        header("Location: hr_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Application Status</title>
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
            color: #ffcc00;  /* Bright yellow for text */
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: #1e1e1e;  /* Slightly lighter dark for the container */
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .radio-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .radio-group label {
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            color: #ffffff;  /* White for labels */
        }

        button {
            padding: 12px 20px;
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

        .return-home {
            margin-top: 20px;
        }

        a {
            text-decoration: none;
            color: #ffcc00;  /* Bright yellow link */
            font-size: 16px;
        }

        a:hover {
            text-decoration: underline;
        }

        p.error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Update Application Status</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="error-message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <form action="update.php?application_id=<?php echo $application['application_id']; ?>" method="POST">
            <div class="radio-group">
                <label>
                    <input type="radio" name="status" value="Accepted" <?php if ($application['status'] == 'Accepted') echo 'checked'; ?>>
                    Accept
                </label>
                <label>
                    <input type="radio" name="status" value="Rejected" <?php if ($application['status'] == 'Rejected') echo 'checked'; ?>>
                    Reject
                </label>
            </div>
            <button type="submit">Update Status</button>
        </form>

        <div class="return-home">
            <a href="hr_dashboard.php"><button type="button">Back to HR Dashboard</button></a>
        </div>
    </div>
</body>
</html>