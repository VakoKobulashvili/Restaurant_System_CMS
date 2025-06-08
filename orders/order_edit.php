<?php
require '../db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = $_GET['id'];

$order_res = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id");
$order = mysqli_fetch_assoc($order_res);

$order_items_res = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $order_id");
$order_items = [];
while ($row = mysqli_fetch_assoc($order_items_res)) {
    $order_items[] = $row;
}

$customers_res = mysqli_query($conn, "SELECT * FROM customers");
$tables_res = mysqli_query($conn, "SELECT * FROM tables");

$menu_items_array = [];
$menu_items_res = mysqli_query($conn, "SELECT * FROM menu_items ORDER BY name");
while ($row = mysqli_fetch_assoc($menu_items_res)) {
    $menu_items_array[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $table_id = $_POST['table_id'];
    $status = $_POST['status'];
    $items = $_POST['items'];

    mysqli_query($conn, "DELETE FROM order_items WHERE order_id = $order_id");

    $total = 0;

    foreach ($items as $item) {
        $menu_id = (int)$item['menu_item_id'];
        $qty = (int)$item['quantity'];

        $price_res = mysqli_query($conn, "SELECT price FROM menu_items WHERE id = $menu_id");
        $price_row = mysqli_fetch_assoc($price_res);
        $price = $price_row['price'];

        $subtotal = $price * $qty;
        $total += $subtotal;

        mysqli_query($conn, "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES ($order_id, $menu_id, $qty, $price)");
    }

    mysqli_query($conn, "UPDATE orders SET customer_id = $customer_id, table_id = $table_id, total_price = $total, status = '$status' WHERE id = $order_id");

    header("Location: orders.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Order</title>
    <script>
        const menuItems = <?= json_encode($menu_items_array) ?>;

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('#items-container > .item-row');
            rows.forEach((row, index) => {
                const btn = row.querySelector('button');
                btn.disabled = (rows.length === 1);
                btn.style.opacity = btn.disabled ? '0.5' : '1';
                btn.style.cursor = btn.disabled ? 'not-allowed' : 'pointer';
            });
        }

        function addItemRow(menuItems, selectedId = null, quantity = 1) {
            const container = document.getElementById('items-container');
            const index = container.children.length;

            let options = '<option value="">--Select Menu Item--</option>';
            menuItems.forEach(mi => {
                const selected = (mi.id == selectedId) ? 'selected' : '';
                options += `<option value="${mi.id}" ${selected}>${mi.name} ($${mi.price})</option>`;
            });

            const row = document.createElement('div');
            row.className = 'item-row';
            row.innerHTML = `
                <select name="items[${index}][menu_item_id]" required>
                    ${options}
                </select>
                <input type="number" name="items[${index}][quantity]" value="${quantity}" min="1" required>
                <button type="button" onclick="removeItemRow(this)" class="remove-btn">Remove</button>
            `;
            container.appendChild(row);
            updateRemoveButtons();
        }

        function removeItemRow(btn) {
            const container = document.getElementById('items-container');
            if (container.children.length > 1) {
                btn.parentElement.remove();
                updateRemoveButtons();
            }
        }

        window.onload = function () {
            const orderItems = <?= json_encode($order_items) ?>;
            const container = document.getElementById('items-container');
            container.innerHTML = '';

            if (orderItems.length === 0) {
                addItemRow(menuItems);
            } else {
                orderItems.forEach(item => {
                    addItemRow(menuItems, item.menu_item_id, item.quantity);
                });
            }
        }
    </script>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./order_edit.css">
</head>
<body>
<?php include '../includes/sidebar.php' ?>
<main class="main-content">
    <h2>Edit Order</h2>

    <form method="post">
        <label>
            Customer:
            <select name="customer_id" required>
                <?php while ($c = mysqli_fetch_assoc($customers_res)) : ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $order['customer_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label>

        <label>
            Table:
            <select name="table_id" required>
                <?php while ($t = mysqli_fetch_assoc($tables_res)) : ?>
                    <option value="<?= $t['id'] ?>" <?= $t['id'] == $order['table_id'] ? 'selected' : '' ?>>
                        Table <?= $t['table_number'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label>

        <label>
            Status:
            <select name="status" required>
                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="served" <?= $order['status'] == 'served' ? 'selected' : '' ?>>Served</option>
                <option value="paid" <?= $order['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
            </select>
        </label>

        <label>
            Order Items:
        </label>
        <div id="items-container"></div>
        <button type="button" onclick="addItemRow(menuItems)" class="add-item-btn">Add Item</button>

        <input type="submit" value="Update Order">
    </form>

    <a href="./orders.php">← Back to Orders List</a>
</main>
</body>
</html>