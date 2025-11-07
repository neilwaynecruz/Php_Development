<?php
session_start();
if (!isset($_SESSION['user']) || strcmp($_SESSION['user'], 'Admin') !== 0) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - PUP Portal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="topbar">Admin Dashboard</div>

  <div class="content">
    <h2 style="margin:0;">Admin Dashboard</h2>
    <div class="accent"></div>
    <p>Hello, <strong><?php echo htmlspecialchars($user); ?></strong> (Admin)</p>

    <h3>Admin Tools</h3>
    <ul>
      <li>Manage Users</li>
      <li>View Reports</li>
      <li>System Settings</li>
      <li>Manage Encoder Submissions</li>
      <li>View Feedback from Encoders</li>
      <li>Manage Permissions and Roles</li>
      <li>Backup and Restore Data</li>
      <li>Send Announcements</li>
      <li>View Activity Logs</li>
      <li>Generate Analytics Summary</li>
    </ul>

    <p><button><a class="btn" href="logout.php">Logout</a></button></p>
  </div>
</body>
</html>
