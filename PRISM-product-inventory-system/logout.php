<?php
session_start();

// Kung may naka-login na user, log natin yung logout action sa database
if (isset($_SESSION['user'])) {
    // Connect sa database
    $connection = mysqli_connect("localhost", "root", "", "inventory_db");
    if ($connection) {
        // Sanitize username bago i-insert
        $u = mysqli_real_escape_string($connection, $_SESSION['user']);
        // Insert sa activity_logs table na action = logout
        mysqli_query($connection, "INSERT INTO activity_logs (username, action, product_id, details) VALUES ('$u', 'logout', NULL, NULL)");
    }
}

// Tapos na, i-clear ang session
session_unset();
session_destroy();

// Redirect sa login page
header("Location: login.php");
exit();
?>
