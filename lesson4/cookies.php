<!--  The setcookie() function must appear BEFORE the <html> tag. -->

<?php
    $cookie_name = "user";
    $cookie_value = "Wayne";
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

    // set the expiration date to one hour ago
    // setcookie("user", "", time() - 3600);

    // setrawcookie();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php
    // if(!isset($_COOKIE[$cookie_name])) {
    // echo "Cookie named '" . $cookie_name . "' is not set!";
    // } else {
    // echo "Cookie '" . $cookie_name . "' is set!<br>";
    echo "Value is: " . $_COOKIE[$cookie_name];
    // }

    // echo "Cookie 'user' is deleted.";
?>
    
</body>
</html>