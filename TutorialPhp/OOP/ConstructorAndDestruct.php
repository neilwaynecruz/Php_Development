<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constructor</title>
</head>
<body>

    <?php
    //__destruct() function that is automatically called at the end of the script:
    
         class Fruits2{
            private $name;
            private $color;
        

            function __construct($name,$color)
            {
                $this->name = $name;
                $this->color = $color;
            }


            function setName($name){
                $this->name = $name;
            }

            function getName(){
                return $this->name;
            }

            function setCOlor($color){
                $this->color = $color;
            }

            function getColor(){
                return $this->color;
            }

            function __destruct()
            {
                echo  "<br>". "The fruits is {$this->name} with a color of {$this->color}";
            }

        }


        $mango = new Fruits2("Mango","Green Yellow");

        echo $mango->getName() . "<br>";
        echo $mango->getColor();
    
    
    ?>
    



</body>
    </html>