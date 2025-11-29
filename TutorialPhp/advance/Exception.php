<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exception</title>
</head>
<body>
    
    <?php
        function division($dividend, $divisor){
            if($divisor === 0):
                throw new Exception("Division by zero (0)");
            else:
                return $dividend/$divisor;
            endif;
        }


        try{
            echo division(5,0);
        }catch(Exception $e){
            echo "Exception thrown in " . $e->getFile(), " on line " . $e->getLine() . ": [Code " . $e->getCode() . "] " . $e->getMessage();
        }finally{
            echo "<br><br>" . "The end";
        }
        

    ?>
</body>
</html>