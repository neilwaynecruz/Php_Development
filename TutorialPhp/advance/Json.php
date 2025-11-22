<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Json</title>
</head>
<body>
    <?php
        // json_encode() - this function is used to convert PHP array or object into JSON string
        // json_decode() - this function is used to convert JSON string into PHP array or object
    
        $names = [1 => "Neil Wayne", 2 => "Junella Mae", 3 => "Charlie Rola", 4 => "Charles Rola"];
        $fruits = ["Apple", "Banana", "Grapes", "Orange", "Kiwi", "Dragon Fruit"];

        echo json_encode($names) . "<br>";
        echo json_encode($fruits);

        $obj1 = '{"Wayne":20,"Junella":20,"Charlie":22}'; // json string

        echo  "<br>";
        var_dump(json_decode($obj1)); // this will convert to object
        echo "<br>"; 
        var_dump(json_decode($obj1,true)); // this will convert to associative array
        echo "<br>";

        $obj_decode = json_decode($obj1);
        echo $obj_decode->Wayne; // accessing object property
        echo "<br>";
        echo $obj_decode->Junella;

        echo "<br>";
        echo "<br>";
        $obj_decode_array = json_decode($obj1, true);
        echo $obj_decode_array['Charlie']; // accessing array element
        echo "<br>";
        echo $obj_decode_array['Wayne'];
        echo "<br>";
        echo "<br>";
        echo "<br>";

        $students = '{
            "students": [
                {
                    "name": "Neil Wayne",
                    "age": 20,
                    "course": "BSIT"
                },
                {
                    "name": "Junella Mae",
                    "age": 20,
                    "course": "BSIT"
                },
                {
                    "name": "Charlie Rola",
                    "age": 22,
                    "course": "BSCS"
                }
            ]
        }';

        $students_decode = json_decode($students);
        foreach($students_decode as $key => $value){
            echo $key . ": " . $value . "<br>";
        }


    ?>
</body>
</html>