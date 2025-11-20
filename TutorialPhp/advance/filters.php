<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    

    <?php
    //Sanitize String

    $str = "<b>Neil Wayne Cruz</b>";
    $str = filter_var($str, FILTER_SANITIZE_STRING);
    print_r($str);

    echo "<br><br>";

    $num1 = 0;

    if(filter_var($num1,FILTER_VALIDATE_INT) === 0 || !empty(filter_var($num1,FILTER_VALIDATE_INT))){
        echo("$num1 is valid");
    } else {
    echo("$num1 is invalid Integer");
    }
    echo "<br><br>";

    $ipv4 = "127.0.0.1";

    if(!empty(filter_var($ipv4,FILTER_VALIDATE_IP))):
        echo "$ipv4 is a valid IP address";
    else:
        echo "$ipv4 is invalid IP address";
    endif;

    echo "<br><br>";

    $email = "john.>>>>>>>doe@example.com";
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    if(!empty(filter_var($email,FILTER_VALIDATE_EMAIL))){
        echo "$email is valid email address";
    }else{
        echo "$email is not valid email address";
    }

    echo "<br><br>";

    $url = "https://www.w3schools.com";
    $url = filter_var($url,FILTER_SANITIZE_URL);

    if (!empty(filter_var($url, FILTER_VALIDATE_URL))) {
        echo("$url is a valid URL");
    } else {
        echo("$url is not a valid URL");
    }


    ?>
</body>
</html>