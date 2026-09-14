-- ================================================================
-- UNIFIED SCHEMA - one database (vendor_system), every table the
-- project actually uses. This replaces schema.sql, schema_customer.sql,
-- and sql_admin/setup.sql - those three are no longer needed.
-- ================================================================

CREATE DATABASE IF NOT EXISTS vendor_system;
USE vendor_system;

-- ================= Vendor module =================

-- add_product.php / manage_products.php
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    wholesale_price DECIMAL(10,2) NOT NULL,
    retail_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- damage_product.php
CREATE TABLE IF NOT EXISTS damage_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    damage_qty INT NOT NULL,
    note VARCHAR(200),
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- delivery_status.php
CREATE TABLE IF NOT EXISTS deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL UNIQUE,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ================= Customer module =================

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    label VARCHAR(50) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20),
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    address_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    subtotal DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (address_id) REFERENCES addresses(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    txn_id VARCHAR(100),
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ================= Admin module =================
-- NOTE: admin/vendors.php manages this "vendors" directory table
-- directly - it is NOT the same thing as a vendor's login account.
-- Login for the vendor role still uses the hardcoded array in the
-- root index.php for now.
CREATE TABLE IF NOT EXISTS vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (rating BETWEEN 1 AND 5)
);

-- ================= Demo data =================

INSERT INTO deliveries (order_id, status) VALUES
    ('ORD101', 'Pending'),
    ('ORD102', 'Shipped'),
    ('ORD103', 'Delivered')
ON DUPLICATE KEY UPDATE status = status;

INSERT INTO vendors (vendor_name, contact_email, phone, status) VALUES
    ('Tech World BD', 'vendor1@example.com', '01800000001', 'pending'),
    ('Computer House', 'vendor2@example.com', '01800000002', 'active');

INSERT INTO announcements (title, content) VALUES
    ('Welcome to the Shop', 'Welcome to our Computer Shop Management System.');

INSERT INTO reviews (customer_name, rating, comment) VALUES
    ('Rahim', 5, 'Very good product and fast delivery.');

-- Demo customer account: the hardcoded "customer"/"customer123" login on
-- index.php attaches to this exact record (matched by email).
INSERT INTO customers (name, email, phone, password) VALUES
    ('Demo Customer', 'customer@example.com', '0000000000', '$2y$10$ozJqQKl8xzX8yZ8x8yX8yeQKl8xzX8yZ8x8yX8yeQKl8xzX8yZ8xO')
ON DUPLICATE KEY UPDATE email = email;
