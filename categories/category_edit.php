<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: categories.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM categories WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$category = mysqli_fetch_assoc($result);

if (!$category) {
    echo "Category not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $update_sql = "UPDATE categories SET name = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $name, $id);
        mysqli_stmt_execute($update_stmt);

        header("Location: categories.php");
        exit();
    } else {
        $error = "Category name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./category_edit.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <h2>Edit Category</h2>

        <?php if (!empty($error)) echo "<p class='error-msg'>$error</p>"; ?>

        <form method="post" action="">
            <label for="category_name">
                Name:
                <input
                    type="text"
                    id="category_name"
                    name="name"
                    value="<?= htmlspecialchars($category['name']) ?>"
                    required
                >
            </label>

            <input type="submit" value="Update Category">
        </form>

        <a href="categories.php">← Back to Categories List</a>
    </main>
</body>
</html>
