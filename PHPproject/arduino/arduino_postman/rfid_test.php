<?php
include("connection.php");
if($_SERVER['REQUEST_METHOD'] == "GET"){
    $rfid_id = $_GET['rfid_id'];
    $arduino_id = $_GET['arduino_id'];
    $query = "SELECT * FROM rfid_manager WHERE rfid_id = '$rfid_id'";
    $result = mysqli_query($con, $query);
    $msg = "";
    if(mysqli_num_rows($result) > 0){
      $all = mysqli_fetch_all($result,MYSQLI_ASSOC);
      $isValid = 0;
      foreach($all as $row){
        if($row['arduino_id'] == $arduino_id && $row['access_state'] == 1){
          $msg = '<data>true</data>';
          $isValid = 1;
          break;
        }
      }
      if(!$isValid){
        $msg = '<data>false</data>';
      }
      $query = "INSERT INTO arduino_logs(rfid_id, arduino_id, state, openedWith)
                VALUES('$rfid_id', '$arduino_id', '$isValid', 'Arduino')";
      $result = mysqli_query($con, $query);
      echo($msg);
    }
    else{
      echo('<data>false</data>');
    }
}
?>
