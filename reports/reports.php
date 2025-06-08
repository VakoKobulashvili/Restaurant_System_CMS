<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require '../db.php';

$sales_query = "
    SELECT c.name AS category_name, SUM(oi.price * oi.quantity) AS total_sales
    FROM order_items oi
    JOIN menu_items mi ON oi.menu_item_id = mi.id
    JOIN categories c ON mi.category_id = c.id
    GROUP BY c.id
";
$sales_result = mysqli_query($conn, $sales_query);
$sales_data = [];
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales_data[] = $row;
}

$status_query = "
    SELECT status, COUNT(*) AS count
    FROM orders
    GROUP BY status
";
$status_result = mysqli_query($conn, $status_query);
$status_data = [];
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Reports - Restaurant System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../includes/sidebar.css">
    <link rel="stylesheet" href="./reports.css">

</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <h2>Reports Dashboard</h2>

        <div class="charts-container">
            <div class="chart-container">
                <h3>Sales by Category</h3>
                <canvas id="salesChart" width="600" height="300"></canvas>
            </div>

            <div class="chart-container">
                <h3>Order Status Distribution</h3>
                <canvas id="statusChart" width="400" height="400"></canvas>
            </div>
        </div>
    </main>

    <script>
        const salesLabels = <?= json_encode(array_column($sales_data, 'category_name')) ?>;
        const salesValues = <?= json_encode(array_map(fn($e) => (float)$e['total_sales'], $sales_data)) ?>;

        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Total Sales ($)',
                    data: salesValues,
                    backgroundColor: 'rgba(37, 99, 235, 0.7)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 50,
                            color: '#374151',
                            font: { size: 14, weight: '600' }
                        },
                        grid: {
                            color: '#e5e7eb'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#374151',
                            font: { size: 14, weight: '600' }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const statusLabels = <?= json_encode(array_column($status_data, 'status')) ?>;
        const statusCounts = <?= json_encode(array_map(fn($e) => (int)$e['count'], $status_data)) ?>;
        const statusColors = ['#2563eb', '#fbbf24', '#ef4444', '#10b981', '#8b5cf6'];

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: statusColors.slice(0, statusLabels.length),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 14,
                                weight: '600'
                            },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                return `${label}: ${value}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>