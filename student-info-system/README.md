---
## ⚙️ Requirements

- PHP 7.4+ or PHP 8.x
- MySQL 5.7+ / 8.x
- Apache (XAMPP/WAMP/LAMP)
- Internet access for Bootstrap & Google Fonts CDN (or host locally)
---

## 🧩 Setup Instructions

1. **Move the folder** to your web root:

   - XAMPP → `htdocs/student-info-system`
   - WAMP → `www/student-info-system`

2. **Create the database and tables**

   - Import `setup.sql` via phpMyAdmin
   - or run it manually to create:
     - Database: `iskolar_sis_db`
     - Tables: `studentinfo`, `users`
     - Default admin user: `admin / admin123`

3. **Theme (PUP-inspired)**

   - Ensure this file exists and is linked:
     ```html
     <link href="assets/css/theme.css" rel="stylesheet" />
     ```

4. **Start services**

   - Start Apache and MySQL in XAMPP/WAMP/LAMP

5. **Open the app**
   - Visit: [http://localhost/student-info-system/login.php](http://localhost/student-info-system/login.php)
   - Login using:
     - **Username:** admin
     - **Password:** admin123
   - Or click “Create an account” to register a new user

---

## 💻 Usage Guide

### Login (`login.php`)

- Enter username and password
- On success → redirected to `students.php`

### Register (`register.php`)

- Create a new account (passwords saved using `password_hash()`)

### Dashboard (`students.php`)

- Create, read, update, and delete student records
- Search by ID → edit form appears
- “Reset All” → truncates the table and resets IDs

### Account (`account.php`)

- Change password (current → new → confirm)
- Works for both plain and hashed existing passwords; new passwords always hashed

### Logout (`logout.php`)

- Ends session and redirects to login

---

## 📄 File Overview

| File                     | Description                                            |
| ------------------------ | ------------------------------------------------------ |
| **setup.sql**            | Creates database, tables, and seeds default admin user |
| **login.php**            | Handles session-based login with validation            |
| **register.php**         | Registers new users using `password_hash()`            |
| **account.php**          | Allows password changes with verification              |
| **students.php**         | CRUD operations for `studentinfo` table                |
| **logout.php**           | Ends session and redirects to login                    |
| **assets/css/theme.css** | PUP-inspired maroon/gold theme for Bootstrap UI        |

---

## 🔒 Validation & Security

- **Sanitization:**  
  `sanitize()` wraps `trim()` + `htmlspecialchars()`

- **Validation:**  
  Functions: `validateLogin()`, `validateRegister()`, `validateStudent()`, `validateChange()`

  - Simple alpha checks for names (letters, spaces, dashes, periods)

- **Passwords:**

  - New registrations and changed passwords use `password_hash()`
  - Login supports old plain text (for default admin only)

- **Simplicity by Design:**
  - Procedural MySQLi
  - Direct SQL strings (for educational clarity)
  - ⚠️ For production: use prepared statements and stronger validation

---

## 📘 Requirements Mapping (for Project Demo)

| Requirement                  | Implementation                                                                                                   |
| ---------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| **PHP/MySQL**                | All DB operations use procedural MySQLi                                                                          |
| **Functions**                | `createStudent`, `updateStudent`, `deleteStudent`, `resetTable`, `searchRec`, plus validation/sanitize functions |
| **Session Handling**         | Login/Logout protection for `students.php`                                                                       |
| **Validation Function**      | Dedicated input validation functions                                                                             |
| **HTML/CSS**                 | Bootstrap 5 + custom PUP theme                                                                                   |
| **CRUD**                     | Create, Read, Update, Delete                                                                                     |
| **Login/Logout via Session** | Implemented (`login.php`, `logout.php`)                                                                          |

---

## 👤 Default Account

**Username:** `admin`  
**Password:** `admin123`

> 📝 _Note: If you imported `setup.sql`, the admin user is created automatically via `INSERT IGNORE`. You can also register a new account anytime._

---

## 📸 Screenshots

Below are sample pages of the **Iskolar Student Information System**.

### 🖥️ Login Page (`login.php`)

![Login Page](assets/screenshots/login.png)

### 🧾 Register Page (`register.php`)

![Register Page](assets/screenshots/register.png)

### 🎓 Student Dashboard (`students.php`)

![Student Dashboard](assets/screenshots/students.png)

### 🔑 Account Settings (`account.php`)

![Account Page](assets/screenshots/account.png)

### 🚪 Logout (`logout.php`)

![Logout Page](assets/screenshots/logout.png)

### 🗄️ Database Setup (`setup.sql`)

![Database Setup](assets/screenshots/database.png)

> 📝 **Note:**  
> The `setup.sql` file should be imported in **phpMyAdmin** to create the `iskolar_sis_db` database and default admin account.

### 🎯 Summary

This project demonstrates a fully functional **Student Information System** using **PHP (procedural)** and **MySQL**, styled with **Bootstrap 5** and a **PUP-inspired theme** — perfect for academic presentation or beginner-level web app development.

---

**Developed by:** Neil Wayne Cruz, Charlie Magne Rola, Charles Gabriel Rola, Christian Colita ,Jenero Santos
🖥️ _Information Technology Student_  
📘 _For educational and presentation use only._
