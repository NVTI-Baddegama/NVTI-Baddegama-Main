<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "nvti_baddegama"; 

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
<?php
// $servername = "sql200.alwaysfreehost.xyz";
// $username = "xeyra_40108320"; 
// $password = "mAMwYnllh0hmsV4"; 
// $dbname = "xeyra_40108320_Chiki"; 

// $con = new mysqli($servername, $username, $password, $dbname);

// if ($con->connect_error) {
//     die("Connection failed: {$con->connect_error}");
// }
?>
