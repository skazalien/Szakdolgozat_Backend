<?php
  include("connection.php");
  include("functions.php");

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    //something was posted
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $msg = "";

      ///////////
      if(!empty($user_name) && !empty($password))
      {
        //read from database
        $query = "SELECT * FROM users
                  WHERE user_name = '$user_name' limit 1";
        $result = mysqli_query($con,$query);
        //in case of result is true
        if($result && mysqli_num_rows($result) > 0)
        {
          $user_data = mysqli_fetch_assoc($result);
          if($user_data['password'] === md5($password))
          {
            $currentUser = $user_data['user_id'];
            $currentEmail = $user_data['email'];
            $res = (object) ['status' => boolval(true), 'email' => $currentEmail];
            $token = md5(uniqid());
            $query = "UPDATE users SET token='$token' WHERE user_id = '$currentUser'";
            $result = mysqli_query($con,$query);
            $msg = json_encode($res);
            header('Token:'.$token);
            echo $msg;

          } else {
            header("HTTP/1.1 402 Unauthorized");
            $msg = json_encode("Wrong Username or Password case1");
            echo $msg;
          }
        }
        else{
        header("HTTP/1.1 402 Unauthorized");
        $msg = json_encode("Wrong Username or Password case2");
        echo $msg;
        }
      }
      //////////
      else
      {
        header("HTTP/1.1 412 Precondition Failed");
        $msg = json_encode("Wrong Username or Password case2");
        echo $msg;
      }
    }


?>
