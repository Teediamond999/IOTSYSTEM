<?php 
$conn = mysqli_connect('localhost', 'root','', 'iot_db');

if(!$conn){
    echo "Database not connected";
}

?>