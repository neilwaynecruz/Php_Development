<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditional Statements</title>
</head>
<body>
    <?php
    $t = "Monday";

    if ($t == "Monday") {
        echo "Have a nice week!";
    } elseif ($t == "Friday") {
        echo "Have a nice weekend!";
    } elseif ($t == "Sunday") {
        echo "Have a nice Sunday!";
    } else {
        echo "Have a nice day!";
    }

    $age = 19;
    

    if( $age >= 18 && $age <= 65 ) {
        echo "<br>You are eligible to work.";
    } else {
        echo "<br>You are not eligible to work.";
    }

    // shorthand if...else (ternary operator)
    if ($age >= 18 && $age <= 65) echo  "<br>". ++$age;

    $status = $age >= 18 ? "Adult" : "Minor";
    echo "<br>" . $status;
    

    if($age >= 18 and $age <=65){
        if( $status == "Adult"){
            echo "<br>Hello. Mr/Ms.!";
        }
        else{
            echo "<br>How did you get here?";
        }
    } else {
        echo "<br>Hello Kiddo!";
    }


    // switch statement
    $favColor = "red";
    switch ($favColor){
        case strtolower("red"):
            echo "<br>Your favorite color is red!";
            break;
        case "blue":
            echo "<br>Your favorite color is blue!";
            break;
        case "green":
            echo "<br>Your favorite color is green!";
            break;
        default:
            echo "<br>Your favorite color is neither red, blue, nor green!";
    }
    ?>
</body>
</html>