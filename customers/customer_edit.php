<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: customers.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM customers WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($result);

if (!$customer) {
    echo "Customer not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    if ($name !== '') {
        $update_sql = "UPDATE customers SET name = ?, phone = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "ssi", $name, $phone, $id);
        mysqli_stmt_execute($update_stmt);

        header("Location: customers.php");
        exit();
    } else {
        $error = "Customer name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./customer_edit.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <h2>Edit Customer</h2>

        <?php if (!empty($error)) echo "<p class='error-msg'>$error</p>"; ?>

        <form method="post" action="">
            <label>
            Name:
            <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>
            </label>

            <label>
            Phone:
            <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>">
            </label>

            <input type="submit" value="Update Customer">
        </form>

        <a href="customers.php">← Back to Customers List</a>
    </main>
</body>
</html>
