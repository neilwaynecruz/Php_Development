<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math</title>
</head>
<body>

    <?php
    echo("pi: " . pi() . "<br>"); // 3.1415926535898
    echo("max: " . max(1090,32,444,21,234) . "<br>"); // 1090
    echo("min: " . min(12,2,5,7,8) . "<br>"); // 2
    echo("abs: " . abs(-6.7) . "<br>"); // 6.7
    echo("sqrt: " . sqrt(64) . "<br>"); // 8
    echo("round: " . round(0.64234) . "<br>"); // 1
    echo("rand: " . rand() . "<br>"); // random number
    echo("rand (10-100): " . rand(10,100) . "<br>"); // random number between 10 and 100. both inclusive
    ?>
</body>
</html>