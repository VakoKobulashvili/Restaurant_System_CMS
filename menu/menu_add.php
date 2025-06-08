<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$categories_sql = "SELECT id, name FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $category_id = $_POST["category_id"];
    $description = $_POST["description"];

    $sql = "INSERT INTO menu_items (name, price, category_id, description) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sdis", $name, $price, $category_id, $description);
    mysqli_stmt_execute($stmt);

    header("Location: menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./menu_add.css">
</head>
<body>
    <?php include '../includes/sidebar.php' ?>
    <main class="main-content">
        <h2>Add New Menu Item</h2>


        <form method="post" action="">
            <label>
                Name:
                <input type="text" name="name" required>
            </label>
            <label>
                Price:
                <input type="number" step="0.01" name="price" required>
            </label>
            <label>
                Category:
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php while ($cat = mysqli_fetch_assoc($categories_result)) { ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php } ?>
                </select>
            </label>
            <label style="flex-basis: 100%;">
                Description:
                <textarea name="description" rows="4" required></textarea>
            </label>
            <input type="submit" value="Add Item">
        </form>

        <br>
        <a href="menu.php">← Back to Menu List</a>
    </main>
</body>
</html>
