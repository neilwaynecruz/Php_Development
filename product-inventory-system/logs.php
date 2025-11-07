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
if (!$isAdmin) {
    $_SESSION["message"] = '<div class="alert alert-danger">Access denied: Admins only.</div>';
    header("Location: products.php");
    exit();
}

function sanitize($s) { return htmlspecialchars(trim($s)); }

/* Map action -> badge CSS class */
function actionBadgeClass($action) {
    switch ($action) {
        case 'create':  return 'badge-action badge-create';
        case 'update':  return 'badge-action badge-update';
        case 'delete':  return 'badge-action badge-delete';
        case 'login':   return 'badge-action badge-login';
        case 'logout':  return 'badge-action badge-logout';
        case 'archive': return 'badge-action badge-archive';
        case 'restore': return 'badge-action badge-restore';
        default:        return 'badge-action badge-generic';
    }
}

/* Pagination size (adjust as you like) */
$records_per_page = 23;

/* Current page from GET (default 1) */
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

/* Read filters (POST when submitting form, GET when clicking pagination) */
$filterUser = '';
$filterAction = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
    $filterUser = sanitize($_POST['user'] ?? '');
    $filterAction = sanitize($_POST['action'] ?? '');
} else {
    $filterUser = sanitize($_GET['user'] ?? '');
    $filterAction = sanitize($_GET['action'] ?? '');
}

/* Build WHERE + bind params */
$where = "1=1";
$params = [];
$types  = '';

if ($filterUser !== '') {
    $where  .= " AND username LIKE ?";
    $types  .= 's';
    $params[] = "%{$filterUser}%";
}
if ($filterAction !== '') {
    $where  .= " AND action = ?";
    $types  .= 's';
    $params[] = $filterAction;
}

/* Count total for pagination */
$total_records_query = "SELECT COUNT(*) AS total FROM activity_logs WHERE $where";
$stmt_total = mysqli_prepare($connection, $total_records_query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt_total, $types, ...$params);
}
mysqli_stmt_execute($stmt_total);
$total_result  = mysqli_stmt_get_result($stmt_total);
$total_records = (int)mysqli_fetch_assoc($total_result)['total'];
mysqli_stmt_close($stmt_total);

$total_pages = max(1, (int)ceil($total_records / $records_per_page));
if ($current_page > $total_pages) $current_page = $total_pages;
$offset = ($current_page - 1) * $records_per_page;

/* Fetch page data */
$query  = "SELECT * FROM activity_logs WHERE $where ORDER BY log_id DESC LIMIT ? OFFSET ?";
$types_with_paging = $types . 'ii';
$params_with_paging = array_merge($params, [$records_per_page, $offset]);

$stmt = mysqli_prepare($connection, $query);
if (!empty($params_with_paging)) {
    mysqli_stmt_bind_param($stmt, $types_with_paging, ...$params_with_paging);
}
mysqli_stmt_execute($stmt);
$rs = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs | Product Inventory System</title>
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
                <a href="products.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="users.php"   class="btn btn-outline-light btn-sm me-2">Users</a>
                <a href="logout.php"  class="btn btn-outline-light btn-sm">Logout</a>
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

        <div class="card shadow-sm card-lift mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Filter Logs</h5>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="row g-2">
                    <div class="col-md-5">
                        <label class="form-label">Username contains</label>
                        <input type="text" name="user" class="form-control" value="<?php echo htmlspecialchars($filterUser); ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select">
                            <option value="">All</option>
                            <option value="login"   <?php echo $filterAction==='login'  ?'selected':''; ?>>login</option>
                            <option value="logout"  <?php echo $filterAction==='logout' ?'selected':''; ?>>logout</option>
                            <option value="create"  <?php echo $filterAction==='create' ?'selected':''; ?>>create</option>
                            <option value="update"  <?php echo $filterAction==='update' ?'selected':''; ?>>update</option>
                            <option value="delete"  <?php echo $filterAction==='delete' ?'selected':''; ?>>delete</option>
                            <option value="archive" <?php echo $filterAction==='archive'?'selected':''; ?>>archive</option>
                            <option value="restore" <?php echo $filterAction==='restore'?'selected':''; ?>>restore</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="filter" class="btn btn-secondary w-100">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm card-lift">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                    <small class="text-muted">
                        Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>
                        (<?php echo number_format($total_records); ?> total)
                    </small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Product ID</th>
                                <th>Details</th>
                                <th>When</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($rs && mysqli_num_rows($rs) > 0) {
                            while ($row = mysqli_fetch_assoc($rs)) {
                                $action = htmlspecialchars($row['action']);
                                $badgeClass = actionBadgeClass($row['action']);
                                echo '<tr>
                                    <td>'. (int)$row['log_id'] .'</td>
                                    <td>'. htmlspecialchars($row['username']) .'</td>
                                    <td><span class="'. $badgeClass .'">'. $action .'</span></td>
                                    <td>'. ($row['product_id'] !== null ? (int)$row['product_id'] : '-') .'</td>
                                    <td style="max-width: 420px;">'. nl2br(htmlspecialchars($row['details'])) .'</td>
                                    <td>'. htmlspecialchars($row['created_at']) .'</td>
                                </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center text-muted">No logs found.</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <?php
                if ($total_pages > 1):
                    $filter_query_string = http_build_query([
                        'user'   => $filterUser,
                        'action' => $filterAction
                    ]);
                ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
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

    <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle color theme" title="Toggle theme">
        <span class="icon-sun" aria-hidden="true"><i class="fa-solid fa-sun"></i></span>
        <span class="icon-moon" aria-hidden="true"><i class="fa-solid fa-moon"></i></span>
        <span class="label">Theme</span>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme-toggle.js"></script>
    <script src="assets/js/ui-enhance.js"></script>
</body>
</html>
<?php mysqli_stmt_close($stmt); ob_end_flush(); ?>