<?php




function file_upload($file, $dir){

  $target_file = $dir . basename($file["name"]);
  $iscsvfile = true;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
  // Check if image file is a actual image or fake image


  // Allow certain file formats
  if($imageFileType != "csv" ) {
    shell_exec("rm -r $dir");
    echo '<script language="javascript">alert("Sorry, only CSV files are allowed.");</script>';
    $iscsvfile = false;
  }
  // Check if $iscsvfile is set to false by an error
  if (!$iscsvfile) {
    shell_exec("rm -r $dir");
    echo '<script language="javascript">alert("Sorry, yourfile was not uploaded.");</script>';
    return null;
    // if everything is ok, try to upload file
  } else {
    if (!file_exists($dir)){
echo '<script language="javascript">alert("Sorry..");</script>';
      return null;
    }
    if (file_exists($target_file)){
      echo '<script language="javascript">alert("Sorry..file exist");</script>';
      return null;
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
  //    echo "The file ". basename( $file["name"]). " has been uploaded.";
    } else {
      echo '<script language="javascript">alert("Sorry, there was an error uploading your file.");</script>';

      return null;
    }
  }

  chmod($target_file,0777);

  return $target_file;



}


?>
