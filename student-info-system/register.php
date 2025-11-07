<?php
ob_start();
session_start();

$connection = mysqli_connect("localhost", "root", "", "iskolar_sis_db");
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

    // Check if username exists
    $exists = false;
    $q = mysqli_query($connection, "SELECT * FROM users");
    if ($q && mysqli_num_rows($q) > 0) {
        while ($row = mysqli_fetch_assoc($q)) {
            if ($row['username'] === $username) { $exists = true; break; }
        }
    }
    if ($exists) { $errors[] = "Username is already taken."; }

    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hash')";
    if (mysqli_query($connection, $sql)) {
        $_SESSION["message"] = '<div class="alert alert-success">Account created! You can now login.</div>';
        header("Location: login.php");
        exit();
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8"> 
    <title>Students | Student Info System</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <!-- Bootstrap v5 + Theme --> 
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"> 
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"> 
     <link href="assets/css/theme.css" rel="stylesheet"> 
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" style="margin-top: 4px;" href="login.php">
                <span class="brand-dot"></span> Student Info System
            </a>
            <!-- <div class="ms-auto">
                <a href="login.php" class="btn btn-outline-light btn-sm">Login</a>
            </div> -->
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
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm" class="form-control" placeholder="Confirm password" required>
                            </div>
                            <button type="submit" name="btnRegister" class="btn btn-primary w-100">Register</button>
                        </form>
                        <p class="text-center mt-3 text-muted" style="font-size: 13px;">Already have an account? <a href="login.php">Login here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>