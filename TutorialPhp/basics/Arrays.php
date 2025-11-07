<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arrays</title>
</head>
<body>
    <?php 
    
    // Indexed Array
    $fruits = array("Apple", "Banana", "Orange");
    $fruits[3] = "Grapes"; // adding an element
    $fruits[] = "Mango"; // adding an element at the end
    $fruits[1] = "Pineapple"; // modifying an element
    unset($fruits[2]); // removing an element
     
    echo "<h2>Indexed Array</h2>";
    foreach ($fruits as $fruit) {
        echo $fruit . "<br>";
    }

    echo "<br>";

    // Associative Array
    $ages = array("Alice" => 25, "Bob" => 30, "Charlie" => 35);
    $ages["David"] = 28; // adding an element
    $ages["Alice"] = 26; // modifying an element
    unset($ages["Bob"]); // removing an element
    
    foreach($ages as $key => $value) {
        echo "$key is $value years old.<br>";
    }

    //creating a array
    // using array() function
    $person = array(
        "name" => "John",
        "age" => 30,
        "city" => "New York"
    );

    // or 
    // using short array syntax []
    $person = [
        "name" => "John",
        "age" => 30,
        "city" => "New York"
    ];

    echo "<br>";
    echo $person["name"] . " is " . $person["age"] . " years old and lives in " . $person["city"] . ".<br><br>";

    $nums = ["1" => "one","two","three"];
    foreach($nums as $key => $value) {
        echo "Key: $key; Value: $value<br>";
    }

    //Excecute a Function Item
    function greet($num = '') {
        echo "Hello, World! " . $num;
    }

    $functions = array("strtolower", "strtoupper", "greet");

    echo "<br><br>";
    $functions[2](12);
    echo "<br><br>";

    function sum($a, $b) {
        return $a + $b;
    }

    $function2 = array("strtolower", "strtoupper", "strrev","strlen", "sum");

    foreach ($function2 as $func) {
        if ($func === "sum") {
            echo "Sum of 5 and 10 is: " . $func(5, 10) . "<br>";
        } else {
            echo $func("Hello") . "<br>";
        }
    }

    // sort

    // sort - sort in ascending order
    // rsort - sort in descending order
    // asort - sort associative arrays in ascending order, according to the value
    // ksort - sort associative arrays in ascending order, according to the key
    // arsort - sort associative arrays in descending order, according to the value
    // krsort - sort associative arrays in descending order, according to the key
    $foodsbox = array("Banana", "Apple", "Orange", "Mango", "kiwi", "Pineapple", "Grapes");

    sort($foodsbox); // Sort in ascending order
    echo "<br>Sorted in ascending order:<br>";
    foreach ($foodsbox as $food) {
        echo $food . "<br>";
    }

    // multidimensional array
    $cars = array(
        array("Car Model" => "Volvo", "Stock" => 22, "Sold" => 18),
        array("Car Model" => "BMW", "Stock" => 15, "Sold" => 13),
        array("Car Model" => "Saab", "Stock" => 5, "Sold" => 2),
        array("Car Model" => "Land Rover", "Stock" => 17, "Sold" => 15)
    );

    foreach ($cars as $car) {
        echo "<br>";
        foreach ($car as $model => $detail) {
            echo $model . ": " . $detail . "; ";
        }
    }


    // or 

    // for ($row = 0; $row < count($cars); $row++){
    //     echo "<br>";
    //     foreach ($cars[$row] as $model => $detail) {
    //         echo $model . ": " . $detail . "; ";
    //     }
    // }

    
    ?>
    
</body>
</html>