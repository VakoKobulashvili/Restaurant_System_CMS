<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $sql = "INSERT IGNORE INTO categories (name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
    }
    header("Location: categories.php");
    exit();
}

$sql = "SELECT * FROM categories ORDER BY id";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Category Management</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./categories.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <h2>Categories</h2>

        <form method="post" action="">
            <input type="text" name="name" placeholder="New Category Name" required>
            <input type="submit" value="Add Category">
        </form>

        <br>

        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>
                        <a href="category_edit.php?id=<?= $row['id'] ?>">Edit</a> |
                        <a href="category_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this category?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </main>
</body>
</html>
