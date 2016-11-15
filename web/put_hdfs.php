<?php
session_start();
if ($_SESSION['process'] == -2 || $_SESSION['process'] == -1){
  echo ("<script>self.close();</script>");
  exit();

}

$mkdir_output = shell_exec("/usr/local/hadoop/bin/hdfs dfs -mkdir -p /".$_SESSION['project_name']." 2>&1");
$put_output = shell_exec("/usr/local/hadoop/bin/hdfs dfs -moveFromLocal ".$_SESSION['target_file']." /".$_SESSION['project_name']."/ > /dev/null 2>".$_SESSION['project_dir']."/hdfs_err.txt &");


  echo ("<script>location.replace('menu.php');</script>");

?>
