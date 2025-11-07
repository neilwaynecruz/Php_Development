<?php

    $nameErr = $emailErr = $webErr = $genderErr = "";
    $name = $email = $website = $comment = $gender = "";
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty($_POST["name"])){
            $nameErr = "Name is required";
        } else {
            $name = test_input($_POST["name"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
                $nameErr = "Only letters and white space allowed";
                $name = "";
             } else{
                $name = test_input($_POST["name"]);
             }
        }

        if(empty($_POST['email'])){
            $emailErr = "Email is required";
        } else {
            $tempEmail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            if(!$tempEmail){
                $emailErr = "Invalid email format";
            } else {
                $email = test_input($_POST['email']);
            }
        }

        if(empty($_POST['website'])){
            $website = "";
        } else {
            $tempWebsite = filter_input(INPUT_POST, 'website',FILTER_VALIDATE_URL);
            if(!$tempWebsite){
                $webErr = "Invalid Url";
            } else{
                $website = test_input($_POST['website']);
            }
        }

        if(empty($_POST['comment'])){
            $comment = "";
        } else {
            $comment = test_input($_POST['comment']);
        }

        if(empty($_POST['gender'])){
            $genderErr = "Gender is required";
        } else {
            $gender = test_input($_POST['gender']);
        }

    }


    function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
    <style>
        .error{
            color: red;
        }
    </style>
</head>
<body>



    <h2>PHP Form Validation Example</h2>
    <p><span class="error">* required fields</span></p>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name);?>">
        <span class="error">* <?php echo htmlspecialchars($nameErr);?></span>
        <hr>
        
        E-mail: <input type="text" name="email" value="<?php echo htmlspecialchars($email);?>">
        <span class="error">* <?php echo htmlspecialchars($emailErr);?></span>
        <hr>

        Website: <input type="text" name="website" value="<?php echo htmlspecialchars($website);?>">
        <span class="error"> <?php echo htmlspecialchars($webErr);?></span>
        <hr>

        Comment: <textarea name="comment" rows="3" cols="40"></textarea>
        <hr>

        Gender: 
        <input type="radio" name="gender" value="Female" <?php if(isset($gender) && $gender == "Female") echo "checked"?>>Female
        <input type="radio" name="gender" value="Male" <?php if(isset($gender) && $gender == "Male") echo "checked"?>>Male
        <input type="radio" name="gender" value="Other" <?php if(isset($gender) && $gender == "Other") echo "checked"?>>Other
        <span class="error">* <?php echo htmlspecialchars($genderErr);?></span>
        <hr>

        <input type="submit" name="submit" value="Submit">
    </form>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($nameErr) && empty($emailErr) && empty($webErr) && empty($genderErr)) {
            echo "<h3>Your Input:</h3>";
            echo "Name: " . htmlspecialchars($name) . "<br>";
            echo "Email: " . htmlspecialchars($email) . "<br>";
            echo "Website: " . htmlspecialchars($website) . "<br>";
            echo "Comment: " . htmlspecialchars($comment) . "<br>";
            echo "Gender: " . htmlspecialchars($gender) . "<br>";
        }
    ?>
    
</body>
</html>