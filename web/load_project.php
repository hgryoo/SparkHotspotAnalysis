<?php
session_start();
$proj_name = str_replace(" ","",$_POST['load_proj_name']);

if (file_exists("/home/daemon/".$proj_name."/column.txt") && file_exists("/home/daemon/".$proj_name."/session.txt")){
  $sessionfile = fopen("/home/daemon/".$proj_name."/session.txt", "r");
  $_SESSION['column_size'] = str_replace("\n","",fgets($sessionfile, 4096));
  $_SESSION['file_number'] = str_replace("\n","",fgets($sessionfile, 4096));
  $_SESSION['file_list'] = array();
  for ($i = 0 ; $i < $_SESSION['file_number'] ; $i++){
    array_push($_SESSION['file_list'],str_replace("\n","",fgets($sessionfile, 4096)) );
  }

  $_SESSION['process'] = str_replace("\n","",fgets($sessionfile, 4096));
  $_SESSION['project_dir'] = str_replace("\n","",fgets($sessionfile, 4096));
  $_SESSION['project_name']= str_replace("\n","",fgets($sessionfile, 4096));
  $_SESSION['type']= str_replace("\n","",fgets($sessionfile, 4096));
  fclose($sessionfile);



  echo "<script>location.replace('menu.php');</script>";

}else{
  echo "no project!";
}

?>
