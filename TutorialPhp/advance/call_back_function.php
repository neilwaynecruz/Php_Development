<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CallBack Function</title>
</head>
<body>
    
    
    <?php
    //Callback Functions

use Brick\Math\Exception\DivisionByZeroException;

        function my_callback($item) {
        return strlen($item);
        }

        $strings = ["apple", "orange", "banana", "coconut"];
        $lengths = array_map("my_callback", $strings);
        print_r($lengths);
        echo "<br><br>";
    ?>

    <?php
    //Callback Functions
        $lengths = array_map(
        function ($item){
            return strlen($item);
        }, 
        $strings);
        print_r($lengths);
    ?>

    <?php
    // Callback  User Define Function

    function addition(...$numbers): mixed{
        $sum = 0;
        foreach($numbers as $number){
            $sum += $number;
        }
        return $sum;
    }

    function subtraction(...$numbers): mixed{
        if(count($numbers) === 0) return 0;
        $difference = array_shift($numbers); // Start from first number
        foreach($numbers as $number){
            $difference -= $number;
        }
        return $difference;
    }

    function multiplication(...$numbers): mixed{
        if(count($numbers) === 0) return 0;
        $product = 1;
        foreach($numbers as $number){
            $product *= $number;
        }
        return $product;
    }

    function division(...$numbers): mixed{
        if(count($numbers) === 0) return 0;

        $quotient = array_shift($numbers); // Start from first number

        foreach ($numbers as $number) {
            if ($number === 0) {
                throw new DivisionByZeroError("Cannot divide by zero: one of the arguments is 0");
            }
            $quotient /= $number;
        }

        return $quotient;
    }

     echo "<br><br>";

    function printOutput($format, array $numbers){
        echo $format(...$numbers) . "<br>";
    }

    printOutput(
        format: "addition",
        numbers: [1,2,3,4,5],
    );

    printOutput(
        format: "subtraction",
        numbers: [1,2,3,4,5],
    );

    // printOutput(
    //     "division",
    //     [3,2,5,0],
    // );

    try{
        printOutput(
        "division",
        [300,2,5,3],
    );
    } catch(DivisionByZeroError $e){
        echo "Error: " . $e->getMessage();
    }
    ?>
</body>
</html>