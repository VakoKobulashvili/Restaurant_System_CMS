<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <title>Admin Panel - Restaurant System</title>

            <link rel="stylesheet" href="./includes/sidebar.css">
            <link rel="stylesheet" href="./dashboard.css">
            <link rel="stylesheet" href="./style.css">
        </head>
        <body>
            <?php include 'includes/sidebar.php'; ?>

            <div class="main-content">
                <div class="dashboard-welcome-box">
                    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
                    <p>Your role: <?= htmlspecialchars($_SESSION['role']) ?></p>
                    <p>Select a section from the left sidebar to manage the restaurant system.</p>
                </div>
            </div>
        </body>
</html>
