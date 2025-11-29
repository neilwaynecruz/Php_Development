<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consts and Abstract Classes</title>
</head>
<body>
    <?php

    //Class constants can be useful if you need to define some constant data within a class. A class constant is declared inside a class with the const keyword. constant cannot be changed once it is declared.

    //We can access a constant from outside the class by using the class name followed by the scope resolution operator (::) followed by the constant name, like here:
    class Animals{
       public const ENDING_MESSAGE = "BE GOOD TO ALL ANIMALS!!";

       public function byeMessage(){
        return self::ENDING_MESSAGE;
       }

    }

    $animal = new Animals();
    echo Animals::ENDING_MESSAGE . "<br>"; 
    echo $animal->byeMessage();
    

    ?>
</body>
</html>