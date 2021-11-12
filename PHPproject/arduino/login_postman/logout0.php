<?php
//DOMAIN
  if(isset($_SERVER['HTTP_TOKEN'])){
      if ($_SERVER['HTTP_TOKEN'] == "") {
        header("HTTP/1.1 402 Unauthorized");
        return;
      }
      $token = $_SERVER['HTTP_TOKEN'];
      include_once("connection.php");
      $query = "SELECT * FROM users WHERE token = '$token'";
      $result = mysqli_query($con,$query);
      if(!mysqli_num_rows($result)){
        header("HTTP/1.1 402 Unauthorized");
        return;
      }
      $query = "UPDATE users SET token='' WHERE token = '$token'";
      $result = mysqli_query($con,$query);
  } else {
    header("HTTP/1.1 402 Unauthorized");
  }

//LOCALHOST
/*
  $headers = apache_request_headers();
  if(isset($headers['token'])){
      if ($headers['token'] == "") {
        header("HTTP/1.1 402 Unauthorized");
        return;
      }
      $token = $headers['token'];
      include_once("connection.php");
      $query = "SELECT * FROM users WHERE token = '$token'";
      $result = mysqli_query($con,$query);
      if(!mysqli_num_rows($result)){
        header("HTTP/1.1 402 Unauthorized");
        return;
      }
      $query = "UPDATE users SET token='' WHERE token = '$token'";
      $result = mysqli_query($con,$query);
  } else {
    header("HTTP/1.1 402 Unauthorized");
  }
  */
?>
