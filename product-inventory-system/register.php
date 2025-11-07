<?php
ob_start();
session_start();

$connection = mysqli_connect("localhost", "root", "", "inventory_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

if (isset($_POST["btnRegister"])) {
    registerUser();
}

function sanitize($str) { return htmlspecialchars(trim($str)); }

function validateRegister($u, $p, $cp) {
    $errors = [];
    if ($u === '') $errors[] = "Username is required.";
    if ($p === '') $errors[] = "Password is required.";
    if ($cp === '') $errors[] = "Confirm Password is required.";
    if ($u !== '' && strlen($u) < 4) $errors[] = "Username must be at least 4 characters.";
    if ($p !== '' && strlen($p) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($p !== '' && $cp !== '' && $p !== $cp) $errors[] = "Passwords do not match.";
    return $errors;
}

function registerUser() {
    global $connection;

    $username = sanitize($_POST["username"]);
    $password = sanitize($_POST["password"]);
    $confirm  = sanitize($_POST["confirm"]);

    $errors = validateRegister($username, $password, $confirm);

    $stmt = mysqli_prepare($connection, "SELECT uid FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt); 
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errors[] = "Username is already taken.";
    }
    mysqli_stmt_close($stmt); 

    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user'; 

    $stmt = mysqli_prepare($connection, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $username, $hash, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION["message"] = '<div class="alert alert-success">Account created! You can now login.</div>';
        mysqli_stmt_close($stmt);
        header("Location: login.php");
        exit();
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: Could not create account due to a database error.</div>';
        mysqli_stmt_close($stmt);
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8"> 
    <title>Register | Product Inventory System</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"> 
    <link href="assets/css/theme.css" rel="stylesheet"> 
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="login.php">
                <img src="assets/img/prism-logo.png" class="prism-logo" alt="Prism Logo">
                <span class="brand-text">PRISM</span>
            </a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                }
                ?>
                <div class="card shadow-sm auth-card">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-3">Create Account</h4>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group password-toggle">
                                    <input type="password" name="password" class="form-control" placeholder="Enter password" data-toggle="password" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group password-toggle">
                                    <input type="password" name="confirm" class="form-control" placeholder="Confirm password" data-toggle="password" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" name="btnRegister" class="btn btn-primary w-100">Register</button>
                        </form>
                        <p class="text-center mt-3 text-muted" style="font-size: 13px;">Already have an account? <a href="login.php">Login here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle color theme" title="Toggle theme">
        <span class="icon-sun" aria-hidden="true"><i class="fa-solid fa-sun"></i></span>
        <span class="icon-moon" aria-hidden="true"><i class="fa-solid fa-moon"></i></span>
        <span class="label">Theme</span>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/password-toggle.js"></script>
    <script src="assets/js/theme-toggle.js"></script>
    <script src="assets/js/ui-enhance.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>