<?php
include("connection.php");
//$headers = apache_request_headers();
if(isset($_SERVER['HTTP_TOKEN'])){
    if (trim($_SERVER['HTTP_TOKEN']) == "") {
      header("HTTP/1.1 402 Unauthorized");
      return;
    }
    $token = $_SERVER['HTTP_TOKEN'];
    $query = "SELECT * FROM users WHERE token = '$token' limit 1";
    $result = mysqli_query($con,$query);
    if(!mysqli_num_rows($result)){
      header("HTTP/1.1 402 Unauthorized");
      return;
    }
    $user_data = mysqli_fetch_assoc($result);
    $user_rfid = $user_data['rfid_id'];
    $query = "SELECT * FROM pictures WHERE arduino_id IN
    (SELECT arduino_id FROM rfid_manager WHERE rfid_id = '$user_rfid' AND access_state = 1)";
    $result = mysqli_query($con, $query);
    if(mysqli_num_rows($result)){
      $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
      echo json_encode($all);
      return;
    }
    else{
      $temp = array();
      echo json_encode($temp);
      return;
    }
} else {
  header("HTTP/1.1 402 Unauthorized");
}
?>
