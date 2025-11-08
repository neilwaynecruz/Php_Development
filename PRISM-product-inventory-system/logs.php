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

$role = $_SESSION['role'] ?? 'user';
$isAdmin = ($role === 'admin');
if (!$isAdmin) {
    $_SESSION["message"] = '<div class="alert alert-danger">Access denied: Admins only.</div>';
    header("Location: products.php");
    exit();
}

function sanitize($s) { return htmlspecialchars(trim($s)); }

function actionBadgeClass($action) {
    $a = strtolower(trim((string)$action));
    return match($a) {
        'create'  => 'badge-action badge-create',
        'update'  => 'badge-action badge-update',
        'delete'  => 'badge-action badge-delete',
        'login'   => 'badge-action badge-login',
        'logout'  => 'badge-action badge-logout',
        'archive' => 'badge-action badge-archive',
        'restore' => 'badge-action badge-restore',
        default   => 'badge-action badge-generic',
    };
}

/* Pagination + Filters */
$records_per_page = 23;
$current_page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
    $filterUser   = sanitize($_POST['user'] ?? '');
    $filterAction = sanitize($_POST['action'] ?? '');
} else {
    $filterUser   = sanitize($_GET['user'] ?? '');
    $filterAction = sanitize($_GET['action'] ?? '');
}

$where  = "1=1";
$params = [];
$types  = '';

if ($filterUser !== '') {
    $where .= " AND username LIKE ?";
    $types .= 's';
    $params[] = "%{$filterUser}%";
}
if ($filterAction !== '') {
    $where .= " AND action = ?";
    $types .= 's';
    $params[] = $filterAction;
}

/* Count */
$total_sql = "SELECT COUNT(*) AS total FROM activity_logs WHERE $where";
$stmt_total = mysqli_prepare($connection, $total_sql);
if ($stmt_total) {
    if ($types !== '') mysqli_stmt_bind_param($stmt_total, $types, ...$params);
    mysqli_stmt_execute($stmt_total);
    $total_result = mysqli_stmt_get_result($stmt_total);
    $total_records = (int)mysqli_fetch_assoc($total_result)['total'];
    mysqli_stmt_close($stmt_total);
} else {
    $total_records = 0;
}

$total_pages = max(1, (int)ceil($total_records / $records_per_page));
if ($current_page > $total_pages) $current_page = $total_pages;
$offset = ($current_page - 1) * $records_per_page;

/* Fetch */
$list_sql = "SELECT * FROM activity_logs WHERE $where ORDER BY log_id DESC LIMIT ? OFFSET ?";
$types_l  = $types . 'ii';
$params_l = array_merge($params, [$records_per_page, $offset]);

$stmt = mysqli_prepare($connection, $list_sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $types_l, ...$params_l);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
} else {
    $rs = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Activity Logs | Product Inventory System</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="assets/css/theme.css?v=5" rel="stylesheet">
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
        <a href="users.php" class="btn btn-outline-light btn-sm me-2">Users</a>
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

    <div class="card shadow-sm card-lift mb-3 logs-card">
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
              <?php
                $actions = ['login','logout','create','update','delete','archive','restore'];
                foreach ($actions as $act) {
                  $sel = ($filterAction === $act) ? 'selected' : '';
                  echo "<option value=\"$act\" $sel>$act</option>";
                }
              ?>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" name="filter" class="btn btn-secondary w-100">Apply</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm card-lift logs-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title mb-0">Recent Activity</h5>
          <small class="text-muted">
            Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>
            (<?php echo number_format($total_records); ?> total)
          </small>
        </div>

        <div class="table-responsive">
          <table class="table-logs table table-hover align-middle">
            <thead>
              <tr>
                <th style="width:6%;">ID</th>
                <th style="width:18%;">User</th>
                <th style="width:12%;">Action</th>
                <th style="width:10%;">Product ID</th>
                <th style="width:36%;">Details</th>
                <th style="width:18%;">When</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if ($rs && mysqli_num_rows($rs) > 0) {
                    while ($row = mysqli_fetch_assoc($rs)) {
                        $rawAction   = strtolower(trim($row['action'] ?? ''));
                        $badgeClass  = actionBadgeClass($rawAction);
                        $actionLabel = htmlspecialchars(ucfirst($rawAction));
                        echo '<tr>';
                        echo '<td>' . (int)$row['log_id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                        echo '<td><span class="' . $badgeClass . '">' . $actionLabel . '</span></td>';
                        echo '<td class="text-center">' . ($row['product_id'] !== null ? (int)$row['product_id'] : '-') . '</td>';
                        echo '<td class="details" title="' . htmlspecialchars($row['details']) . '">' . nl2br(htmlspecialchars($row['details'])) . '</td>';
                        echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center text-muted">No logs found.</td></tr>';
                }
              ?>
            </tbody>
          </table>
        </div>

        <?php if ($total_pages > 1):
            $filter_query_string = http_build_query(['user' => $filterUser, 'action' => $filterAction]);
        ?>
        <nav class="logs-pagination mt-4" aria-label="Page navigation">
          <ul class="pagination pagination-theme justify-content-center">
            <li class="page-item <?php if($current_page <= 1) echo 'disabled'; ?>">
              <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&<?php echo $filter_query_string; ?>" tabindex="<?php echo $current_page <= 1 ? '-1':''; ?>">Previous</a>
            </li>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?php if($current_page == $i) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo $filter_query_string; ?>" aria-current="<?php echo $current_page==$i?'page':''; ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?php if($current_page >= $total_pages) echo 'disabled'; ?>">
              <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&<?php echo $filter_query_string; ?>" tabindex="<?php echo $current_page >= $total_pages ? '-1':''; ?>">Next</a>
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
<?php
if ($stmt) mysqli_stmt_close($stmt);
ob_end_flush();
?>