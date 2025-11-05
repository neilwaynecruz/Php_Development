# Student Information System (PHP/MySQL + Bootstrap 5)

This is a simple Student Information System built with:

- PHP (procedural) + MySQL (mysqli)
- Sessions for Login/Logout and flash messages
- Validation functions for input checks
- Bootstrap 5 for UI (CDN)

## Files

- login.php — Session-based login
- students.php — Protected CRUD (Create, Read, Update, Delete)
- logout.php — Destroys session and redirects to login
- setup.sql — Database and initial user

## Setup

1. Import `setup.sql` into MySQL.
2. Place the folder in your web server root.
3. Open `http://localhost/student-info-system/login.php`.
4. Login with admin/admin123.

## Requirements mapping

- PHP/MySQL: All database operations use procedural mysqli.
- Functions: `createStudent`, `updateStudent`, `deleteStudent`, `resetTable`, `searchRec`, `validateStudent`, `validateLogin`, `sanitize`.
- Session: Login session (`$_SESSION['user']`) and flash messages (`$_SESSION['message']`).
- Validation Function: Ensures required fields and simple alpha checks.
- HTML/CSS: Bootstrap 5 cards, forms, tables, navbar.
- CRUD: Create form, read table, update form (via search by ID), delete button per row.
- Login/Logout: Session-based; protected students.php page.

## Notes

- This project follows a simple, easy-to-understand style (no frameworks, no PDO).
- Passwords are plain text to match the simplicity of the case study (for production, use password_hash).
- PRG pattern used to prevent resubmission; messages shown via session.
