-- Inventory database setup (with roles + logs)
CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;

-- Users table, dito natin ise-store yung users ng system
CREATE TABLE IF NOT EXISTS users (
  uid INT AUTO_INCREMENT PRIMARY KEY, -- unique id para sa bawat user
  username VARCHAR(50) NOT NULL UNIQUE, -- username, unique dapat
  password VARCHAR(255) NOT NULL,       -- password, pwedeng plain o hashed
  role ENUM('admin','user') NOT NULL DEFAULT 'user' -- role ng user, admin o user lang
);

-- Products table, dito nakalagay lahat ng items sa inventory
CREATE TABLE IF NOT EXISTS products (
  product_id INT AUTO_INCREMENT PRIMARY KEY, -- unique id ng product
  name VARCHAR(255) NOT NULL,                -- pangalan ng product
  category VARCHAR(100) NOT NULL,            -- category ng product, para sa filtering
  quantity INT NOT NULL DEFAULT 0,           -- stock quantity
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00  -- presyo per item
);

-- Add is_archived column, para ma-archive o ma-restore yung products
ALTER TABLE products
ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0 AFTER price;

-- Activity logs table, para ma-track ang actions ng users sa system
CREATE TABLE IF NOT EXISTS activity_logs (
  log_id INT AUTO_INCREMENT PRIMARY KEY,     -- unique log id
  username VARCHAR(50) NOT NULL,            -- sino ang gumawa ng action
  action VARCHAR(20) NOT NULL,              -- type ng action (create, update, delete, login, logout)
  product_id INT NULL,                       -- kung anong product involved (optional)
  details TEXT NULL,                         -- extra details kung kailangan
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP -- timestamp ng action
);

-- Seed admin user, para may default admin account agad
-- Note: password dito ay plain text, pero pwede i-hash sa login logic
INSERT IGNORE INTO users (username, password, role) VALUES ('admin', 'admin123', 'admin');
