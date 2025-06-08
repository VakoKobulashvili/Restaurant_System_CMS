<?php
require '../db.php';

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = (int)$_GET['id'];

mysqli_query($conn, "DELETE FROM order_items WHERE order_id = $order_id");

mysqli_query($conn, "DELETE FROM orders WHERE id = $order_id");

header("Location: orders.php");
exit;
?>
