CREATE DATABASE IF NOT EXISTS computer_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE computer_shop;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    role ENUM('customer','seller','vendor','admin') NOT NULL DEFAULT 'customer',
    status ENUM('pending','approved','rejected','active') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    seller_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT DEFAULT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    product_id INT DEFAULT NULL,
    rating TINYINT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (rating BETWEEN 1 AND 5)
);

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Demo admin account: admin@computershop.com / admin123
INSERT INTO users (name,email,password,phone,role,status)
SELECT 'System Admin','admin@computershop.com','$2y$12$1kPsj98J/fIf/eTYVKEvI.SXdKY/F3JgYizWoYXGjKhmcRtls2QE2','01700000000','admin','active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='admin@computershop.com');

-- Demo vendor records so the approval page can be tested.
INSERT INTO users (name,email,password,phone,role,status)
SELECT 'Tech World BD','vendor1@example.com','$2y$12$1kPsj98J/fIf/eTYVKEvI.SXdKY/F3JgYizWoYXGjKhmcRtls2QE2','01800000001','vendor','pending'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='vendor1@example.com');

INSERT INTO users (name,email,password,phone,role,status)
SELECT 'Computer House','vendor2@example.com','$2y$12$1kPsj98J/fIf/eTYVKEvI.SXdKY/F3JgYizWoYXGjKhmcRtls2QE2','01800000002','vendor','approved'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='vendor2@example.com');

INSERT INTO products (name,price,stock) SELECT 'Demo Laptop',75000,10 WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Demo Laptop');
INSERT INTO products (name,price,stock) SELECT 'Wireless Keyboard',2500,30 WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Wireless Keyboard');
INSERT INTO orders (total,status) SELECT 75000,'pending' WHERE NOT EXISTS (SELECT 1 FROM orders WHERE total=75000 AND status='pending');
INSERT INTO reviews (rating,comment) SELECT 5,'Very good product and fast delivery.' WHERE NOT EXISTS (SELECT 1 FROM reviews WHERE comment='Very good product and fast delivery.');
INSERT INTO announcements (title,body,created_by) SELECT 'Welcome to Computer Shop','Welcome to our Computer Shop Management System.',(SELECT id FROM users WHERE email='admin@computershop.com') WHERE NOT EXISTS (SELECT 1 FROM announcements WHERE title='Welcome to Computer Shop');
