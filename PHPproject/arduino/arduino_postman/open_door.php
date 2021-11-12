<?php
include("connection.php");
if($_SERVER['REQUEST_METHOD'] == "POST"){
  if(isset($_SERVER['HTTP_TOKEN'])){
    $token = $_SERVER['HTTP_TOKEN'];
    $query = "SELECT * FROM users WHERE token = '$token' limit 1";
    $result = mysqli_query($con,$query);
    if(!mysqli_num_rows($result)){
      header("HTTP/1.1 402 Unauthorized");
      return;
    }
    $arduino_id = $_POST['arduino_id'];
    $rfid_id = $_POST['rfid_id'];
    $query = "UPDATE rfid_manager SET isRequested = 1 WHERE rfid_id = '$rfid_id' AND arduino_id = '$arduino_id'";
    $result = mysqli_query($con,$query);
    $res = (object) ['status' => boolval(true), 'isOpened' => boolval(true)];
    echo json_encode($res);
    return;
  }
}

if ($_SERVER['REQUEST_METHOD'] == "GET"){
  $arduino_id = $_GET['arduino_id'];
  $query = "SELECT * FROM rfid_manager WHERE arduino_id = '$arduino_id'";
  $result = mysqli_query($con,$query);
  $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
  foreach($all as $row){
    if ($row['isRequested'] == 1) {
      $rfid_id = $row['rfid_id'];
      $query = "UPDATE rfid_manager SET isRequested = 0 WHERE arduino_id = '$arduino_id'";
      $result = mysqli_query($con,$query);
      echo("<data>true</data>");
      $query = "INSERT INTO arduino_logs(rfid_id, arduino_id, state, openedWith)
                VALUES('$rfid_id', '$arduino_id', 1, 'Application')";
      $result = mysqli_query($con, $query);
      return;
    }
  }
}
?>
