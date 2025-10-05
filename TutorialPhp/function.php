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

        echo "<H1>User-defined Functions</H1>";
        // 1. USER-DEFINED FUNCTIONS
        function addNumbers($a, $b):mixed{
            $sum = $a + $b;
            return $sum;
        }

        $result = addNumbers(10, 20);
        echo "The sum is: {$result} <br>";

        function multiplyNumbers($x, $y):mixed {   
            $product = $x * $y;
            return $product;
        }
        $result2 = multiplyNumbers(5, 4);
        echo "The product is: {$result2} <br>";

        // with default parameters
        function taxCalculation($amount, $taxRate = 0.05):mixed {
            $tax = $amount * $taxRate;
            return $tax;
        }
        $result3 = taxCalculation(1000);

        // reference parameters
        function increment(&$value):void {
            $value++;
        }
        $num = 5;
        increment($num);
        echo "The incremented value is: {$num} <br>";

        // variable length arguments
        function sumMyNumbers(...$numbers):mixed {
            $total = 0;
            foreach ($numbers as $number) {
                $total += $number;
            }
            return $total;
        }
        $sum = sumMyNumbers(1, 2, 3, 4, 5);
        echo "The total sum is: {$sum} <br>";

        echo "<br>";

        // 2. BUILT-IN FUNCTIONS
        echo "<H1>Built-in Functions</H1>";
        $str = "Hello, welcome to PHP functions!";
        $length = strlen($str);
        echo "The length of the string is: {$length} <br>";

        $number = 7.8;
        $rounded = round($number);
        echo "The rounded number is: {$rounded} <br>";
    ?>
</body>
</html>