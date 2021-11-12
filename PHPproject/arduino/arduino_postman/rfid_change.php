<?php
include("connection.php");
if(isset($_SERVER['HTTP_TOKEN'])){
  $token = $_SERVER['HTTP_TOKEN'];
  $query = "SELECT * FROM users WHERE token = '$token' limit 1";
  $result = mysqli_query($con,$query);
  if(!mysqli_num_rows($result)){
    header("HTTP/1.1 402 Unauthorized");
    return;
  }
  $user_data = mysqli_fetch_assoc($result);
  $user_id = $user_data["user_id"];
  if($_SERVER['REQUEST_METHOD'] == "GET"){
        $query = "SELECT DISTINCT rfid_id FROM rfid_manager WHERE user_request = 1";
        $result = mysqli_query($con,$query);
        $temp = array();
        if(mysqli_num_rows($result) > 0){
            $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
            foreach($all as $row){
                array_push($temp, $row['rfid_id']);
            }
        }
        echo json_encode($temp);
        return;
    }
  if($_SERVER['REQUEST_METHOD'] == "POST"){
      if(isset($_POST['changeRFID'])){
          $rfid_id = $_POST['rfid_id'];
          $query = "UPDATE rfid_manager SET user_request = 0 WHERE rfid_id = '$rfid_id'";
          $result = mysqli_query($con, $query);
          $query = "UPDATE users SET rfid_id = 'Undefined' WHERE rfid_id = '$rfid_id'";
          $result = mysqli_query($con, $query);
          $object = (object) ['status' => boolval(true)];
          echo json_encode($object);
          return;
      }
      else{
          $query = "UPDATE rfid_manager SET user_request = 1 WHERE rfid_id = 
          (SELECT rfid_id FROM users WHERE token = '$token' AND admin_user = 0)";
          $result = mysqli_query($con, $query);
          $object = (object) ['status' => boolval(true)];
          echo json_encode($object);
          return;
      }
      
      
  }
  
    

}
?>