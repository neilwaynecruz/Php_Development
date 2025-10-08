<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constant</title>
</head>
<body>
    <?php
    
    define("GREETING", "Welcome to W3Schools.com!");
    echo GREETING . "<br>";

    // another way to define constant usding "const" keyword
    const MY_NAME = "Neil Wayne Cruz";
    echo MY_NAME . "<br>";

    //magic constant
    
    echo "Line number: " . __LINE__ . "<br>";
    echo __CLASS__ ."<br>";
    echo "File name: " . __FILE__ . "<br>";  
    echo "function name: " . __FUNCTION__ . "<br>";
    


    ?>
    
</body>
</html>