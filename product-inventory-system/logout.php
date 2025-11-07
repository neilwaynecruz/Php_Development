<?php
session_start();
// log logout
if (isset($_SESSION['user'])) {
    $connection = mysqli_connect("localhost", "root", "", "inventory_db");
    if ($connection) {
        $u = mysqli_real_escape_string($connection, $_SESSION['user']);
        mysqli_query($connection, "INSERT INTO activity_logs (username, action, product_id, details) VALUES ('$u', 'logout', NULL, NULL)");
    }
}
session_unset();
session_destroy();
header("Location: login.php");
exit();