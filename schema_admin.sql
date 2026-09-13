

CREATE DATABASE IF NOT EXISTS vendor_system;

USE vendor_system;

-- add_product.php
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    wholesale_price DECIMAL(10,2) NOT NULL,
    retail_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--  damage_product.php
CREATE TABLE IF NOT EXISTS damage_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    damage_qty INT NOT NULL,
    note VARCHAR(200),
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--  delivery_status.php
CREATE TABLE IF NOT EXISTS deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL UNIQUE,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- A few sample orders so delivery_status.php has something to update/check
INSERT INTO deliveries (order_id, status) VALUES
    ('ORD101', 'Pending'),
    ('ORD102', 'Shipped'),
    ('ORD103', 'Delivered')
ON DUPLICATE KEY UPDATE status = status;
