<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Function</title>
</head>
<body>
    <?php
    // function has two types:
    // 1. User-defined functions
    // 2. Built-in functions

        // 1. USER-DEFINED FUNCTIONS
        function addNumbers($a, $b){
            $sum = $a + $b;
            return $sum;
        }

        $result = addNumbers(10, 20);
        echo "The sum is: {$result} <br>";

        echo "<br>";

        function multiplyNumbers($x, $y){   
            $product = $x * $y;
            return $product;
        }

        $result2 = multiplyNumbers(5, 4);
        echo "The product is: {$result2} <br>";

        echo "<br>";

        // 2. BUILT-IN FUNCTIONS
        $str = "Hello, welcome to PHP functions!";
        $length = strlen($str);
        echo "The length of the string is: {$length} <br>";

        echo "<br>";

        $number = 7.8;
        $rounded = round($number);
        echo "The rounded number is: {$rounded} <br>";
    ?>
</body>
</html>