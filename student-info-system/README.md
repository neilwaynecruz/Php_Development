# Iskolar Student Information System (PHP/MySQL + Bootstrap 5)

A simple, presentation-ready Student Information System built in your coding style:

- PHP (procedural, mysqli)
- Sessions for Login/Logout and flash messages
- Validation functions for input checks
- Bootstrap 5 UI with a PUP-inspired theme
- CRUD for student records

Database name: `iskolar_sis_db`

---

## Features

- Authentication
  - Register new account (register.php)
  - Login/Logout via PHP sessions (login.php / logout.php)
  - Change Password (account.php) — supports both plain and hashed passwords; after change, password is hashed
- Students CRUD (students.php)
  - Create: Add new student
  - Read: List all students in a table
  - Update: Search by ID → edit and update record
  - Delete: Delete a record from the list
  - Reset All: Truncate table and reset IDs
- Validation functions and sanitization
  - sanitize(), validateLogin(), validateRegister(), validateStudent(), validateChange()
- UI/UX
  - Bootstrap 5 components
  - PUP-inspired theme (maroon/gold) via `assets/css/theme.css`
  - Responsive layout

---

## Folder Structure

student-info-system/
├─ login.php
├─ register.php
├─ students.php
├─ account.php
├─ logout.php
├─ setup.sql
├─ README.md
└─ assets/
└─ css/
└─ theme.css

---

## Requirements

- PHP 7.4+ (or PHP 8.x)
- MySQL 5.7+ / 8.x
- Apache (XAMPP/WAMP/LAMP)
- Internet access for Bootstrap & Google Fonts CDN (or host locally)

---

## Setup

1. Move the folder to your web root

- XAMPP: htdocs/student-info-system
- WAMP: www/student-info-system

2. Create the database and tables

- Import `setup.sql` via phpMyAdmin, or run:

This creates:

- Database: `iskolar_sis_db`
- Tables: `studentinfo`, `users`
- Default admin user (if not present): `admin / admin123`

3. Theme (PUP-inspired)

- Ensure this file exists and is linked:
- `assets/css/theme.css` (PUP maroon/gold theme)
- All pages link it via:
<link href="assets/css/theme.css" rel="stylesheet"> ```

4. Start services
   Start Apache and MySQL in XAMPP/WAMP/LAMP

5. Open the app

- Go to: http://localhost/student-info-system/login.php
- Login using: admin / admin123
- Or click “Create an account” to register a new user

## Usage

- Login (login.php)

  - Enter username and password
  - On success, you’re redirected to students.php

- Register (register.php)

  - Create a new account (passwords are saved using password_hash())

- Dashboard (students.php)

  - Create new student
  - View table of all students
  - Update: Use “Search Student (by ID)” → edit form appears
  - Delete: Use “Delete” button on a row
  - Reset All: Truncate the table and reset IDs

- Account (account.php)

  - Change your password (current, new, confirm)
  - Works for both plain and hashed existing passwords; new password is always hashed

- Logout (logout.php)
  - Ends the session and returns to login

## File Overview

- setup.sql

  - Creates iskolar_sis_db, studentinfo, and users tables
  - Seeds admin user (INSERT IGNORE)

- login.php

  - Session-based login
  - Validates input (validateLogin)
  - Supports both hashed and plain password checks

- register.php

  - Create new user with password_hash()
  - Validates input (validateRegister)

- account.php

  - Change password (validateChange)
  - Verifies current password (plain or hashed), stores new as hashed

- students.php

  - Protected by session
  - CRUD for studentinfo table
  - Update via search-by-ID edit form
  - Reset (TRUNCATE) table
  - Validation (validateStudent), sanitization (sanitize)

- logout.php

  - session_unset + session_destroy + redirect

- assets/css/theme.css
  - PUP-inspired maroon/gold theme for Bootstrap-based UI

# Validation & Security Notes

- Sanitization: sanitize() wraps trim + htmlspecialchars
- Validation:

  - validateLogin, validateRegister, validateStudent, validateChange
  - Simple alpha checks for names (letters, spaces, dashes, periods)

- Passwords:

  - New registrations and changed passwords use password_hash()
  - Login supports old plain text for the default admin account

- Simplicity by design:
  - Procedural mysqli
  - Direct SQL strings (no prepared statements) to match your style
  - For production, use prepared statements and stronger validation

## Requirements Mapping (for presentation)

- PHP/MySQL

  - All DB operations use procedural mysqli

- Functions

  - createStudent, updateStudent, deleteStudent, resetTable, searchRec
  - sanitize, validateLogin, validateRegister, validateStudent, validateChange

- Session

  - Login/Logout; access protection for students.php
  - Flash messages via $\_SESSION['message']

- Validation Function

  - Dedicated functions + input sanitization

- HTML/CSS

  - Bootstrap 5 template + custom PUP theme

- CRUD

  - Create, Read (table), Update (search -> edit form), Delete

- Login/Logout via Session
  - Yes (login.php / logout.php)

## Default Account

- Username: admin
- Password: admin123

NOTE: If you imported setup.sql, the admin is created with INSERT IGNORE. You can also register a new account anytime.
