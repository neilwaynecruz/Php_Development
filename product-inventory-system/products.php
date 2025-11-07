<?php
ob_start();
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "inventory_db");
if (!$connection) {
    die("No connection: " . mysqli_connect_error());
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$isAdmin = ($role === 'admin');

// Config
$lowThreshold = 10;
$showTotalToUser = true; // set to false if you want to hide stock value for non-admin users
$records_per_page = 23;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$view_mode = isset($_GET['view']) && $_GET['view'] === 'archived' ? 'archived' : 'active';
$is_archived_view = ($view_mode === 'archived');

if ($current_page < 1) $current_page = 1;

// Handlers (admin-only mutations)
if ($isAdmin) {
    if (isset($_POST["create"])) { createProduct(); }
    if (isset($_POST["btnUpdate"])) { updateProduct(); }
    if (isset($_POST["reset"])) { resetTable(); }
    if (isset($_POST["archive"])) { archiveProduct(); }
    if (isset($_POST["restore"])) { restoreProduct(); }
    if (isset($_POST["delete_permanent"])) { permanentlyDeleteProduct(); } // NEW: handle permanent delete
}

// Guard against unauthorized POST attempts
if (!$isAdmin && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    // Allow only search/filter for non-admin
    $allowed = isset($_POST['search']) || isset($_POST['filter']);
    if (!$allowed) {
        $_SESSION["message"] = '<div class="alert alert-danger">Access denied: You do not have permission to modify products.</div>';
        header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
        exit();
    }
}

function sanitize($str) { return htmlspecialchars(trim($str)); }

function validateProduct($n, $c, $q, $p) {
    $errors = [];
    $n = trim($n); $c = trim($c);
    if ($n === '') $errors[] = "Product name is required.";
    if ($c === '') $errors[] = "Category is required.";
    if ($q === '' || !is_numeric($q) || intval($q) < 0) $errors[] = "Quantity must be a non-negative integer.";
    if ($p === '' || !is_numeric($p) || floatval($p) < 0) $errors[] = "Price must be a non-negative number.";
    return $errors;
}

function logActivity($action, $productId, $details) {
    global $connection;
    $user = $_SESSION['user'] ?? 'unknown';
    $stmt = mysqli_prepare($connection, "INSERT INTO activity_logs (username, action, product_id, details) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssis", $user, $action, $productId, $details);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

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

    // Types: s = string, s = string, i = integer, d = double/float
    mysqli_stmt_bind_param($stmt, "ssid", $name, $category, $qty, $price);

    if (mysqli_stmt_execute($stmt)) {
        $newId = mysqli_insert_id($connection);
        logActivity('create', $newId, "Added: $name");
        $_SESSION["message"] = '<div class="alert alert-success">Product added!</div>';
    } else {
        $_SESSION["message"] = '<div class="alert alert-danger">Database error: '. htmlspecialchars(mysqli_stmt_error($stmt)) .'</div>';
    }
    mysqli_stmt_close($stmt);

    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

function updateProduct() {
    global $connection;

    $id = (int) $_POST["pid"];
    $name = sanitize($_POST["name"]);
    $category = sanitize($_POST["category"]);
    $qty = (int)$_POST["quantity"];        // cast
    $price = (float)$_POST["price"];       // cast

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

    // Types: s s i d i (matches 5 placeholders)
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


function resetTable() {
    global $connection;
    mysqli_query($connection, "TRUNCATE TABLE products");
    logActivity('delete', null, "reset products table");
    $_SESSION["message"] = '<div class="alert alert-warning">All products deleted and ID reset to 1.</div>';
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}

function permanentlyDeleteProduct() {
    global $connection;
    $id = (int)$_POST["delete_id"];
    $stmt = mysqli_prepare($connection, "DELETE FROM products WHERE product_id = ? AND is_archived = 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        logActivity('delete', $id, "Permanently deleted product ID: $id");
        $_SESSION["message"] = '<div class="alert alert-danger">Product permanently deleted.</div>';
    } else { $_SESSION["message"] = '<div class="alert alert-danger">Database error.</div>'; }
    mysqli_stmt_close($stmt);
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . "?view=archived");
    exit();
}

function searchProduct() {
    global $connection;

    $pid = isset($_POST["search-id"]) ? (int)$_POST["search-id"] : 0;

    $stmt = mysqli_prepare($connection, "SELECT product_id, name, category, quantity, price FROM products WHERE product_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $pid);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($res && ($row = mysqli_fetch_assoc($res))) {
            $id = (int)$row["product_id"];
            $name = htmlspecialchars($row["name"]);
            $category = htmlspecialchars($row["category"]);
            $qty = (int)$row["quantity"];
            $price = (float)$row["price"];

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

    echo '<div class="alert alert-danger mt-3">Product not found!</div>';
}
    $whereClause = $is_archived_view ? "is_archived = 1" : "is_archived = 0";
    $params = [];
    $types = '';

    $searchName = isset($_REQUEST['q']) ? sanitize($_REQUEST['q']) : '';
    $searchCategory = isset($_REQUEST['cat']) ? sanitize($_REQUEST['cat']) : '';

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

    // --- SECURE SUMMARY METRICS (REPLACES OLD CODE) ---
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
    $totalProducts = (int)$sum_row['total_products'];
    $totalValue = (float)$sum_row['total_value'];
    $lowCount = (int)$sum_row['low_count'];
    mysqli_stmt_close($stmt_sum);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products | Product Inventory System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap + Theme -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="products.php">
                <img src="assets/img/prism-logo.png" class="prism-logo" alt="Prism Logo">
                <span class="brand-text">PRISM</span>
            </a>
            <div class="ms-auto d-flex align-items-center">
            <span class="navbar-text me-3 text-white">
                Hello, <?php echo htmlspecialchars($_SESSION['user']); ?>
                <span class="badge bg-warning text-dark ms-2"><?php echo htmlspecialchars($role); ?></span>
            </span>
            <?php if ($isAdmin): ?>
                <a href="users.php" class="btn btn-outline-light btn-sm me-2">Users</a>
                <a href="logs.php" class="btn btn-outline-light btn-sm me-2">Logs</a>
            <?php endif; ?>
            <a href="account.php" class="btn btn-outline-light btn-sm me-2">Account</a>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>

        <div class="row g-4 mb-2">
            <div class="col-md-4">
                <div class="card shadow-sm stat-card card-lift">
                    <div class="card-body">
                        <h6 class="card-title">Total <?php echo $is_archived_view ? 'Archived' : 'Active'; ?> Products</h6>
                        <div class="display-6"><?php echo number_format($totalProducts); ?></div>
                    </div>
                </div>
            </div>
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

        <!-- FIX: Filter Form now uses GET to support pagination -->
        <div class="card shadow-sm card-lift mb-4">
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET" class="row g-2 align-items-end">
                    <!-- This hidden input ensures we stay in the archived view when filtering -->
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
            <div class="col-md-5">
                <?php if ($isAdmin): ?>
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
                <div class="card shadow-sm card-lift">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Search & Update (by ID)</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="row g-2">
                            <!-- Search Form remains the same -->
                            <div class="col-8"><input type="number" name="search-id" class="form-control" placeholder="Ex: 3" required step="1" min="1"></div>
                            <div class="col-4"><button type="submit" name="search" class="btn btn-secondary w-100">Search</button></div>
                        </form>
                        <div class="form-text mt-2">If found, an edit form will appear below.</div>
                        <?php if ($isAdmin && isset($_POST["search"])) { searchProduct(); } ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm card-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <!-- FIX: Dynamic Title and View Toggle Link -->
                            <h5 class="card-title mb-0"><?php echo $is_archived_view ? 'Archived Products' : 'Active Products'; ?></h5>
                            <div>
                                <?php if ($is_archived_view): ?>
                                    <a href="products.php" class="btn btn-outline-secondary btn-sm">View Active Products</a>
                                <?php else: ?>
                                    <a href="products.php?view=archived" class="btn btn-outline-secondary btn-sm">View Archived</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th><th>Name</th><th>Category</th><th>Qty</th><th>Price</th><th>Total</th>
                                        <?php if ($isAdmin): ?><th class="text-end">Action</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // --- FIX: Replaced old query with the new, secure prepared statement ---
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

                                        $row_class = $isLow && !$is_archived_view ? 'table-warning' : '';

                                        echo "<tr class='$row_class'>
                                            <td>{$row['product_id']}</td>
                                            <td>" . htmlspecialchars($row['name']) . "</td>
                                            <td>" . htmlspecialchars($row['category']) . "</td>
                                            <td>" . ($isLow && !$is_archived_view ? "<span class='badge bg-warning text-dark'>{$row['quantity']}</span>" : $row['quantity']) . "</td>
                                            <td>₱" . number_format($row['price'], 2) . "</td>
                                            <td>₱" . number_format($row['quantity'] * $row['price'], 2) . "</td>";

                                        if ($isAdmin) {
                                                echo '<td class="text-end">';
                                            if ($is_archived_view) {
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
                                                // This part for the "Archive" button is already correct and doesn't need to be changed.
                                                echo '<form action="products.php" method="POST" class="d-inline" onsubmit="return confirm(\'Archive this product?\');">
                                                        <input type="hidden" name="archive_id" value="'.$row['product_id'].'">
                                                        <button type="submit" name="archive" class="btn btn-sm btn-warning">Archive</button>
                                                    </form>';
                                            }
                                            
                                            echo '</td>';
                                        }
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center text-dark">No products found.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- FIX: Added Pagination Controls -->
                        <?php if ($total_pages > 1):
                            // This ensures filters are kept when changing pages
                            $filter_query_string = http_build_query(['q' => $searchName, 'cat' => $searchCategory, 'view' => $view_mode]);
                        ?>
                        <nav class="mt-4" aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
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

    <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle color theme" title="Toggle theme">
        <span class="icon-sun" aria-hidden="true"><i class="fa-solid fa-sun"></i></span>
        <span class="icon-moon" aria-hidden="true"><i class="fa-solid fa-moon"></i></span>
        <span class="label">Theme</span>
    </button>
    
    
</body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme-toggle.js?v=3"></script>
    <script src="assets/js/ui-enhance.js"></script>
</html>
<?php ob_end_flush(); ?>