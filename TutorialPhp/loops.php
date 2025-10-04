<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loops</title>
</head>
<body>
    <?php
        // while loop - loops through a block of code as long as the condition is true

        // $a = 0;
        // while($a < 10){
            // if($a == 1) continue;
        //     if($a === 5) break;

        //     $a++;
        //     echo "The value of a is: {$a} <br>";
        // }
        $b = 0;
        while($b < 100):
            $b += 10;
            echo "The value of b is: {$b} <br>";

        endwhile;

        echo "<br>";
        // do while loop - executes at least once
        $c = 0;
        do{
            
            $c += 10;
            echo "The value of c is: {$c} <br>";
            if ($c === 60) break;
        }while($c < 100);

        echo "<br>";

        // for loop - loops through a block of code a specified number of times
        for($d = 0; $c < 100; $c += 10){
            if($d === 50) continue;
            echo "The value of c is: {$c} <br>";
        }

        echo "<br>";
        for($e = 0; $e < 100; $e += 10):
            if($e === 50) continue;
            echo "The value of e is: {$e} <br>";
        endfor;
        
    ?>
</body>
</html>