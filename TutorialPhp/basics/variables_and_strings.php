<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variables and strings</title>
</head>
<body>
    
<?php 
    $num1 = 15;
    $num2 = 20;
    $str1 = "Hello World";
    $str2 = "I LOVEE YOUU JUNELLA MAE ANDRES";


    echo "the value of num1 is $num1<br>" ; // interpolation
    echo 'the value of num1 is '.$num1.'<br>' ; // concatenation
    echo "Value of str1 is $str1<br>";
    echo "The value of str2 is $str2<br>";
    echo var_dump($num1) . "<br>"; // data type and value
    echo str_word_count($str1) . "<br>"; // number of words
    echo strlen($str1) . "<br>"; // length of string
    echo strpos($str1, "World") . "<br>"; // position of word
    echo strtoupper($str1) . "<br>"; // to uppercase
    echo strtolower($str2) . "<br>";// to lowercase
    echo str_replace("World", "Everyone", $str1) . "<br>"; // replace word
    echo strrev($str2) . "<br>"; // reverse string
    echo trim("   HELLO   POO   ") . "<br>"; // remove whitespace from beginning and end
    $y = explode(" ", $str2);
    //Use the print_r() function to display the result:
    print_r($y);

    //slicing
    
    echo "<br><br>". substr($str2, 2, 5); // starting at index 2, return 5 characters
?>


<!-- <p>The value of num1 is <?=$num1?></p> -->

</body>
</html>