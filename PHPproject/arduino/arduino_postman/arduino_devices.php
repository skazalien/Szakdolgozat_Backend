<?php
include("connection.php");
if(isset($_SERVER['HTTP_TOKEN'])){
    if (trim($_SERVER['HTTP_TOKEN']) == "") {
        header("HTTP/1.1 402 Unauthorized");
        return;
    }
    $token = $_SERVER['HTTP_TOKEN'];
    if($_SERVER['REQUEST_METHOD'] == "GET"){
        $query = "SELECT * FROM arduino_devices";
        $result = mysqli_query($con, $query);
        if(mysqli_num_rows($result) > 0){
            $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode($all);
            return;
        }
        else{
            header("HTTP/1.1 404 Bad Request");
            return;
        }
    }
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(!isset($_POST['arduino_id']) || !isset($_POST['url'])){
            header("HTTP/1.1 404 Bad Request");
            return;
        }
       $arduino_id = $_POST['arduino_id'];
       $url = $_POST['url'];
       $query = "UPDATE arduino_devices SET video_url = '$url' WHERE arduino_id = '$arduino_id'";
       $result = mysqli_query($con, $query);
       if($result){
        $res = (object) ['isUpdated' => boolval(true), 'arduino_id' => $arduino_id, 'url' => $url];
        echo json_encode($res);
        return;
       }
        $res = (object) ['isUpdated' => boolval(false), 'arduino_id' => $arduino_id, 'url' => $url];
        echo json_encode($res);
        return;
    }
}
?>