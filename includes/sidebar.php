<?php 
    $currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <div>
            <li>
                <a href="/restaurant_system/dashboard.php" 
                   class="<?= preg_match('/^dashboard(_.*)?\.php$/', $currentPage) ? 'active' : '' ?>">
                   Dashboard
                </a>
            </li>

            <li>
                <a href="/restaurant_system/categories/categories.php" 
                   class="<?= preg_match('/^(categories\.php|category_.*\.php)$/', $currentPage) ? 'active' : '' ?>">
                   Categories
                </a>
            </li>

            <li>
                <a href="/restaurant_system/menu/menu.php" 
                   class="<?= preg_match('/^menu(_.*)?\.php$/', $currentPage) ? 'active' : '' ?>">
                   Menu Items
                </a>
            </li>

            <li>
                <a href="/restaurant_system/orders/orders.php" 
                   class="<?= preg_match('/^(orders\.php|order_.*\.php)$/', $currentPage) ? 'active' : '' ?>">
                   Orders
                </a>
            </li>

            <li>
                <a href="/restaurant_system/customers/customers.php" 
                   class="<?= preg_match('/^(customers\.php|customer_.*\.php)$/', $currentPage) ? 'active' : '' ?>">
                   Customers
                </a>
            </li>

            <li>
                <a href="/restaurant_system/tables/tables.php" 
                   class="<?= preg_match('/^(tables\.php|table_.*\.php)$/', $currentPage) ? 'active' : '' ?>">
                   Tables
                </a>
            </li>

            <li>
                <a href="/restaurant_system/reports/reports.php" 
                   class="<?= preg_match('/^(reports\.php|report_.*\.php)$/', $currentPage) ? 'active' : '' ?>">
                   Reports
                </a>
            </li>
        </div>
        <li><a href="/restaurant_system/logout.php" class="logout">Logout</a></li>
    </ul>
</div>
