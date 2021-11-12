<?php
// Rui Santos
// Complete project details at https://RandomNerdTutorials.com/esp32-cam-post-image-photo-server/
// Code Based on this example: w3schools.com/php/php_file_upload.asp

$file_tmp = $_FILES['imageFile']['tmp_name'];
$file_name = $_FILES['imageFile']['name'];
$temp = explode(".", $file_name);
$tempname = explode("---", reset($temp));
$arduino_id = end($tempname);
$esp32 = reset($tempname) . "_";

echo $file_name;

$directory = "uploads/";
$filecount = 0;
$files = glob($directory . "*");  //* = any filetype
if ($files) {$filecount = count($files);}
$filecount += 1;
$newfilename = $esp32 . $filecount . '.' . end($temp);
$file_destination = 'uploads/' . $newfilename;


$uploadOk = 1;
$imageFileType = strtolower(pathinfo($file_destination,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image

{
  $check = getimagesize($_FILES["imageFile"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  }
  else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($file_destination)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["imageFile"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  header("HTTP/1.1 412 Precondition Failed"); //file was not uploaded.
  $res = (object) ['isUploaded' => boolval(false), 'isValidType' => boolval(false)];
  echo json_encode($res);
  return;
}
else {
  if (isset($arduino_id)) {
    if(move_uploaded_file($file_tmp, $file_destination)){
      include_once("connection.php");
        $query = "INSERT INTO pictures (img_name, arduino_id)
                  values ('$newfilename', '$arduino_id')";
        $result = mysqli_query($con, $query);
        if($result){
          $res = (object) ['isUploaded' => boolval(true)]; //was uploaded
          echo json_encode($res);
        }
    }
    else{
      header("HTTP/1.1 404 Not Found"); //file was not uploaded.
      $res = (object) ['isUploaded' => boolval(false)];
      echo json_encode($res);
    }
  }
  else {
    header("HTTP/1.1 428 Precondition Required"); //file was not uploaded, arduino missing.
    $res = (object) ['isUploaded' => boolval(false), 'isMissing' => "arduino_id"];
    echo json_encode($res);
  }
}
?>
