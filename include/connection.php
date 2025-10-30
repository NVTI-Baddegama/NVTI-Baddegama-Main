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
// $servername = "nvtibaddegama.site";
// $username = "nvtibadde"; 
// $password = "3CY+C9*etd9Qz9"; 
// $dbname = "nvtibadde_vta_baddegama"; 

// $con = new mysqli($servername, $username, $password, $dbname);

// if ($con->connect_error) {
//     die("Connection failed: " . $con->connect_error);
// }
?>
