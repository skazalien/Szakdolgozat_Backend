<?php
    include("connection.php");
  if(isset($_SERVER['HTTP_TOKEN']))){
      $token = $_SERVER['HTTP_TOKEN'];
      if ($token == "") {
        header("HTTP/1.1 402 Unauthorized");
        return;
      }
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
?>
