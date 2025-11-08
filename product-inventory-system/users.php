<?php
ob_start();
session_start();

// Guard: kung hindi naka-login, redirect sa login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Connect sa database
$connection = mysqli_connect("localhost", "root", "", "inventory_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

// Role check: admin-only page
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$isAdmin = ($role === 'admin');
if (!$isAdmin) {
    $_SESSION["message"] = '<div class="alert alert-danger">Access denied: Admins only.</div>';
    header("Location: products.php");
    exit();
}

// Handlers: check kung may POST action
if (isset($_POST["create"])) { createUser(); }      // Create new user
if (isset($_POST["changeRole"])) { changeRole(); }  // Change role ng user
if (isset($_POST["changePass"])) { changePass(); }  // Change password ng user
if (isset($_POST["delete"])) { deleteUser(); }      // Delete user

// Helper: sanitize input para safe sa HTML
function sanitize($str) { return htmlspecialchars(trim($str)); }

// Validate username/password/role inputs
function validateUser($u, $p, $r, $isCreate=true) {
    $errors = [];
    if ($u === '') $errors[] = "Username is required.";
    if ($isCreate && $p === '') $errors[] = "Password is required.";
    if ($u !== '' && strlen($u) < 4) $errors[] = "Username must be at least 4 characters.";
    if ($isCreate && $p !== '' && strlen($p) < 6) $errors[] = "Password must be at least 6 characters.";
    if (!in_array($r, ['admin','user'], true)) $errors[] = "Invalid role.";
    return $errors;
}


//CRUD

// Create new user
function createUser() {
    global $connection;

    $username = sanitize($_POST["username"]);
    $password = sanitize($_POST["password"]);
    $role = sanitize($_POST["role"]);

    $errors = validateUser($username, $password, $role, true);

    // Check if username exists
    $exists = false;
    $q = mysqli_query($connection, "SELECT username FROM users WHERE username = '" . mysqli_real_escape_string($connection,$username) . "'");
    if ($q && mysqli_num_rows($q) > 0) $exists = true;
    if ($exists) { $errors[] = "Username is already taken."; }

    if (!empty($errors)) {
        // Show error messages
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    // Insert sa DB
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $u = mysqli_real_escape_string($connection, $username);
    $r = mysqli_real_escape_string($connection, $role);
    $sql = "INSERT INTO users (username, password, role) VALUES ('$u', '$hash', '$r')";
    if (mysqli_query($connection, $sql)) {
        $_SESSION["message"] = '<div class="alert alert-success">User created successfully.</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
    }
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

// Change role ng existing user
function changeRole() {
    global $connection;
    $uid = (int)$_POST["uid"];
    $role = sanitize($_POST["role"]);
    if (!in_array($role, ['admin','user'], true)) {
        $_SESSION["message"] = '<div class="alert alert-danger">Invalid role.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"])); exit();
    }
    $r = mysqli_real_escape_string($connection, $role);
    $sql = "UPDATE users SET role = '$r' WHERE uid = $uid";
    if (mysqli_query($connection, $sql)) {
        $_SESSION["message"] = '<div class="alert alert-success">Role updated.</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
    }
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

// Change password ng user
function changePass() {
    global $connection;
    $uid = (int)$_POST["uid"];
    $new = sanitize($_POST["new"]);
    if ($new === '' || strlen($new) < 6) {
        $_SESSION["message"] = '<div class="alert alert-danger">Password must be at least 6 characters.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"])); exit();
    }
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = '$hash' WHERE uid = $uid";
    if (mysqli_query($connection, $sql)) {
        $_SESSION["message"] = '<div class="alert alert-success">Password updated.</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
    }
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

// Delete user (hindi pwedeng self-delete)
function deleteUser() {
    global $connection;
    $uid = (int)$_POST["uid"];
    $self = $_SESSION['user'];
    $q = mysqli_query($connection, "SELECT username FROM users WHERE uid = $uid");
    $row = $q ? mysqli_fetch_assoc($q) : null;
    if ($row && $row['username'] === $self) {
        $_SESSION["message"] = '<div class="alert alert-warning">You cannot delete your own account.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    $sql = "DELETE FROM users WHERE uid = $uid";
    if (mysqli_query($connection, $sql)) {
        $_SESSION["message"] = '<div class="alert alert-success">User deleted.</div>';
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
    <title>Users | Product Inventory System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap + Theme CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/theme.css?v=7" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="products.php">
                <img src="assets/img/prism-logo.png" class="prism-logo" alt="Prism Logo">
                <span class="brand-text">PRISM</span>
            </a>
            <div class="ms-auto d-flex align-items-center">
                <a href="products.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="logs.php" class="btn btn-outline-light btn-sm me-2">Logs</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Flash messages -->
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>

        <div class="row g-4">
            <!-- Add User Form -->
            <div class="col-md-5">
                <div class="card shadow-sm card-lift">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Add User</h5>
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
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" name="create" class="btn btn-primary w-100">Create User</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="col-md-7">
                <div class="card shadow-sm card-lift users-card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">User Accounts</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle users-table">
                                <!-- remove Bootstrap's table-light so theme variables apply -->
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Password</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Fetch all users from DB
                                $rs = mysqli_query($connection, "SELECT * FROM users ORDER BY uid DESC");
                                if ($rs && mysqli_num_rows($rs) > 0) {
                                    while ($row = mysqli_fetch_assoc($rs)) {
                                        $uid   = (int)$row['uid'];
                                        $uname = htmlspecialchars($row['username']);
                                        $urole = htmlspecialchars($row['role']);

                                        echo '<tr>
                                            <td>'. $uid .'</td>
                                            <td>'. $uname .'</td>
                                            <td>
                                                <!-- Change role form -->
                                                <form action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'" method="POST" class="d-flex gap-2 align-items-center role-form">
                                                    <input type="hidden" name="uid" value="'. $uid .'">
                                                    <select name="role" class="form-select form-select-sm">
                                                        <option value="user"'. ($urole==='user'?' selected':'') .'>User</option>
                                                        <option value="admin"'. ($urole==='admin'?' selected':'') .'>Admin</option>
                                                    </select>
                                                    <button type="submit" name="changeRole" class="btn btn-sm btn-success px-3">Save</button>
                                                </form>
                                            </td>
                                            <td>
                                                <!-- Change password form -->
                                                <form id="passForm-'. $uid .'" action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'" method="POST" class="password-wrap password-toggle d-flex align-items-center gap-2">
                                                    <input type="hidden" name="uid" value="'. $uid .'">
                                                    <input type="password" name="new" class="form-control form-control-sm" placeholder="New password" data-toggle="password">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm toggle-visibility" aria-label="Show password">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-end">
                                                <div class="action-stack">
                                                    <!-- Submit password form -->
                                                    <button type="submit" form="passForm-'. $uid .'" name="changePass" value="1" class="btn btn-sm btn-update-blue">Update</button>

                                                    <!-- Delete user -->
                                                    <form action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'" method="POST" onsubmit="return confirm(\'Delete user '. $uname .'?\');" class="d-inline-block w-100">
                                                        <input type="hidden" name="uid" value="'. $uid .'">
                                                        <button type="submit" name="delete" class="btn btn-sm btn-danger w-100">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Theme toggle -->
    <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle color theme" title="Toggle theme">
        <span class="icon-sun" aria-hidden="true"><i class="fa-solid fa-sun"></i></span>
        <span class="icon-moon" aria-hidden="true"><i class="fa-solid fa-moon"></i></span>
        <span class="label">Theme</span>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/password-toggle.js?v=1"></script>
    <script src="assets/js/theme-toggle.js"></script>
    <script src="assets/js/ui-enhance.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>