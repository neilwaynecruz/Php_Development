<?php
session_start();

$error = '';

if (isset($_POST['submit'])) {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');

    if (strcmp($username, 'Admin') === 0 && strcmp($password, 'Admin') === 0) {
        $_SESSION['user'] = $username;
        header('Location: admin.php');
        exit();
    } else if (strcmp($username, 'Encoder') === 0 && strcmp($password, 'Encoder') === 0) {
        $_SESSION['user'] = $username;
        header('Location: encoder.php');
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PUP Portal - Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2 style="margin:0;">Log in</h2>
    <div class="accent"></div>

    <form action="login.php" method="POST">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" placeholder="Ex: Admin" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="text" placeholder="Ex: 123" required>

      <button type="submit" name="submit" value="1">Login</button>
      <p class="small">Tip: try Admin/Admin or Encoder/Encoder</p>

      <?php if ($error): ?>
        <div class="alert"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
    
    </form>
  </div>
</body>
</html>