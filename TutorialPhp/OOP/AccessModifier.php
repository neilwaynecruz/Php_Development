<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Modifier</title>
</head>
<body>
    
    <?php
    
    class Phones{
        public $Brand;
        private $model;
        protected $os;
    }


    $apple = new Phones();
    $apple->Brand = "Apple";
    // $apple->model = "Iphone 17 Pro Max";
    // $apple->os = "IOS";

    ?>
</body>
</html>