<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superglobals</title>
</head>
<body>


    <?php
    // Superglobals are built-in variables that are always accessible, regardless of scope.

    // $GLOBALS
    $x = 75;
    $y = 0;
    $z = 0;

    sum();

    function sum(){
        // $sum = $GLOBALS["x"] + $GLOBALS["y"] + $GLOBALS["z"];
        //or
        global $x, $y, $z;
        $sum = $x + $y + $z;

        echo "The sum is: {$sum} <br>";
    }

    //$_SERVER
    // $_SERVER is a PHP superglobal variable which holds information about headers, paths, and script locations.
    echo $_SERVER['PHP_SELF'] . "<br>"; // the filename of the currently executing script
    echo $_SERVER['GATEWAY_INTERFACE'] . "<br>"; // the version of the Common Gateway Interface (CGI) the server is using
    echo $_SERVER['SERVER_NAME'] . "<br>"; // the name of the server host under which the current script is executing
    echo $_SERVER['HTTP_HOST'] . "<br>"; // the contents of the Host: header from the current request, if there is one
    echo $_SERVER['HTTP_USER_AGENT'] . "<br>"; // contents of the User-Agent: header from the current request, if there is one
    echo $_SERVER['SCRIPT_NAME'] . "<br>"; // the path of the current script 
    echo $_SERVER['REQUEST_METHOD'] . "<br>"; // which request method was used to access the page; e.g. 'GET', 'HEAD', 'POST', 'PUT'.
    echo $_SERVER['QUERY_STRING'] . "<br>"; // the query string, if any, via which the page was accessed
    echo $_SERVER['SERVER_PORT'] . "<br>"; // the port on the server machine being used by the web server for communication
    echo $_SERVER['REMOTE_ADDR'] . "<br>"; // the IP address from which the user is viewing the current page
    echo $_SERVER['REMOTE_PORT'] . "<br>"; // the port being used on the user's machine to communicate with the web server
    echo $_SERVER['REQUEST_TIME'] . "<br>"; // the timestamp of the start of the request (available since PHP 5.1.0)
    echo $_SERVER['REQUEST_TIME_FLOAT'] . "<br>"; // the timestamp of the start of the request, with microsecond precision (available since PHP 5.4.0)
    echo $_SERVER['SERVER_PROTOCOL'] . "<br>"; // the name and revision of the information protocol via which the page was requested; e.g. 'HTTP/1.0'; 'HTTP/1.1'
    echo $_SERVER['SERVER_SOFTWARE'] . "<br>"; // server identification string, given in the headers when responding to requests
    echo $_SERVER['DOCUMENT_ROOT'] . "<br>"; // the document root directory under which the current script is executing, as defined in the server's configuration file
    echo $_SERVER['SCRIPT_FILENAME'] . "<br>"; // the absolute pathname of the currently executing script 
    echo $_SERVER['PHP_SELF'] . "<br>"; // the filename of the currently executing script
    echo $_SERVER['GATEWAY_INTERFACE'] . "<br>"; // the version of the Common Gateway Interface (CGI) the server is using
    echo $_SERVER['SERVER_ADMIN'] . "<br>"; // the value given to the SERVER_ADMIN directive in the web server configuration file
    echo $_SERVER['SERVER_SIGNATURE'] . "<br>"; // the server version and virtual host name which are added to server-generated pages, if enabled

    // $_REQUEST
    // $_REQUEST is used to collect data after submitting an HTML form.
    // $_REQUEST can collect data sent via both GET and POST methods
    // Note: Using $_REQUEST is not recommended for production code as it can lead to security issues.

    echo("
    <form method='POST' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
        Name: <input type='text' name='fname'>
        <input type='submit'><br>
    </form>");
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // collect value of input field
        $name = htmlspecialchars($_REQUEST['fname']);
        if (empty($name)) {
            echo "Name is empty";
        } else {
            echo $name;
        }
    }

    // $_POST
    // $_POST is used to collect form data after submitting an HTML form with method="post".
    // difference between $_REQUEST and $_POST is that $_REQUEST can collect data 
    // sent via both GET and POST methods, while $_POST only collects data sent via the POST method

    echo("
    <form method='POST' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'></form>
        Name: <input type='text' name='fname'>
        <input type='submit'><br>
    </form>");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // collect value of input field
        $name = htmlspecialchars($_POST['fname']);
        if (empty($name)) {
            echo "Name is empty";
        } else {
            echo $name;
        }
    }

    // $_GET
    // $_GET is used to collect form data after submitting an HTML form with method="get
    // $_GET can also collect data sent in the URL
    echo "
<form method='GET' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
    Name: <input type='text' name='fname'>
    <input type='submit'>
</form>
";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Collect value of input field
    $name = htmlspecialchars($_GET['fname']);
    if (empty($name)) {
        echo "Name is empty";
    } else {
        echo "Hello, " . $name . "!";
    }
}


    

    ?>
    
</body>
</html>