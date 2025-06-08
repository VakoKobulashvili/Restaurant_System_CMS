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

$sql = "SELECT * FROM tables WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$table = mysqli_fetch_assoc($result);

if (!$table) {
    echo "Table not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_number = intval($_POST['table_number']);
    if ($table_number > 0) {
        $update_sql = "UPDATE tables SET table_number = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "ii", $table_number, $id);
        mysqli_stmt_execute($update_stmt);

        header("Location: tables.php");
        exit();
    } else {
        $error = "Table number must be a positive integer.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Table</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./table_edit.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <h2>Edit Table</h2>

        <?php if (!empty($error)) echo "<p class='error-msg'>$error</p>"; ?>

        <form method="post" action="">
            <label for="table_number">
                Table Number:
                <input
                    type="number"
                    id="table_number"
                    name="table_number"
                    value="<?= htmlspecialchars($table['table_number']) ?>"
                    required
                    min="1"
                >
            </label>

            <input type="submit" value="Update Table">
        </form>

        <a href="tables.php">← Back to Tables List</a>
    </main>
</body>
</html>
