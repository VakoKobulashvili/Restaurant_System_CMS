<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_number = intval($_POST['table_number']);
    if ($table_number > 0) {
        $sql = "INSERT IGNORE INTO tables (table_number) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $table_number);
        mysqli_stmt_execute($stmt);
    }
    header("Location: tables.php");
    exit();
}

$sql = "SELECT * FROM tables ORDER BY table_number";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Tables Management</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./tables.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <h2>Restaurant Tables</h2>

        <form method="post" action="">
            <input type="number" name="table_number" placeholder="Table Number" required min="1">
            <input type="submit" value="Add Table">
        </form>

        <br>

        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Table Number</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['table_number'] ?></td>
                    <td>
                        <a href="table_edit.php?id=<?= $row['id'] ?>">Edit</a> |
                        <a href="table_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this table?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </main>
</body>
</html>
