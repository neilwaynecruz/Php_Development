<?php
ob_start();
session_start();

// kung hindi naka-login, redirect sa login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Connect sa database
$connection = mysqli_connect("localhost", "root", "", "inventory_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

// Kung may form submission for password change
if (isset($_POST["changePass"])) {
    changePassword();
}

// Helper function: sanitize input para safe sa HTML
function sanitize($s) { return htmlspecialchars(trim($s)); }

// Validate password change inputs
function validateChange($cur, $new, $conf) {
    $errors = [];
    if ($cur === '') $errors[] = "Current password is required.";
    if ($new === '') $errors[] = "New password is required.";
    if ($conf === '') $errors[] = "Confirm new password is required.";
    if ($new !== '' && strlen($new) < 6) $errors[] = "New password must be at least 6 characters.";
    if ($new !== '' && $conf !== '' && $new !== $conf) $errors[] = "New passwords do not match.";
    return $errors;
}

// Main function to change password
function changePassword() {
    global $connection;

    $username = $_SESSION['user'];
    $current = sanitize($_POST["current"]);
    $new     = sanitize($_POST["new"]);
    $confirm = sanitize($_POST["confirm"]);

    // Validate input
    $errors = validateChange($current, $new, $confirm);
    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    // Get current password from DB (simple scan)
    $q = mysqli_query($connection, "SELECT * FROM users");
    $found = false; $stored = '';
    if ($q && mysqli_num_rows($q) > 0) {
        while ($row = mysqli_fetch_assoc($q)) {
            if ($row['username'] === $username) {
                $stored = $row['password'];
                $found = true; break;
            }
        }
    }

    // Guard: account not found
    if (!$found) {
        $_SESSION["message"] = '<div class="alert alert-danger">Account not found.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    // Verify current password
    $ok = false;
    if (password_verify($current, $stored)) { $ok = true; } // hashed
    if (!$ok && $stored === $current) { $ok = true; }       // fallback plain text

    if (!$ok) {
        $_SESSION["message"] = '<div class="alert alert-danger">Current password is incorrect.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    // Prevent same password
    if ($current === $new) {
        $_SESSION["message"] = '<div class="alert alert-warning">New password must be different from current password.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    // Hash the new password at update sa DB
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $u = mysqli_real_escape_string($connection, $username);
    $sql = "UPDATE users SET password = '$hash' WHERE username = '$u'";
    if (mysqli_query($connection, $sql)) {
        $_SESSION["message"] = '<div class="alert alert-success">Password updated successfully.</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
    }

    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account | Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap, Font Awesome, Google Fonts, Theme CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="products.php">
                <img src="assets/img/prism-logo.png" class="prism-logo" alt="Prism Logo">
                <span class="brand-text">PRISM</span>
            </a>
            <div class="ms-auto d-flex align-items-center">
                <!-- Greeting sa user -->
                <span class="navbar-text me-3">
                    Hello, <?php echo htmlspecialchars($_SESSION['user']); ?>
                    <span class="badge bg-warning text-dark ms-2"><?php echo htmlspecialchars($_SESSION['role'] ?? 'user'); ?></span>
                </span>
                <!-- Navigation buttons -->
                <a href="products.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Flash messages -->
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Card for Change Password form -->
                <div class="card shadow-sm stat-card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Change Password</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                            <!-- Current Password Input -->
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <div class="input-group password-toggle">
                                    <input type="password" name="current" class="form-control" data-toggle="password" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- New Password Input -->
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <div class="input-group password-toggle">
                                    <input type="password" name="new" class="form-control" data-toggle="password" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Confirm New Password Input -->
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <div class="input-group password-toggle">
                                    <input type="password" name="confirm" class="form-control" data-toggle="password" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" name="changePass" class="btn btn-primary w-100">Update Password</button>
                        </form>
                        <!-- Tip -->
                        <div class="text-muted mt-3" style="font-size: 13px;">Tip: Use at least 6 characters.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Theme Toggle Button -->
    <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle color theme" title="Toggle theme">
        <span class="icon-sun" aria-hidden="true"><i class="fa-solid fa-sun"></i></span>
        <span class="icon-moon" aria-hidden="true"><i class="fa-solid fa-moon"></i></span>
        <span class="label">Theme</span>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/password-toggle.js"></script>
    <script src="assets/js/theme-toggle.js"></script>
    <script src="assets/js/ui-enhance.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
