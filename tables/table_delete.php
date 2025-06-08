<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: tables.php");
    exit();
}

$id = $_GET['id'];

$sql_check = "SELECT COUNT(*) as count FROM orders WHERE table_id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt_check, "i", $id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$row_check = mysqli_fetch_assoc($result_check);

if ($row_check['count'] > 0) {
    echo "Cannot delete table assigned to existing orders.";
    echo "<br><a href='tables.php'>Back to Tables</a>";
    exit();
}

$sql = "DELETE FROM tables WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: tables.php");
exit();
