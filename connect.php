<?php
/////////////////HOSTING1//////////////////
// $host = 'sql210.infinityfree.com';
// $user = 'if0_36226466';
// $pass = 'CpsR371OfmOq';
// $database = 'if0_36226466_XXX';

/////////////////HOSTING2//////////////////
// $host = 'localhost';
// $user = 'id22002102_projectpo111';
// $pass = '01@2024pcM';
// $database = 'id22002102_po';

//////////////////TEST///////////////////
$host = 'localhost';
$user = 'root';
$pass = '';
$database = 'purchase_order';

$conn = new mysqli($host, $user, $pass, $database);
if($conn->connect_error){
    die($conn->connect_error);
}else{
    mysqli_set_charset($conn,"utf8");
}


?>