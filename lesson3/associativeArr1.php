<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Associative array</title>
</head>
<body>
    <h3>Mirienda</h3>

    <?php
    $x = array("Spaghetti" => "75.00", "Palabok" => "85.00", "Pansit Bihon" => "70.00");
    foreach($x as $i => $y)
        echo "<b>{$i} {$y}</b><br>";

    echo "<br><b>New set of merienda:</b><br>";
    $x["Baked Macaroni"] = "100.00";

    foreach($x as $i => $y)
        echo "<b>{$i} {$y}</b><br>";
    
    echo "<br><br><br>";

    $fruits = array("apple","bananas","oranges");
    $meats = array("steaks","hamburger","pork chops");

    $groceries = array("Fruits" => $fruits, "Meats" => $meats);

    foreach($groceries as $fruits => $v){
        echo "<b>{$fruits}:</b><br>";
        foreach($meats as $i)
            echo "{$i}<br>";
        echo "<br>";
    }

    ?>
</body>
</html>