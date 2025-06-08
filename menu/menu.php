<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT menu_items.*, categories.name AS category_name 
        FROM menu_items
        JOIN categories ON menu_items.category_id = categories.id";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu Management</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./menu.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
    <h2>Menu Items</h2>

    <p><a href="menu_add.php">+ Add New Item</a></p>

    <table border="1" cellpadding="10">
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>$<?= number_format($row['price'], 2) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <a href="menu_edit.php?id=<?= $row['id'] ?>">Edit</a> |
                    <a href="menu_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
        </main>
</body>
</html>
