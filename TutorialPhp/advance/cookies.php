 <?php

        // cookie - used to identify a user and store user preferences on their computer. 
        // A cookie is a small file that the server embeds on the user's computer.
        // setcookie(name, value, expire, path, domain, secure, httponly)
        // to remove a cookie, set the expiration date to one hour ago: time() - 3600 or time() - 0

        setcookie("user", "John Doe", time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie("password", "john123", time() + (86400 * 30), "/"); // 86400 = 1 day

        if(!isset($_COOKIE["user"])) {
            echo "Cookie named 'user' is not set!<br>";
        } else {
            echo "Cookie 'user' is set!<br>";
            echo "Value: " . $_COOKIE["user"] . "<br><br>";
        }

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie</title>
</head>
<body>
</body>
</html>