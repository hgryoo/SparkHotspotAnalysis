<?php
session_destroy();
session_start();
/*

php.ini

운영

display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL
log_errors = On

개발

display_errors = On
display_startup_errors = On
error_reporting = -1
log_errors = On
*/


$target_user_dir = "/home/daemon/".$_POST["user"]."/";
$target_dir = $target_user_dir.$_POST['project_name']."/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image




// Allow certain file formats
if($imageFileType != "csv" ) {
    echo "Sorry, only CSV files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";

// if everything is ok, try to upload file
} else {
    if (!file_exists($target_user_dir)){
      echo "mkdir user\n";
      mkdir($target_user_dir,0777);
    }
    if (!file_exists($target_dir)){
      echo "mkdir project\n";
      mkdir($target_dir,0777);
    }
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

chmod($target_file,0777);
$handle = fopen("$target_file", "r");
 if ($handle) {
        $buffer = fgetcsv($handle, 1000,",");



        $column_size = count($buffer);

        $_SESSION["column_size"] = $column_size;
        $_SESSION["column_list"] = $buffer;
        $_SESSION["user"] = $_POST['user'];
        $_SESSION['pro_name'] = $_POST['project_name'];
        $_SESSION['target_file'] = $target_file;
        $_SESSION['target_dir'] = $target_dir;
       // $_SESSION['column_list'] = $buffer;
        urlencode(serialize($buffer));

        echo $column_size;


    }

    $_SESSION["process"] = 0;
    echo("<script>location.replace('pick_column.php');</script>");


    fclose($handle);


 ?>
