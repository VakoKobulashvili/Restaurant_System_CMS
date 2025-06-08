<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'staff')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
    mysqli_stmt_execute($stmt);

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./register.css">
</head>
<body>
    <h2>Register</h2>
    <form method="post" action="">
        <label>Username:</label>
        <input type="text" name="username" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Register">
    </form>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>
