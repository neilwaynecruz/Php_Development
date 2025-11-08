<?php
ob_start(); // start output buffering para safe ang header redirects later
session_start(); // start session para ma-access user info

// Check kung naka-login, kung hindi redirect sa login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Connect sa MySQL database
$connection = mysqli_connect("localhost", "root", "", "inventory_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error()); // if fail, show error
}

// Get role ng user, default 'user' kung wala
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$isAdmin = ($role === 'admin'); // boolean para sa admin checks

//  Configuration 
$lowThreshold = 10; // threshold para sa low stock warning
$showTotalToUser = true; // show stock value sa non-admin users
$records_per_page = 23; // pagination
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$view_mode = isset($_GET['view']) && $_GET['view'] === 'archived' ? 'archived' : 'active';
$is_archived_view = ($view_mode === 'archived');

if ($current_page < 1) $current_page = 1; // prevent negative page numbers

//    Admin-only POST handlers 
if ($isAdmin) {
    if (isset($_POST["create"])) { createProduct(); } // add product
    if (isset($_POST["btnUpdate"])) { updateProduct(); } // update product
    if (isset($_POST["reset"])) { resetTable(); } // truncate table
    if (isset($_POST["archive"])) { archiveProduct(); } // archive product
    if (isset($_POST["restore"])) { restoreProduct(); } // restore archived
    if (isset($_POST["delete_permanent"])) { permanentlyDeleteProduct(); } // permanent delete
}

// Guard for non-admins na nag-attempt mag POST
if (!$isAdmin && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    $allowed = isset($_POST['search']) || isset($_POST['filter']); // only allow search/filter
    if (!$allowed) {
        $_SESSION["message"] = '<div class="alert alert-danger">Access denied: You do not have permission to modify products.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }
}

//  Helper functions 

// sanitize input para safe sa HTML at SQL injection
function sanitize($str) { return htmlspecialchars(trim($str)); }

// validation ng product input
function validateProduct($n, $c, $q, $p) {
    $errors = [];
    $n = trim($n); $c = trim($c);
    if ($n === '') $errors[] = "Product name is required.";
    if ($c === '') $errors[] = "Category is required.";
    if ($q === '' || !is_numeric($q) || intval($q) < 0) $errors[] = "Quantity must be a non-negative integer.";
    if ($p === '' || !is_numeric($p) || floatval($p) < 0) $errors[] = "Price must be a non-negative number.";
    return $errors; // return array of errors kung meron
}

// log activity sa activity_logs table
function logActivity($action, $productId, $details) {
    global $connection;
    $user = $_SESSION['user'] ?? 'unknown';
    $stmt = mysqli_prepare($connection, "INSERT INTO activity_logs (username, action, product_id, details) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        // productId may be null
        mysqli_stmt_bind_param($stmt, "ssis", $user, $action, $productId, $details);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

//   CRUD functions 

// Create/Add product
function createProduct() {
    global $connection;

    $name = sanitize($_POST["pName"]);
    $category = sanitize($_POST["pCategory"]);
    $qty = (int)$_POST["pQty"];         
    $price = (float)$_POST["pPrice"];    

    $errors = validateProduct($name, $category, $qty, $price);
    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    $stmt = mysqli_prepare($connection, "INSERT INTO products (name, category, quantity, price) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $_SESSION["message"] = '<div class="alert alert-danger">Prepare failed: '. htmlspecialchars(mysqli_error($connection)) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"])); exit();
    }

    // bind parameters: s=string, i=int, d=float
    mysqli_stmt_bind_param($stmt, "ssid", $name, $category, $qty, $price);

    if (mysqli_stmt_execute($stmt)) {
        $newId = mysqli_insert_id($connection); // get new product id
        logActivity('create', $newId, "Added: $name"); // log action
        $_SESSION["message"] = '<div class="alert alert-success">Product added!</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Database error: '. htmlspecialchars(mysqli_stmt_error($stmt)) .'</div>';
    }
    mysqli_stmt_close($stmt);

    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

// Update product
function updateProduct() {
    global $connection;

    $id = (int) $_POST["pid"];
    $name = sanitize($_POST["name"]);
    $category = sanitize($_POST["category"]);
    $qty = (int)$_POST["quantity"];
    $price = (float)$_POST["price"];

    $errors = validateProduct($name, $category, $qty, $price);
    if (!empty($errors)) {
        $_SESSION["message"] = '<div class="alert alert-danger">'. implode("<br>", $errors) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }

    $stmt = mysqli_prepare($connection, "UPDATE products SET name = ?, category = ?, quantity = ?, price = ? WHERE product_id = ?");
    if (!$stmt) {
        $_SESSION["message"] = '<div class="alert alert-danger">Prepare failed: '. htmlspecialchars(mysqli_error($connection)) .'</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"])); exit();
    }

    mysqli_stmt_bind_param($stmt, "ssidi", $name, $category, $qty, $price, $id);

    if (mysqli_stmt_execute($stmt)) {
        logActivity('update', $id, "Updated: $name");
        $_SESSION["message"] = '<div class="alert alert-success">Product updated!</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Database error: '. htmlspecialchars(mysqli_stmt_error($stmt)) .'</div>';
    }

    mysqli_stmt_close($stmt);
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

// Archive product
function archiveProduct() {
    global $connection;
    $id = (int)$_POST["archive_id"];
    $stmt = mysqli_prepare($connection, "UPDATE products SET is_archived = 1 WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        logActivity('archive', $id, "Archived product ID: $id");
        $_SESSION["message"] = '<div class="alert alert-warning">Product archived.</div>';
    } else { $_SESSION["message"] = '<div class="alert alert-danger">Database error.</div>'; }
    mysqli_stmt_close($stmt);
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

// Restore archived product
function restoreProduct() {
    global $connection;
    $id = (int)$_POST["restore_id"];
    $stmt = mysqli_prepare($connection, "UPDATE products SET is_archived = 0 WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        logActivity('restore', $id, "Restored product ID: $id");
        $_SESSION["message"] = '<div class="alert alert-success">Product restored.</div>';
    } else { $_SESSION["message"] = '<div class="alert alert-danger">Database error.</div>'; }
    mysqli_stmt_close($stmt);
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . "?view=archived");
    exit();
}

// Reset table (truncate)
function resetTable() {
    global $connection;
    mysqli_query($connection, "TRUNCATE TABLE products");
    logActivity('delete', null, "reset products table");
    $_SESSION["message"] = '<div class="alert alert-warning">All products deleted and ID reset to 1.</div>';
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

// Permanently delete yung archived product (and resequence IDs)
function permanentlyDeleteProduct() {
    global $connection;
    $id = (int)$_POST["delete_id"];
    $stmt = mysqli_prepare($connection, "DELETE FROM products WHERE product_id = ? AND is_archived = 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        // Log deletion of old id
        logActivity('delete', $id, "Permanently deleted product ID: $id");

        // Resequence product IDs and update activity_logs to map to new IDs
        $ok = resequenceProducts();
        if ($ok) {
            $_SESSION["message"] = '<div class="alert alert-danger">Product permanently deleted. IDs resequenced.</div>';
        } else {
            $_SESSION["message"] = '<div class="alert alert-warning">Product deleted but resequence failed. Check logs.</div>';
        }
    } else { $_SESSION["message"] = '<div class="alert alert-danger">Database error.</div>'; }
    mysqli_stmt_close($stmt);
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . "?view=archived");
    exit();
}

/**
 * Re-sequence product IDs and update activity_logs to match new IDs.
 * Returns true on success, false on failure.
 */
function resequenceProducts() {
    global $connection;

    // Fetch all rows ordered by current product_id
    $rows = [];
    $q = mysqli_query($connection, "SELECT product_id, name, category, quantity, price, IFNULL(is_archived,0) AS is_archived FROM products ORDER BY product_id");
    if (!$q) return false;

    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = $r;
    }

    if (empty($rows)) return true; // nothing to do

    // Start transaction
    if (!mysqli_begin_transaction($connection)) {
        error_log("Failed to start transaction for resequence: " . mysqli_error($connection));
        return false;
    }

    try {
        // Create mapping array
        $mapping = [];

        // Temporarily rename table to keep a backup (safer than raw truncate)
        $temp = 'products_backup_' . time();
        if (!mysqli_query($connection, "RENAME TABLE products TO $temp")) {
            throw new Exception('Rename products failed: ' . mysqli_error($connection));
        }

        // Create a new empty products table using backup structure
        if (!mysqli_query($connection, "CREATE TABLE products LIKE $temp")) {
            throw new Exception('Create table like failed: ' . mysqli_error($connection));
        }

        // Prepare insert (omit product_id so auto_increment assigns new ids)
        $ins = mysqli_prepare($connection, "INSERT INTO products (name, category, quantity, price, is_archived) VALUES (?, ?, ?, ?, ?)");
        if (!$ins) throw new Exception('Prepare insert failed: ' . mysqli_error($connection));

        foreach ($rows as $oldRow) {
            $name = $oldRow['name'];
            $category = $oldRow['category'];
            $qty = (int)$oldRow['quantity'];
            $price = (float)$oldRow['price'];
            $arch = (int)$oldRow['is_archived'];

            mysqli_stmt_bind_param($ins, "ssidi", $name, $category, $qty, $price, $arch);
            if (!mysqli_stmt_execute($ins)) {
                throw new Exception('Insert failed: ' . mysqli_stmt_error($ins));
            }
            $newId = mysqli_insert_id($connection);
            $mapping[(int)$oldRow['product_id']] = $newId;
        }
        mysqli_stmt_close($ins);

        // Update activity_logs product_id references using mapping
        $upd = mysqli_prepare($connection, "UPDATE activity_logs SET product_id = ? WHERE product_id = ?");
        if (!$upd) throw new Exception('Prepare update logs failed: ' . mysqli_error($connection));

        foreach ($mapping as $oldId => $newId) {
            // Only update if mapping changed
            if ($oldId === $newId) continue;
            mysqli_stmt_bind_param($upd, "ii", $newId, $oldId);
            if (!mysqli_stmt_execute($upd)) {
                throw new Exception('Update logs failed: ' . mysqli_stmt_error($upd));
            }
        }
        mysqli_stmt_close($upd);

        // Drop backup table
        if (!mysqli_query($connection, "DROP TABLE IF EXISTS $temp")) {
            // Not fatal, just warn
            error_log("Warning: failed to drop backup table $temp: " . mysqli_error($connection));
        }

        // Commit
        mysqli_commit($connection);
        return true;
    } catch (Exception $ex) {
        // Rollback and log
        mysqli_rollback($connection);
        error_log('Resequence failed: ' . $ex->getMessage());
        // Attempt best-effort restore: If backup exists, rename back (may require manual intervention)
        return false;
    }
}

// Search product by ID (for update form)
function searchProduct() {
    global $connection;

    $pid = isset($_POST["search-id"]) ? (int)$_POST["search-id"] : 0;

    $stmt = mysqli_prepare($connection, "SELECT product_id, name, category, quantity, price FROM products WHERE product_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $pid);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($res && ($row = mysqli_fetch_assoc($res))) {
            // prepare variables for edit form
            $id = (int)$row["product_id"];
            $name = htmlspecialchars($row["name"]);
            $category = htmlspecialchars($row["category"]);
            $qty = (int)$row["quantity"];
            $price = (float)$row["price"];

            // display edit form
            echo '<div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Update Product</h5>
                        <form action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Product ID</label>
                                <input type="number" name="pid" value="'. $id .'" readonly class="form-control" step="1">
                                <div class="form-text">read only</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" value="'. $name .'" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" value="'. $category .'" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" value="'. $qty .'" class="form-control" min="0" step="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <input type="number" name="price" value="'. number_format($price, 2, ".", "") .'" class="form-control" min="0" step="0.01" required>
                            </div>
                            <button type="submit" name="btnUpdate" class="btn btn-success w-100">Update</button>
                        </form>
                    </div>
                  </div>';
            mysqli_stmt_close($stmt);
            return;
        }
        mysqli_stmt_close($stmt);
    }

    // kung hindi nakita, show error
    echo '<div class="alert alert-danger mt-3">Product not found!</div>';
}

// --- Filtering, pagination and summary setup ---
$whereClause = $is_archived_view ? "is_archived = 1" : "is_archived = 0";
$params = [];
$types = '';

$searchName = isset($_REQUEST['q']) ? sanitize($_REQUEST['q']) : '';
$searchCategory = isset($_REQUEST['cat']) ? sanitize($_REQUEST['cat']) : '';

// build WHERE clause dynamically based on search
if ($searchName !== '') {
    $whereClause .= " AND (name LIKE ? OR category LIKE ?)";
    $types .= 'ss';
    $params[] = "%$searchName%";
    $params[] = "%$searchName%";
}
if ($searchCategory !== '') {
    $whereClause .= " AND category LIKE ?";
    $types .= 's';
    $params[] = "%$searchCategory%";
}

// Count total records for pagination
$total_records_query = "SELECT COUNT(*) AS total FROM products WHERE $whereClause";
$stmt_total = mysqli_prepare($connection, $total_records_query);
if (!empty($params)) mysqli_stmt_bind_param($stmt_total, $types, ...$params);
mysqli_stmt_execute($stmt_total);
$total_records = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_total))['total'];
mysqli_stmt_close($stmt_total);
$total_pages = ceil($total_records / $records_per_page);
$offset = ($current_page - 1) * $records_per_page;

// --- Summary metrics for dashboard cards ---
$sumQuery = "SELECT COUNT(*) AS total_products, COALESCE(SUM(quantity * price), 0) AS total_value, SUM(CASE WHEN quantity <= ? THEN 1 ELSE 0 END) AS low_count FROM products WHERE $whereClause";
$stmt_sum = mysqli_prepare($connection, $sumQuery);
$sum_params = array_merge([$lowThreshold], $params);
$sum_types = 'i' . $types;
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt_sum, $sum_types, ...$sum_params);
} else {
    mysqli_stmt_bind_param($stmt_sum, 'i', $lowThreshold);
}
mysqli_stmt_execute($stmt_sum);
$sum_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_sum));
$totalProducts = (int)$sum_row['total_products']; // total number of products
$totalValue = (float)$sum_row['total_value']; // total stock value
$lowCount = (int)$sum_row['low_count']; // count of low stock items
mysqli_stmt_close($stmt_sum);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products | Product Inventory System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap + Theme CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/theme.css?v=6" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar: dito makikita yung logo, greeting sa user at mga links -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="products.php">
                <img src="assets/img/prism-logo.png" class="prism-logo" alt="Prism Logo">
                <span class="brand-text">PRISM</span>
            </a>
            <div class="ms-auto d-flex align-items-center">
                <!-- Greeting at role ng user -->
                <span class="navbar-text me-3 text-white">
                    Hello, <?php echo htmlspecialchars($_SESSION['user']); ?>
                    <span class="badge bg-warning text-dark ms-2"><?php echo htmlspecialchars($role); ?></span>
                </span>
                <!-- Admin-only links -->
                <?php if ($isAdmin): ?>
                    <a href="users.php" class="btn btn-outline-light btn-sm me-2">Users</a>
                    <a href="logs.php" class="btn btn-outline-light btn-sm me-2">Logs</a>
                <?php endif; ?>
                <!-- Lahat makikita -->
                <a href="account.php" class="btn btn-outline-light btn-sm me-2">Account</a>
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

        <!-- Summary Cards: Total products, Stock Value, Low-Stock -->
        <div class="row g-4 mb-2">
            <div class="col-md-4">
                <div class="card shadow-sm stat-card card-lift">
                    <div class="card-body">
                        <h6 class="card-title">Total <?php echo $is_archived_view ? 'Archived' : 'Active'; ?> Products</h6>
                        <div class="display-6"><?php echo number_format($totalProducts); ?></div>
                    </div>
                </div>
            </div>

            <!-- Only admin or allowed users makikita yung Total Stock Value -->
            <?php if (($isAdmin || $showTotalToUser) && !$is_archived_view): ?>
            <div class="col-md-4">
                <div class="card shadow-sm stat-card card-lift">
                    <div class="card-body">
                        <h6 class="card-title">Total Stock Value</h6>
                        <div class="display-6">₱<?php echo number_format($totalValue, 2); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Low stock count, only sa active products -->
            <?php if (!$is_archived_view): ?>
            <div class="col-md-4">
                <div class="card shadow-sm stat-card card-lift">
                    <div class="card-body">
                        <h6 class="card-title">Low-Stock (≤ <?php echo (int)$lowThreshold; ?>)</h6>
                        <div class="display-6"><?php echo number_format($lowCount); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Filter Form: GET method para sa pagination support -->
        <div class="card shadow-sm card-lift mb-4">
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET" class="row g-2 align-items-end">
                    <!-- Hidden input para manatili sa archived view kung nasa archived -->
                    <?php if($is_archived_view): ?><input type="hidden" name="view" value="archived"><?php endif; ?>
                    <div class="col-md-5">
                        <label class="form-label">Search (name/category)</label>
                        <input type="text" name="q" class="form-control" value="<?php echo htmlspecialchars($searchName); ?>" placeholder="Ex: Ballpen or Supplies">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Category contains</label>
                        <input type="text" name="cat" class="form-control" value="<?php echo htmlspecialchars($searchCategory); ?>" placeholder="Ex: School">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Add Product / Search by ID -->
            <div class="col-md-5">
                <?php if ($isAdmin): ?>
                <!-- Add Product Form -->
                <div class="card shadow-sm card-lift mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Add Product</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                            <div class="mb-3"><label class="form-label">Name</label><input type="text" name="pName" class="form-control" placeholder="Ex: Ballpen" required></div>
                            <div class="mb-3"><label class="form-label">Category</label><input type="text" name="pCategory" class="form-control" placeholder="Ex: School Supplies" required></div>
                            <div class="mb-3"><label class="form-label">Quantity</label><input type="number" name="pQty" class="form-control" placeholder="Ex: 10" min="0" step="1" required></div>
                            <div class="mb-3"><label class="form-label">Price</label><input type="number" name="pPrice" class="form-control" placeholder="Ex: 25.50" min="0" step="0.01" required></div>
                            <button type="submit" name="create" class="btn btn-primary w-100">Submit</button>
                        </form>
                    </div>
                </div>

                <!-- Search by ID Form -->
                <div class="card shadow-sm card-lift">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Search & Update (by ID)</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="row g-2">
                            <!-- Input field para sa product ID -->
                            <div class="col-8"><input type="number" name="search-id" class="form-control" placeholder="Ex: 3" required step="1" min="1"></div>
                            <div class="col-4"><button type="submit" name="search" class="btn btn-secondary w-100">Search</button></div>
                        </form>
                        <div class="form-text mt-2">If found, an edit form will appear below.</div>
                        <?php if ($isAdmin && isset($_POST["search"])) { searchProduct(); } ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Product Table -->
            <div class="col-md-7">
                <div class="card shadow-sm card-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <!-- Table Title + Toggle Archived/Active + Reset -->
                            <h5 class="card-title mb-0"><?php echo $is_archived_view ? 'Archived Products' : 'Active Products'; ?></h5>
                            <div class="d-flex gap-2 align-items-center">
                                <?php if ($isAdmin): ?>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" onsubmit="return confirm('This will delete all products. Continue?');" class="d-inline">
                                        <button type="submit" name="reset" class="btn btn-outline-danger btn-sm">Reset All</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($is_archived_view): ?>
                                    <a href="products.php" class="btn btn-outline-secondary btn-sm">View Active Products</a>
                                <?php else: ?>
                                    <a href="products.php?view=archived" class="btn btn-outline-secondary btn-sm">View Archived</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th><th>Name</th><th>Category</th><th>Qty</th><th>Price</th><th>Total</th>
                                        <?php if ($isAdmin): ?><th class="text-end">Action</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Query gamit prepared statements para sa security
                                $selectQuery = "SELECT * FROM products WHERE $whereClause ORDER BY product_id DESC LIMIT ? OFFSET ?";
                                $stmt_main = mysqli_prepare($connection, $selectQuery);
                                $main_params = array_merge($params, [$records_per_page, $offset]);
                                $main_types = $types . 'ii';
                                if (!empty($main_params)) mysqli_stmt_bind_param($stmt_main, $main_types, ...$main_params);
                                mysqli_stmt_execute($stmt_main);
                                $getRecord = mysqli_stmt_get_result($stmt_main);

                                if ($getRecord && mysqli_num_rows($getRecord) > 0) {
                                    while ($row = mysqli_fetch_assoc($getRecord)) {
                                        $isLow = ($row['quantity'] <= $lowThreshold);
                                        // semantic adaptive row class
                                        $row_class = $isLow && !$is_archived_view ? 'row-low-stock' : 'row-normal';

                                        echo "<tr class='$row_class'>
                                            <td class='text-white'>{$row['product_id']}</td>
                                            <td class='text-white'>" . htmlspecialchars($row['name']) . "</td>
                                            <td class='text-white'>" . htmlspecialchars($row['category']) . "</td>
                                            <td class='text-white'>" . ($isLow && !$is_archived_view ? "<span class='qty-alert-badge text-white' title='Below threshold'>{$row['quantity']}</span>" : (int)$row['quantity']) . "</td>
                                            <td class='text-white'>₱" . number_format($row['price'], 2) . "</td>
                                            <td class='text-white'>₱" . number_format($row['quantity'] * $row['price'], 2) . "</td>";

                                        if ($isAdmin) {
                                            echo '<td class="text-end">';
                                            if ($is_archived_view) {
                                                // Archived view: Restore / Permanent Delete
                                                echo '<div class="d-flex flex-column gap-1">
                                                        <form action="products.php?view=archived" method="POST">
                                                            <input type="hidden" name="restore_id" value="'.$row['product_id'].'">
                                                            <button type="submit" name="restore" class="btn btn-sm btn-success w-100">Restore</button>
                                                        </form>
                                                        <form action="products.php?view=archived" method="POST" onsubmit="return confirm(\'PERMANENTLY DELETE? This cannot be undone.\');">
                                                            <input type="hidden" name="delete_id" value="'.$row['product_id'].'">
                                                            <button type="submit" name="delete_permanent" class="btn btn-sm w-100 btn-danger">Delete</button>
                                                        </form>
                                                    </div>';
                                            } else {
                                                // Active view: Archive button
                                                echo '<form action="products.php" method="POST" class="d-inline" onsubmit="return confirm(\'Archive this product?\');">
                                                        <input type="hidden" name="archive_id" value="'.$row['product_id'].'">
                                                        <button type="submit" name="archive" class="btn btn-sm btn-archive">Archive</button>
                                                    </form>';
                                            }
                                            echo '</td>';
                                        }
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center text-white" style="background-color: #151b24;">No products found.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1):
                            $filter_query_string = http_build_query(['q' => $searchName, 'cat' => $searchCategory, 'view' => $view_mode]);
                        ?>
                        <nav class="mt-4" aria-label="Page navigation">
                            <ul class="pagination pagination-theme justify-content-center">
                                <li class="page-item <?php if($current_page <= 1) echo 'disabled'; ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&<?php echo $filter_query_string; ?>">Previous</a>
                                </li>
                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if($current_page == $i) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo $filter_query_string; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                <li class="page-item <?php if($current_page >= $total_pages) echo 'disabled'; ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&<?php echo $filter_query_string; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
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

</body>
<!-- JS na mga ginamit -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/theme-toggle.js?v=3"></script>
<script src="assets/js/ui-enhance.js"></script>
</html>
<?php ob_end_flush(); ?>