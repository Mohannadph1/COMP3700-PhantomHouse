<?php



define('DB_HOST',     'localhost');   
define('DB_USER',     'root');        
define('DB_PASS',     '');            
define('DB_NAME',     'phantom_house'); 


$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);


if (!$conn) {
    
    die("<p style='color:red; font-family:Arial; padding:20px;'>
         <strong>Database Connection Error:</strong> " .
         mysqli_connect_error() . "</p>");
}


mysqli_set_charset($conn, "utf8");
?>
