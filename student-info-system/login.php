<?php
ob_start();
session_start();

$connection = mysqli_connect("localhost", "root", "", "iskolar_sis_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

if (isset($_SESSION['user'])) {
    header("Location: students.php");
    exit();
}

if (isset($_POST["btnLogin"])) {
    loginUser();
}

function sanitize($str) { return htmlspecialchars(trim($str)); }

function validateLogin($u, $p) {
    $errors = [];
    if ($u === '') $errors[] = "Username is required.";
    if ($p === '') $errors[] = "Password is required.";
    return $errors;
}

function loginUser() {
    global $connection;

    $username = sanitize($_POST["username"]);
    $password = sanitize($_POST["password"]);

    $errors = validateLogin($username, $password);
    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    $q = mysqli_query($connection, "SELECT * FROM users");
    $valid = false;
    if ($q && mysqli_num_rows($q) > 0) {
        while ($row = mysqli_fetch_assoc($q)) {
            if ($row['username'] === $username) {
                $stored = $row['password'];
                // Support both hashed and plain passwords
                if (password_verify($password, $stored) || $stored === $password) {
                    $valid = true; break;
                }
            }
        }
    }

    if ($valid) {
        $_SESSION['user'] = $username;
        $_SESSION["message"] = '<div class="alert alert-success">Login successful. Welcome, ' . htmlspecialchars($username) . '!</div>';
        header("Location: students.php");
        exit();
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Invalid username or password.</div>';
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
    <nav class="navbar navbar-expand-lg navbar-dark margin">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" style="margin-top: 4px;" href="login.php">
                <span class="brand-dot"></span> Student Info System
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
                        <h4 class="text-center mb-3">Student Information System</h4>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
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

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>