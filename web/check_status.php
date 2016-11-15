<?php
  $output = shell_exec("jps");
  $jps_list = explode("\n", $output);
  $is_hdfs_working = 0;
  $is_spark_working = 0;
  for ($i = 0 ; $i < count($jps_list) -1 ; $i++ ){
    $jps_line = explode(" ", $jps_list[$i]);
    if ($jps_line[1] == "FsShell"){
      $is_hdfs_working += 1;
      break;
    }
    if ($jps_line[1] == "SparkSubmit"){
      $is_spark_working += 1;
      break;
    }
  }

  echo $is_hdfs_working." ".$is_spark_working;



?>
