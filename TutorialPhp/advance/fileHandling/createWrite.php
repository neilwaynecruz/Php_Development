<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

        // r = Open a file for read only. File pointer starts at the beginning of the file

        // w = Open a file for write only. Erases the contents of the file or creates a new file if it doesn't exist. File pointer starts at the beginning of the file

        // a = Open a file for write only. The existing data in file is preserved. File pointer starts at the end of the file. Creates a file if the file doesn't exist

        // x = Creates a new file for write only. Returns FALSE and an error if file already exists

        // r+ = Open a file for read/write. File pointer starts at the beginning of the file

        // w+ = Open a file for read/write. Erases the contents of the file or creates a new file if it doesn't exist. File pointer starts at the beginning of the file

        // a+ = Open a file for read/write. The existing data in file is preserved. File pointer starts at the end of the file. Creates a new file if the file doesn't exist

        // x+ = Creates a new file for read/write. Returns FALSE and an error if file already exists


        // PHP Write to File - fwrite()
        
        $myfileWrite = fopen("listOfNames.txt","w") or die("Enable to open file");
        $name = "Neil Wayne Cruz\n";
        fwrite($myfileWrite,$name);
        $name = "Junella Mae Andres\n";
        fwrite($myfileWrite,$name);
        $name = "Charlie Magne Rola\n";
        fwrite($myfileWrite,$name);
        fclose($myfileWrite);

        $myfileWrite = fopen("listOfNames.txt","a") or die("Enable to open file");
        $name = "Charles Gabriel Rola\n";
        fwrite($myfileWrite,$name);
        $name = "Christian Colita\n";
        fwrite($myfileWrite,$name);
        fclose($myfileWrite);
        
        $myfileWrite = fopen("listOfNames.txt","r") or die("Enable to open file");
        while(!feof($myfileWrite)){
            echo fgets($myfileWrite) . "<br>";
        }
        fclose($myfileWrite);



    ?>
</body>
</html>