-- Inventory database setup (with roles + logs)
CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;

-- Users with roles
CREATE TABLE IF NOT EXISTS users (
  uid INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user'
);

-- Products table per case study
CREATE TABLE IF NOT EXISTS products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL,
  quantity INT NOT NULL DEFAULT 0,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00
);

ALTER TABLE products
ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0 AFTER price;


-- Activity logs (optional but recommended)
CREATE TABLE IF NOT EXISTS activity_logs (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  action VARCHAR(20) NOT NULL, -- 'create','update','delete','login','logout'
  product_id INT NULL,
  details TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Seed admin user (plain text; login supports hash/plain)
INSERT IGNORE INTO users (username, password, role) VALUES ('admin', 'admin123', 'admin');
