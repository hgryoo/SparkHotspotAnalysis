<?php
session_start();
$column_file = fopen($_SESSION['project_dir']."column.txt", "w");
$_SESSION['type'] = $_GET['attribute'];

for ($key = 0 ; $key < $_GET['key_num']; $key++){
  $key_att = $_GET['key'];
  if ($_GET['attribute']=='latitude_longitude_time' || $_GET['attribute']=='x_y_t'){
    fwrite($column_file, (string)$key_att[$key][0]." ".(string)$key_att[$key][1]." ".(string)$key_att[$key][2]);
  }
  else if ($_GET['attribute']=='latitude_longitude' || $_GET['attribute']=='x_y'){
    fwrite($column_file, (string)$key_att[$key][0]." ".(string)$key_att[$key][1]);
  }

  if ($key != $_GET['key_num']-1 )
    fwrite($column_file,"\n");

}
fclose($column_file);
chmod($_SESSION['project_dir']."column.txt",0777);


$_SESSION['process'] = 2;

echo ("<script>location.replace('put_hdfs.php');</script>");

?>
