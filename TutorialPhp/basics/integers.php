<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integers</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; text-align: justify;">


    <?php
    $x = 14;
    $y = 14.14;
    $z = "14";

    echo var_dump($x) . "<br>"; // data type and value
    echo var_dump($y) . "<br>"; // data type and value
    echo var_dump($z) . "<br><br>"; // data type and value

    echo "<b>INTEGERS:</b><br>";
    echo var_dump($x) . "<br>";
    echo var_dump(is_int($x)) . "<br>";
    echo var_dump(is_int($y)) . "<br>";

    echo  "MAX INT: " . PHP_INT_MAX . "<br>";
    echo  "MIN INT: " . PHP_INT_MIN . "<br>";
    echo "INT SIZE: " . PHP_INT_SIZE . "<br><br>";

    //floats
    echo "<b>FLOATS:</b><br>";
    echo var_dump($y) . "<br>";
    echo var_dump(is_float($x)) . "<br>";
    echo var_dump(is_float($y)) . "<br>";

    echo "MAX FLOAT: ". PHP_FLOAT_MAX . "<br>";
    echo "MIN FLOAT: ". PHP_FLOAT_MIN . "<br>";
    echo "FLOAT DIG: ". PHP_FLOAT_DIG . "<br>";
    echo "FLOAT EPSILON: ". PHP_FLOAT_EPSILON . "<br><br>";

    //Infinity
    $a = 1.9e411; //value greater than PHP_FLOAT_MAX
    echo "<b>INFINITY:</b><br>";
    echo var_dump($a) . "<br>";
    echo var_dump(is_infinite($a)) . "<br>";
    echo var_dump(is_finite($a)) . "<br><br>";

    //Nan - Not a number
    $b = acos(8); //acos() value must be in the range -1 to 1
    echo "<b>Nan:</b><br>";
    echo var_dump($b) . "<br>";
    echo var_dump(is_nan($b)) . "<br><br>";


    //Numerical Strings
    echo "<b>Numerical Stirng:</b><br>";
    $c = 5985;
    echo var_dump(is_numeric($c)) . "<br>";
    $c = "5985";
    echo var_dump(is_numeric($c)) . "<br>";
    $c = "59.85" + 100;
    echo var_dump(is_numeric($c)) . "<br>";
    $c = "Hello";
    echo var_dump(is_numeric($c)) . "<br><br>";

    //Casting
    echo "<b>Casting:</b><br>";
    $d = 1234.567;
    $d = (int)$d;
    echo var_dump($d) . "<br>";

    $e = "1234.567";
    $e = (float)$e;
    echo var_dump($e) . "<br>";

    ?>
    
</body>
</html>