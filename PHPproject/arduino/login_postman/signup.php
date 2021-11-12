<?php
session_start();

  include("connection.php");
  include("functions.php");

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    //something was posted

    $msg = "";
    if(isset($_POST['email']) && isset($_POST['user_name']) && isset($_POST['password']) && isset($_POST['password2'])){

      $email  = $_POST['email'];
      $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
      $user_name = $_POST['user_name'];
      $password = $_POST['password'];
      $password2 = $_POST['password2'];
      $md5password = md5($password);
      $md5password2 = md5($password2);

      if(!empty($email) && !empty($user_name) && !empty($password) && !empty($password2)){
        if(strlen($user_name)>40 || strlen($password)>100 || strlen($password2)>100){
          //echo '1';
          header("HTTP/1.1 412 Precondition Failed");
          $res = (object) ['status' => boolval(false)];
          $msg = json_encode($res);
          echo $msg;
          return;
        }
        elseif(strlen($password)<6 || strlen($user_name) < 6){
          //echo '2';
          header("HTTP/1.1 412 Precondition Failed");
          $res = (object) ['status' => boolval(false)];
          $msg = json_encode($res);
          echo $msg;
          return;
        }
        elseif (filter_var($emailB, FILTER_VALIDATE_EMAIL) === false || $emailB != $email){
          //echo '3';
          header("HTTP/1.1 412 Precondition Failed");
          $res = (object) ['status' => boolval(false), 'isEmail' => boolval(false)];
          $msg = json_encode($res);
          echo $msg;
          return;
        }
        else{
          $query = "SELECT user_name FROM users WHERE user_name = '$user_name' UNION
                    SELECT email from users WHERE email = '$email'";
          $result = mysqli_query($con,$query);
          $all = mysqli_fetch_all($result, MYSQLI_NUM);
          $emailOrUsername = [];
          foreach($all as $row){
            if(in_array($user_name, $row) || in_array($email, $row)){
              array_push($emailOrUsername, $row[0]);
            }
          }
          if(count($emailOrUsername)>0){
            header("HTTP/1.1 409 Conflict");
            $res = (object) ['status' => boolval(false)];
            $msg = json_encode($res);
            echo $msg;
            return;
          }
          else{
              if($md5password == $md5password2){
                //save to database
                $user_id = uniqid();
                $query = "INSERT INTO users (user_id, email, user_name,password)
                          values ('$user_id','$emailB', '$user_name','$md5password')";
                mysqli_query($con,$query);
                $res = (object) ['status' => boolval(true)];
                $msg = json_encode($res);
                echo $msg;
                return;
              }
              else{
                header("HTTP/1.1 406 Not Acceptable");
                $res = (object) ['status' => boolval(false), 'passwordsMatch' => boolval(false)];
                $msg = json_encode($res);
                echo $msg;
                return;
                }
          }
        }
      }
        else{
        header("HTTP/1.1 400 Bad Request");
        $res = (object) ['status' => boolval(false)];
        $msg = json_encode($res);
        echo $msg;
        return;
      }
    }
    else{
      header("HTTP/1.1 400 Bad Request");
      $res = (object) ['status' => boolval(false)];
      $msg = json_encode($res);
      echo $msg;
      return;
    }
  }


?>
