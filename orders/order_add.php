<?php
session_start();
include("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$customers_res = mysqli_query($conn, "SELECT * FROM customers ORDER BY name");
$tables_res = mysqli_query($conn, "SELECT * FROM tables ORDER BY table_number");
$menu_items_res = mysqli_query($conn, "SELECT * FROM menu_items ORDER BY name");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
    $table_id = !empty($_POST['table_id']) ? intval($_POST['table_id']) : null;
    $user_id = $_SESSION['user_id'];
    $items = $_POST['items'] ?? [];

    if (empty($items)) {
        $error = "Add at least one order item.";
    } else {
        $total_price = 0;
        foreach ($items as $item) {
            $menu_item_id = intval($item['menu_item_id']);
            $quantity = intval($item['quantity']);
            if ($menu_item_id <= 0 || $quantity <= 0) {
                $error = "Invalid item or quantity.";
                break;
            }
            $price_res = mysqli_query($conn, "SELECT price FROM menu_items WHERE id = $menu_item_id");
            if ($price_res && $row = mysqli_fetch_assoc($price_res)) {
                $total_price += $row['price'] * $quantity;
            } else {
                $error = "Menu item not found.";
                break;
            }
        }

        if (!$error) {
            $sql_order = "INSERT INTO orders (customer_id, table_id, user_id, total_price, status) VALUES (?, ?, ?, ?, 'pending')";
            $stmt_order = mysqli_prepare($conn, $sql_order);
            mysqli_stmt_bind_param($stmt_order, "iiid", $customer_id, $table_id, $user_id, $total_price);
            mysqli_stmt_execute($stmt_order);
            $order_id = mysqli_insert_id($conn);

            $sql_item = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($conn, $sql_item);

            foreach ($items as $item) {
                $menu_item_id = intval($item['menu_item_id']);
                $quantity = intval($item['quantity']);
                $price_res = mysqli_query($conn, "SELECT price FROM menu_items WHERE id = $menu_item_id");
                $price = 0;
                if ($price_res && $row = mysqli_fetch_assoc($price_res)) {
                    $price = $row['price'];
                }
                mysqli_stmt_bind_param($stmt_item, "iiid", $order_id, $menu_item_id, $quantity, $price);
                mysqli_stmt_execute($stmt_item);
            }

            header("Location: orders.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Order</title>
    <script>
        function addItemRow() {
            const container = document.getElementById('items-container');
            const index = container.children.length;
            const row = document.createElement('div');

            row.innerHTML = `
                <select name="items[${index}][menu_item_id]" required>
                    <?php
                    mysqli_data_seek($menu_items_res, 0);
                    while ($mi = mysqli_fetch_assoc($menu_items_res)) {
                        echo "<option value='{$mi['id']}'>{$mi['name']} (${$mi['price']})</option>";
                    }
                    ?>
                </select>
                <input type="number" name="items[${index}][quantity]" value="1" min="1" required>
                <button type="button" class="remove-btn" onclick="removeItemRow(this)">Remove</button>
            `;

            container.appendChild(row);
            updateRemoveButtons();
        }

        function removeItemRow(button) {
            const container = document.getElementById('items-container');
            if (container.children.length > 1) {
                button.parentElement.remove();
                updateRemoveButtons();
            }
        }

        function updateRemoveButtons() {
            const container = document.getElementById('items-container');
            const buttons = container.querySelectorAll('.remove-btn');
            buttons.forEach(btn => {
                btn.disabled = buttons.length === 1;
            });
        }

        window.onload = function () {
            const container = document.getElementById('items-container');
            if (container.children.length === 0) {
                addItemRow();
            } else {
                updateRemoveButtons();
            }
        };
    </script>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./order_add.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <h2>Add New Order</h2>

        <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="post" action="">
            <label>
                Customer:
                <select name="customer_id" required>
                    <option value="">-- Select Customer --</option>
                    <?php while ($c = mysqli_fetch_assoc($customers_res)) { ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php } ?>
                </select>
            </label>

            <label>
                Table:
                <select name="table_id" required>
                    <option value="">-- Select Table --</option>
                    <?php while ($t = mysqli_fetch_assoc($tables_res)) { ?>
                        <option value="<?= $t['id'] ?>">Table <?= $t['table_number'] ?></option>
                    <?php } ?>
                </select>
            </label>

            <label>
                Order Items:
                <div id="items-container"></div>
            </label>

            <button type="button" onclick="addItemRow()">Add Item</button>
            <input type="submit" value="Create Order">
        </form>
        <br>
        <a href="orders.php">← Back to Orders List</a>
    </main>
</body>
</html>
