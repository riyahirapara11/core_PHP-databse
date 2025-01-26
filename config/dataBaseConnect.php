<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname   = "phpvi";

// Create connection
$connection = new mysqli($servername, $username , $password, $dbname );

// Check connection
if ($connection->connect_error) {
  echo " failed to connect ";
  die("Connection failed: " . $connection->connect_error);
}


// echo "connected successfully ";

?>