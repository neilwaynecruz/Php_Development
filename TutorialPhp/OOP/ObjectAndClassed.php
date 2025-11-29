<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <?php

use function PHPUnit\Framework\isInstanceOf;

        class Fruits{
            private $name;
            private $color;


            function set_name($name){
                $this->name = $name;
            }

             function get_name(){
                return $this->name;
            }
            function set_color($color){
                $this->color = $color;
            }

             function get_color(){
                return $this->color;
            }
        }


        $mango = new Fruits();
        $apple = new Fruits();
        $grapes = new Fruits();
        
        
        $mango->set_name("Mango");
        $apple->set_name("Apple");
        $grapes->set_name("Grapes");
        $mango->set_color("Yellow Green");
        $apple->set_color("Red");
        $grapes->set_color("Violet");
    

        echo $mango->get_name() . "<br>";
        echo $apple->get_name() . "<br>";
        echo $grapes->get_name() . "<br><br>";
        echo $mango->get_color() . "<br>";
        echo $apple->get_color() . "<br>";
        echo $grapes->get_color() . "<br><br>";
        var_dump($mango instanceof Fruits);
    ?>
</body>
</html>