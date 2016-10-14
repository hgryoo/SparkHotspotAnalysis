<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title></title>
</head>
<body>

  <?php
  session_start();

  $degree = $_POST['degree'];
  $time_step = $_POST['time_step'];
  $hotspot_num = $_POST['hotspot_num'];

  $input_path = "hdfs://164.125.37.194:9000/".$_SESSION['user']."/".$_SESSION['pro_name']."/";
  $output_path = $_SESSION['target_dir'];


  var_dump($output_path);

  $column = $_SESSION['target_dir']."column.txt";

  //chmod()
  //hdfs://164.125.37.194:9000/data/data/yellow_tripdata_2015-01.csv
  $output =
    shell_exec("/usr/local/spark/bin/spark-submit --master spark://164.125.37.194:7077\\
    /home/cocoss/SparkHotSpotAnalysis/half_HOTSPOT_v0.3/target/scala-2.10/hotspot.jar \\
    $degree $time_step $input_path $output_path $hotspot_num $column 2>&1" //1> /dev/null
  );
echo "<pre>$output</pre>";
echo "<pre>Its done</pre>";
?>
<form method="GET" action="leaflet.php">
  <input type="submit" text="leaflet" />
</form>
</body>
</html>
