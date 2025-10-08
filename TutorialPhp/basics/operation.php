<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operations</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 20px;">
    
    <?php
    
    //arithmetic operators
    $x = 10;
    $y = 5;

    
    echo "X = {$x} and Y = {$y} <br><br>";
    echo "ARITHMETIC OPERATORS <br>";
    echo "Addition: " . ($x + $y) . "<br>";
    echo "Subtraction: " . ($x - $y) . "<br>";
    echo "Multiplication: " . ($x * $y) . "<br>";
    echo "Division: " . ($x / $y) . "<br>";
    echo "Modulus: " . ($x % $y) . "<br>";
    echo "exponentiation: " . ($x ** $y) . "<br><br>";

    $x = 10;
    $y = 5;
    echo "ASSIGNMENT OPERATOR <br>";
    echo "equal " . ($x = $y) . "<br>";
    echo "add and assign " . ($x += $y) . "<br>";
    echo "subtract and assign " . ($x -= $y) . "<br>";
    echo "multiply and assign " . ($x *= $y) . "<br>";
    echo "divide and assign " . ($x /= $y) . "<br>";
    echo "modulus and assign " . ($x %= $y) . "<br>";
    echo "exponentiation and assign " . ($x **= $y) . "<br><br>";

    $x = 10;
    $y = 5;
    //comparison operators
    echo "COMPARISON OPERATORS <br>";
    echo "equal (==)" . var_dump($x == $y) . "<br>";
    echo "identical (===)" . var_dump($x === $y) . "<br>";
    echo "not equal (!=) " . var_dump($x != $y) . "<br>";
    echo "not identical (!==) " . var_dump($x !== $y) . "<br>";
    echo "greater than (>)" . var_dump($x > $y) . "<br>";
    echo "less than (<)" . var_dump($x < $y) . "<br>";
    echo "greater than or equal to (>=)" . var_dump($x >= $y) . "<br>";
    echo "less than or equal to (<=)" . var_dump($x <= $y) . "<br><br>";
    echo "spaceship operator ( <=> ) " . var_dump($x <=> $y) . "<br><br>";
    
    $x = 10;
    $y = 5;
    //increment / decrement operators
    echo "INCREMENT / DECREMENT OPERATORS <br>";
    echo "X = {$x} <br>";
    echo "Y = {$y} <br>";
    echo "pre-increment X: " . (++$x) . "<br>";
    echo "post-increment Y: " . ($y++) . "<br>";
    echo "Y after post-increment: " . $y . "<br>";
    echo "pre-decrement X: " . (--$x) . "<br>";
    echo "post-decrement Y: " . ($y--) . "<br>";
    echo "Y after post-decrement: " . $y . "<br><br>";

    //logical operators
    echo "LOGICAL OPERATORS <br>";
    $a = true;
    $b = false;
    $c = false;
    echo "A = true <br>";
    echo "B = false <br>";
    echo "C = false <br>";
    echo "&& (A and B): " . var_dump($a and $b) . "<br>";
    echo "|| (A or B): " . var_dump($a or $b) . "<br>";
    echo "XOR (A xor B): " . var_dump($a xor $b) . "<br>";
    echo "! (not A): " . var_dump(!$a) . "<br>";
    echo "&& (B and C): " . var_dump($b and $c) . "<br>";
    echo "|| (B or C): " . var_dump($b or $c) . "<br>";
    echo "XOR (B xor C): " . var_dump($b xor $c) . "<br><br>";

    //String operators (concationation)
    $txt1 = "Hello ";
    $txt2 = "World";
    echo "STRING OPERATORS <br>";
    echo "concatenate and assignment: " . ($txt1 .= $txt2) . "<br><br>";

    // array operators
    $array1 = array("a" => "red", "b" => "green");
    $array2 = array("c" => "blue", "d" => "yellow");
    echo "ARRAY OPERATORS <br>";
    echo "Union (+): " . var_dump($array1 + $array2) . "<br>";
    echo "Equality (==): " . var_dump($array1 == $array2) . "<br>";
    echo "Identity (===): " . var_dump($array1 === $array2) . "<br>";
    echo "Inequality (!=): " . var_dump($array1 != $array2) . "<br>";
    echo "another form of inequality (<>): " . var_dump($array1 <> $array2) . "<br>";
    echo "Non-identity (!==): " . var_dump($array1 !== $array2) . "<br><br>";

    // conditional assignment operator (ternary operator)
    $age = 20;
    $is_Adult = ($age >= 18) ? true : false;
    echo "CONDITIONAL ASSIGNMENT OPERATOR (TERNARY OPERATOR) <br>";
    echo "Age = {$age} <br>";
    echo "is_Adult: " . var_dump($is_Adult) . "<br><br>";
    ?>
</body>
</html>