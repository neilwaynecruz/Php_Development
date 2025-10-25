<?php
session_start();
if (!isset($_SESSION['user']) || strcmp($_SESSION['user'], 'Encoder') !== 0) {
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
  <title>Encoder Dashboard - PUP Portal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="topbar">Encoder Dashboard</div>

  <div class="content">
    <h2 style="margin:0;">Encoder Dashboard</h2>
    <div class="accent"></div>
    <p>Hello, <strong><?php echo htmlspecialchars($user); ?></strong> (Encoder)</p>

    <h3>Encoder Menu</h3>
    <ul>
      <li>View Profile</li>
      <li>Edit Account</li>
      <li>Notifications</li>
      <li>Encode New Data</li>
      <li>View Submission History</li>
      <li>Search and Filter Records</li>
      <li>Send Feedback to Admin</li>
      <li>Check Daily Encoding Tasks</li>
      <li>View Encoding Performance</li>
      <li>Settings</li>
    </ul>


    <p><button><a class="btn" href="logout.php">Logout</button></a></p>
</body>
</html>
