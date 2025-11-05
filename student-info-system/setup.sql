-- setup.sql
CREATE DATABASE IF NOT EXISTS iskolar_sis_db;
USE iskolar_sis_db;

-- Table for students (same as your examples)
CREATE TABLE IF NOT EXISTS studentinfo (
  sid INT AUTO_INCREMENT PRIMARY KEY,
  lastname VARCHAR(60) NOT NULL,
  firstname VARCHAR(60) NOT NULL,
  middlename VARCHAR(60) NULL,
  course_section VARCHAR(30) NOT NULL
);

-- Simple users table for login
CREATE TABLE IF NOT EXISTS users (
  uid INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);


-- Seed one user (plain text for simplicity)
INSERT IGNORE INTO users (username, password) VALUES ('admin', 'admin123');