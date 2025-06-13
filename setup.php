<?php
$host = "localhost";
$user = "root";
$pass = "";

if (file_exists("installed.lock")) {
    die("⚠️ Setup has already been completed.");
}

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS restaurant";
if ($conn->query($sql) === TRUE) {
    echo "✅ Database 'restaurant' created successfully.<br>";
} else {
    die("❌ Error creating database: " . $conn->error);
}

$conn->select_db("restaurant");

$table_sql = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    table_id INT,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'served', 'paid') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);
SQL;

if ($conn->multi_query($table_sql)) {
    do {
    } while ($conn->next_result());
    echo "✅ All tables created successfully.<br>";
} else {
    die("❌ Error creating tables: " . $conn->error);
}

$insert_sql = <<<SQL

INSERT IGNORE INTO users (username, password, role) VALUES
('staff1', 'staff123', 'staff');

INSERT IGNORE INTO categories (name) VALUES
('Drinks'), ('Main Course'), ('Desserts');

INSERT IGNORE INTO menu_items (name, price, category_id, description) VALUES
('Cola', 1.50, 1, 'Cold soft drink'),
('Orange Juice', 2.00, 1, 'Freshly squeezed juice'),
('Cheeseburger', 5.99, 2, 'Grilled beef burger with cheese'),
('Pizza Margherita', 7.50, 2, 'Classic pizza with mozzarella and tomato'),
('Chocolate Cake', 3.00, 3, 'Rich chocolate dessert');

INSERT IGNORE INTO customers (name, phone) VALUES
('John Doe', '123456789'),
('Jane Smith', '987654321'),
('Alice Brown', '555123456');

INSERT IGNORE INTO tables (table_number) VALUES (1), (2), (3), (4);
SQL;

if ($conn->multi_query($insert_sql)) {
    do {} while ($conn->next_result());
    echo "✅ Demo data inserted.<br>";
} else {
    die("❌ Error inserting demo data: " . $conn->error);
}

file_put_contents("installed.lock", "setup complete");
echo "✅ Setup completed. You can now use the system.<br>";

$conn->close();
?>
