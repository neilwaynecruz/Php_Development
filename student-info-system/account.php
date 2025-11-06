<?php
ob_start();
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "iskolar_sis_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

if (isset($_POST["changePass"])) {
    changePassword();
}

function sanitize($s) { return htmlspecialchars(trim($s)); }

function validateChange($cur, $new, $conf) {
    $errors = [];
    if ($cur === '') $errors[] = "Current password is required.";
    if ($new === '') $errors[] = "New password is required.";
    if ($conf === '') $errors[] = "Confirm new password is required.";
    if ($new !== '' && strlen($new) < 6) $errors[] = "New password must be at least 6 characters.";
    if ($new !== '' && $conf !== '' && $new !== $conf) $errors[] = "New passwords do not match.";
    return $errors;
}

function changePassword() {
    global $connection;

    $username = $_SESSION['user'];
    $current = sanitize($_POST["current"]);
    $new     = sanitize($_POST["new"]);
    $confirm = sanitize($_POST["confirm"]);

    $errors = validateChange($current, $new, $confirm);
    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    // Get current user's password
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

    if (!$found) {
        $_SESSION["message"] = '<div class="alert alert-danger">Account not found.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    // Verify current password (supports both hashed and plain)
    $ok = false;
    if (password_verify($current, $stored)) { $ok = true; }
    if (!$ok && $stored === $current) { $ok = true; }

    if (!$ok) {
        $_SESSION["message"] = '<div class="alert alert-danger">Current password is incorrect.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    if ($current === $new) {
        $_SESSION["message"] = '<div class="alert alert-warning">New password must be different from current password.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

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
    <!-- Bootstrap + Theme -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" style="margin-top: 4px;" href="students.php">
                <span class="brand-dot"></span> Student Info System
            </a>
            <div class="ms-auto">
                <span class="navbar-text me-3">Hello, <?php echo htmlspecialchars($_SESSION['user']); ?></span>
                <a href="students.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Change Password</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm" class="form-control" required>
                            </div>
                            <button type="submit" name="changePass" class="btn btn-primary w-100">Update Password</button>
                        </form>
                        <div class="text-muted mt-3" style="font-size: 13px;">Tip: Use at least 6 characters.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>