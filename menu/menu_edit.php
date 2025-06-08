<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: menu.php");
    exit();
}

$id = $_GET['id'];

$categories_sql = "SELECT id, name FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_sql);

$item_sql = "SELECT * FROM menu_items WHERE id = ?";
$stmt = mysqli_prepare($conn, $item_sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$item_result = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($item_result);

if (!$item) {
    echo "Menu item not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $category_id = $_POST["category_id"];
    $description = $_POST["description"];

    $update_sql = "UPDATE menu_items SET name=?, price=?, category_id=?, description=? WHERE id=?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "sdisi", $name, $price, $category_id, $description, $id);
    mysqli_stmt_execute($update_stmt);

    header("Location: menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Menu Item</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./menu_edit.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
        <main class="main-content">
        <h2>Edit Menu Item</h2>

        <form method="post" action="">
            <label>
                Name:
                <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
            </label>

            <label>
                Price:
                <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($item['price']) ?>" required>
            </label>

            <label>
                Category:
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php while ($cat = mysqli_fetch_assoc($categories_result)) { ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $item['category_id'] ? "selected" : "" ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php } ?>
                </select>
            </label>

            <label style="flex-basis: 100%;">
                Description:
                <textarea name="description" rows="4" required><?= htmlspecialchars($item['description']) ?></textarea>
            </label>

            <input type="submit" value="Update Item">
        </form>

        <br>
        <a href="menu.php">← Back to Menu List</a>
    </main>
</body>
</html>
