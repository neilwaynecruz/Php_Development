<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
</head>
<body>
    <form action="Login.php" method="POST">
        <label for="username" >Username</label><br>
        <input type="text" name="username" id="username"><br><br>
        <label for="password">Password</label><br>
        <input type="text" name="password" id="password"><br><br>
        <input type="submit" name="login" value="Login"> <br><br>
    </form>
</body>
</html>

<?php
    if(isset($_POST["login"])){
        $username = $_POST["username"];
        $password = $_POST["password"];

        if(empty($username) && empty($password)){
            echo "Both Username and Password are missing";
        } else if(empty($password)){
             echo "Password is missing";
        } else if(empty($username)){
              echo "Username is missing";
        } else{
            echo "Welcome, {$username}";
        }
    }else{
        echo "Please input a username and password";
    }
?>