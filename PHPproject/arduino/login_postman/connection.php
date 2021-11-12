<?php
//DOMAIN
$dbhost = "Domain_url";
$dbuser = "Domain_login";
$dbpass = "Domain_password";
$dbname = "Domain_database";


//LOCALHOST XAMPP
/*
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "login_db";
*/
if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname)){
  die("Failed to connect". mysqli_connect_error());
}
?>
