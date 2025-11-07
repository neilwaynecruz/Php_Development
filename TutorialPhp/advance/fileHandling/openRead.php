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

 
        // PHP Read File - fread()
        $myfile = fopen('webdictionary.txt','r') or die("Enable to open file");
        echo fread($myfile,filesize("webdictionary.txt")) . "<br><br>";
        fclose($myfile);

        // PHP "Read Single Line" - fgets()
        $myfile = fopen('webdictionary.txt','r') or die("Enable to open file");
        echo fgets($myfile) . "<br><br>";
        fclose($myfile);

        //PHP Read "Single Character" - fgetc()
        $myfile = fopen('webdictionary.txt','r') or die("Enable to open file");
        while(!feof($myfile)) {
             echo fgetc($myfile) ;
        }
        echo "<br> <br>";
        fclose($myfile);


        // PHP Check End-Of-File - feof()
        $myfile = fopen('webdictionary.txt','r') or die("Enable to open file");
        while(!feof($myfile)){
            echo fgets($myfile) . "<br>";
        }
        fclose($myfile);


        
    ?>
</body>
</html>