<?php

session_start();

shell_exec("/usr/local/hadoop/bin/hdfs dfs -rm -r /".$_SESSION['project_name']."/ 2>&1");
shell_exec("rm -r ".$_SESSION['project_dir']);

session_destroy();

echo "<script>location.replace('home.php');</script>";
 ?>
