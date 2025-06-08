<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    if ($name !== '') {
        $sql = "INSERT INTO customers (name, phone) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $name, $phone);
        mysqli_stmt_execute($stmt);

        header("Location: customers.php");
        exit();
    } else {
        $error = "Customer name is required.";
    }
}

$sql = "SELECT * FROM customers ORDER BY id";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Management</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./customers.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <h2>Customers</h2>

        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="post" action="">
            <input type="text" name="name" placeholder="Customer Name" required>
            <input type="text" name="phone" placeholder="Phone (optional)">
            <input type="submit" value="Add Customer">
        </form>

        <br>

        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <a href="customer_edit.php?id=<?= $row['id'] ?>">Edit</a> |
                        <a href="customer_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this customer?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </main>
</body>
</html>