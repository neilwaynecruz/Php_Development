<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inheritance</title>
</head>
<body>
    <?php

    class Phones2{
        public $brand;
        public $model;
        public $os;
        
        function __construct($brand, $model, $os){
            $this->brand = $brand;
            $this->model = $model;
            $this->os = $os;
        }

        function intro(){
            echo "<h3>The phone brand:{$this->brand}<br>model: {$this->model}<br>OS:{$this->os}</h3>";
        }

        protected function broken(){ // can be accsses within the class or the class that inheriting this class Phones2
            echo "<p>{$this->model} is broken</p>";
        }
    }

    class Samsung extends Phones2{
        public function message(){
            echo "<h1>This is samsung</h1>";
        }

        function brokenMessage(){
            $this->broken();
        }

        function __destruct()
        {
            echo "<h1>END OF CODE</h1>";
        }
    }

    class Iphone extends Phones2{
        private $color;
        function __construct($brand, $model, $os,$color)
        {
            $this->brand = $brand;
            $this->model = $model;
            $this->os = $os;
            $this->color=$color;
        }

        function intro(){
            echo "<h3>The phone brand:{$this->brand}<br>model: {$this->model}<br>OS:{$this->os}<br>Color:{$this->color}</h3>";
        }
        
    }

    final class Animals{

    }

    // class Predator extends Animals{ // this is produce warning since Parant class (Animals) is declared final class

    // }

    

    $samsung = new Samsung("Samsung","Galaxy S25+","Android");
    $samsung->message();
    $samsung->intro();
    $samsung->brokenMessage();

    $iphone = new Iphone("Iphone","Iphone 17 Pro Max","IOS", "Navy Blue");
    $iphone->intro();




    ?>
</body>
</html>