<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanitize and Validate input</title>
</head>
<body>

    <form action="sanitizeValidate.php" method="POST">
        <label for="name">name:</label> <br>
        <input type="text" name="name" required><br><br>

        <label for="age">age:</label> <br>
        <input type="text" name="age" required><br><br>

        <label for="email">email:</label> <br>
        <input type="text" name="email" required><br><br>

        <input type="submit" name="login" value="login"><br>
    </form>
<!-- <script>alert("Virus check");</script> -->

    <?php

        if(isset($_POST["login"])){
            // $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
            // $age = filter_input(INPUT_POST,"age", FILTER_SANITIZE_NUMBER_INT);
            // $email = filter_input(INPUT_POST,"email", FILTER_SANITIZE_EMAIL);
            // echo $name . '<br>';
            // echo $age . '<br>';
            // echo $email . '<br>';

            //validate
            
            $age = filter_input(INPUT_POST,"age", FILTER_VALIDATE_INT);
            $email = filter_input(INPUT_POST,"email", FILTER_VALIDATE_EMAIL);

            if(empty($age) || empty($email)){
                echo "Enter a valid age or email";
            }else{
                echo "You are {$age} year/s old and your email is {$email}";
            }

        }
    
    ?>
    
</body>
</html>