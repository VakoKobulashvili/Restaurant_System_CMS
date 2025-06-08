<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$sql = "
SELECT o.id, o.total_price, o.status, o.created_at,
       c.name AS customer_name,
       t.table_number,
       u.username AS staff_username
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.id
LEFT JOIN tables t ON o.table_id = t.id
JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders Management</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./orders.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <h2>Orders</h2>

        <a href="order_add.php">+ Add New Order</a><br><br>

        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Table</th>
                <th>Staff</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['table_number'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['staff_username']) ?></td>
                    <td>$<?= number_format($row['total_price'], 2) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a href="order_edit.php?id=<?= $row['id'] ?>">Edit</a> |
                        <a href="order_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this order?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </main>
</body>
</html>
