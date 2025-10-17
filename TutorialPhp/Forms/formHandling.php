<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Handling</title>
</head>
<body>
    
</body>

<?php
    function varArr(... $arr) {
        foreach( $arr as $k) {
            echo $k + 1,"", $k,"";
        }
        echo count($arr);
    }
    
    varArr(1,2,3,4,5);
?>
</html>

