<?php
ob_start(); // Para ma-buffer yung output, useful kapag may header() redirect
session_start(); // Simulan ang session para mag-store ng user info

// Connect sa database
$connection = mysqli_connect("localhost", "root", "", "inventory_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error()); // Error kung hindi maka-connect
}

// Check kung naka-login na, kung oo, diretso sa products page
if (isset($_SESSION['user'])) {
    header("Location: products.php");
    exit();
}

// Check kung na-click yung login button
if (isset($_POST["btnLogin"])) {
    loginUser(); // Tawagin function para i-login yung user
}

// Function para i-sanitize input para safe sa HTML
function sanitize($str) { return htmlspecialchars(trim($str)); }

// Function para i-validate yung login form
function validateLogin($u, $p) {
    $errors = [];
    if ($u === '') $errors[] = "Username is required."; // Username required
    if ($p === '') $errors[] = "Password is required."; // Password required
    return $errors;
}

// Main login function
function loginUser() {
    global $connection;

    $username = sanitize($_POST["username"]); // Sanitize input username
    $password = sanitize($_POST["password"]); // Sanitize input password

    $errors = validateLogin($username, $password); // Validate input
    if (!empty($errors)) {
        // Ipakita error message kung may kulang
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    // Query sa users table
    $q = mysqli_query($connection, "SELECT * FROM users");
    $valid = false; $role = 'user';
    if ($q && mysqli_num_rows($q) > 0) {
        while ($row = mysqli_fetch_assoc($q)) {
            if ($row['username'] === $username) {
                $stored = $row['password'];
                // Check password using password_verify o plain text
                if (password_verify($password, $stored) || $stored === $password) {
                    $valid = true;
                    $role = $row['role'] ?? 'user'; // Kunin role kung available
                    break;
                }
            }
        }
    }

    if ($valid) {
        // Set session variables kapag valid
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $role;
        logLoginEvent($username, 'login'); // Log ang login action
        $_SESSION["message"] = '<div class="alert alert-success">Login successful. Welcome, ' . htmlspecialchars($username) . '!</div>';
        header("Location: products.php"); // Redirect sa products page
        exit();
    } else {
        // Show error kung invalid login
        $_SESSION["message"] = '<div class="alert alert-danger">Invalid username or password.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }
}

// Function para i-log yung login action sa activity_logs
function logLoginEvent($username, $action) {
    global $connection;
    $u = mysqli_real_escape_string($connection, $username);
    $a = mysqli_real_escape_string($connection, $action);
    mysqli_query($connection, "INSERT INTO activity_logs (username, action, product_id, details) VALUES ('$u', '$a', NULL, NULL)");
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8"> 
    <title>Login | Product Inventory System</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"> 
    <link href="assets/css/theme.css" rel="stylesheet"> 
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark margin">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="login.php">
                <img src="assets/img/prism-logo.png" class="prism-logo" alt="Prism Logo">
                <span class="brand-text brand-accent">PRISM</span>
            </a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <?php
                // Display message kung meron (success o error)
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                    unset($_SESSION['message']); // Clear message after showing
                }
                ?>
                <div class="card shadow-sm auth-card">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-3"><span class="brand-text brand-accent ">Inventory Login</span> </h4>
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
                            <button type="submit" name="btnLogin" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div class="d-flex justify-content-between mt-3">
                            <small class="text-muted">Use admin / admin123</small>
                            <a href="register.php" class="small">Create an account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Button para sa theme toggle -->
    <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle color theme" title="Toggle theme">
        <span class="icon-sun" aria-hidden="true"><i class="fa-solid fa-sun"></i></span>
        <span class="icon-moon" aria-hidden="true"><i class="fa-solid fa-moon"></i></span>
        <span class="label">Theme</span>
    </button>
    
    <!-- JS na mga ginamit -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/password-toggle.js"></script>
    <script src="assets/js/theme-toggle.js"></script>
    <script src="assets/js/ui-enhance.js"></script>
</body>
</html>
<?php ob_end_flush(); // End output buffering ?>
