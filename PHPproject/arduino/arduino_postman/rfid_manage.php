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

  /*
  $query = "SELECT rfid_manager.arduino_id, rfid_manager.access_state, rfid_manager.rfid_id, rfid_manager.id FROM rfid_manager
            INNER JOIN arduino_assets ON rfid_manager.rfid_id = arduino_assets.rfid_id WHERE arduino_assets.user_id = '$user_id'";
  */
  $query = "SELECT arduino_id, access_state, rfid_id, id FROM rfid_manager
            WHERE rfid_id = (SELECT rfid_id from users WHERE token = '$token')";
  $result = mysqli_query($con, $query);

  if(mysqli_num_rows($result) > 0){
    $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
  }
  else{
    if($_SERVER['REQUEST_METHOD'] == "GET"){
      $temp = array();
      $object = (object) ['status' => boolval(true)];
      $msg = json_encode($temp);
      echo $msg;
      return;
    }
    $query = "SELECT rfid_id from users where token = '$token'";
    $result = mysqli_query($con,$query);
    $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $all[0]['access_state'] = 0;
    $all[0]['arduino_id'] = "Undefined";
  }

  if($_SERVER['REQUEST_METHOD'] == "GET"){
      $temp = array();
      $query = "SELECT admin_user FROM users WHERE token='$token'";
      $result = mysqli_query($con,$query);
      $admin_user = mysqli_fetch_assoc($result)['admin_user'];
      foreach($all as $row){
        $row['access_state'] = boolval($row['access_state']);
        $row['admin_user'] = $admin_user;
       // if($row['access_state']){
            if(isset($_GET['only_active'])){
              if($row['access_state']){
                array_push($temp,$row);
              }
            }
            else{
              array_push($temp,$row);
            }
       // }
      }
      $msg = json_encode($temp);
      echo $msg;
      return;
  }

  if($_SERVER['REQUEST_METHOD'] == "POST"){
      //TODO: MODIFY

      $arduino_id = $_POST['arduino_id'];
      $rfid_id = $_POST['rfid_id'];

      if (isset($_POST['id'])) {
        $rfid_id = $_POST['rfid_id'];
        $access_state = $_POST['access_state'];
        $id = $_POST['id'];

        //TODO KUKA arduino_assets
        $query = "UPDATE rfid_manager SET arduino_id = '$arduino_id',
                  rfid_id = '$rfid_id',
                  access_state = '$access_state'
                  WHERE id = '$id'";
        $result = mysqli_query($con, $query);
        $object = (object) ['status' => boolval(true)];
        $msg = json_encode($object);
        echo $msg;
        return;
      }

      //////////////Insert
      //TODO: INSERT NEW LINE
      $query = "SELECT arduino_id from rfid_manager where rfid_id = '$rfid_id'";
      $result = mysqli_query($con, $query);
      $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
      $assignedArduinos = [];
      foreach($all as $row){
        array_push($assignedArduinos, $row['arduino_id']);
      }
      //echo $arduino_id;
      //print_r($assignedArduinos);
      if(in_array($arduino_id, $assignedArduinos))
      {

        header("HTTP/1.1 409 Conflict");
        $res = (object) ['status' => boolval(false)];
        $msg = json_encode($res);
        echo $msg;
        return;
      }
      else{

        //print_r($arduino_id);
        //print_r($assignedArduinos);
        $query = "INSERT INTO rfid_manager(rfid_id, access_state, arduino_id, isRequested)
                  Values ('$rfid_id', 1, '$arduino_id', 0)";
        $result = mysqli_query($con, $query);
        $query = "UPDATE users SET rfid_id = '$rfid_id' WHERE user_id = '$user_id'";
        $result = mysqli_query($con, $query);
        $object = (object) ['status' => boolval(true)];
        $msg = json_encode($object);
        echo $msg;
        return;
      }
  }
}
?>
