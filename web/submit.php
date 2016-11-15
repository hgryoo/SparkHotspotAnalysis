<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="apple-touch-icon" sizes="76x76" href="x_material_kit/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="x_material_kit/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Spark HotSpot Analysis by ABC </title>

  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

  <!-- Canonical SEO -->
  <link rel="canonical" href="http://www.creative-tim.com/product/material-kit"/>

  <!--     Fonts and icons     -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

  <!-- CSS Files -->
  <link href="x_material_kit/assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="x_material_kit/assets/css/material-kit.css" rel="stylesheet"/>
  <!-- Material thema ------------------------>
  <title></title>
  <style type="text/css">
  #card-signup .content {
    padding: 0px 30px 0px 30px;
  }
  </style>

  <?php
  session_start();

  if (isset($_POST['process'])){
    shell_exec("rm ".$_SESSION['project_dir']."*.csv");
    $degree = $_POST['degree'];
    $time_step = $_POST['time_step'];
    $hotspot_num = $_POST['hotspot_num'];
    $sampling = $_POST['sampling_rate'];
    $input_path = "hdfs://164.125.37.194:9000/".$_SESSION['project_name']."/";
    $output_path = $_SESSION['project_dir'];

    $column = $_SESSION['project_dir']."column.txt";

    if ($_SESSION['type'] == 'latitude_longitude_time'){
      $output =
      shell_exec("/usr/local/spark/bin/spark-submit --master spark://164.125.37.194:7077 --driver-memory 10g --executor-memory 10g /home/cocoss/SparkHotSpotAnalysis/half_HOTSPOT_v0.5/target/scala-2.10/hotspot.jar $degree $time_step $input_path $output_path $hotspot_num $column $sampling 1> ".$_SESSION['project_dir']."/spark_log.txt 2>".$_SESSION['project_dir']."/spark_err.txt &" );

  }else if($_SESSION['type'] == 'x_y_t'){
    $output =
    shell_exec("/usr/local/spark/bin/spark-submit --master spark://164.125.37.194:7077 --driver-memory 10g --executor-memory 10g /home/cocoss/SparkHotSpotAnalysis/half_x_y_t_v0.1/target/scala-2.10/hotspot.jar $degree $time_step $input_path $output_path $hotspot_num $column $sampling 1> ".$_SESSION['project_dir']."/spark_log.txt 2>".$_SESSION['project_dir']."/spark_err.txt &" );
  }else if($_SESSION['type'] == 'x_y'){
    $output =
    shell_exec("/usr/local/spark/bin/spark-submit --master spark://164.125.37.194:7077 --driver-memory 10g --executor-memory 10g /home/cocoss/SparkHotSpotAnalysis/half_x_y_v0.2/target/scala-2.10/hotspot.jar $degree 0 $input_path $output_path $hotspot_num $column $sampling 1> ".$_SESSION['project_dir']."/spark_log.txt 2>".$_SESSION['project_dir']."/spark_err.txt &" );
}else if($_SESSION['type'] == 'latitude_longitude'){
  $output =
  shell_exec("/usr/local/spark/bin/spark-submit --master spark://164.125.37.194:7077 /home/cocoss/SparkHotSpotAnalysis/half_lat_long_v0.2/target/scala-2.10/hotspot.jar $degree 0 $input_path $output_path $hotspot_num $column $sampling 1> ".$_SESSION['project_dir']."/spark_log.txt 2>".$_SESSION['project_dir']."/spark_err.txt &");
}

$_SESSION['process'] = 4;
echo "<script>location.replace('menu.php')</script>;";
}


?>

</head>
<body class="signup-page" >



  <nav id="mainNav" class="navbar navbar-default navbar-custom navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
           
                
                <a class="navbar-brand page-scroll" href="#page-top" style = "color : #ffffff" >HotSpot with Spark - Make your own work!</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->

            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

  <div class="wrapper">
    <div class="header header-filter" style="background-image: url('x_material_kit/assets/img/city.jpg'); background-size: cover; background-position: top center;">
      <div class="container">
        <div class="row">
          <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3" style="width:100%; margin-right:auto; margin-left:auto;" >
            <div class="card card-signup center-block">

              <div class="header header-primary text-center">
                <h4>Enter Submition Option</h4>

              </div>

              <div class="content" style = "padding : 0px 10px 0px 10px ">



                <form class="text-center block-center" method="POST" action="submit.php"  >

                  <br />
                  <div class = "panel panel-default center-block" >
                    <div class = "panel-heading">
                    Enter degree, time_step, Hotspot_num, sampling_rate <br />
                    </div>
                    
                     <div class= "panel-body input-group" style="display:block">
                    <div >
                      degree</br>
                      <input type="text" name="degree" placeholder="degree"/> <br /><br/>
                    </div>

                    <div id="time">
                      time_step(day)</br>
                      <input type="text" name="time_step" placeholder="time_step"/> <br /><br/>
                    </div>
                    <div>
                      hotspot_num(number)</br>
                        <input type="text" name="hotspot_num" placeholder="hotspot_num"/> <br /><br/>
                    </div>
                    sampling_rate</br>
			              <input type="text" name="sampling_rate" placeholder="sampling_rate" /><br />
			

                    </div>
                  </div>
                    
                    <div class = "panel panel-default center-block">
                      <div class = "panel-heading">
                      Help
                      <br />
                    </div>
                    <div class = "panel-body">
		     degree : 경위도 좌표에서는 각도의 '도'를 의미한다. </br>
			절대 좌표계에서는 정해진 좌표계의 단위를 나타낸다.</br>
			time step : 얼마만큼의 시간을 묶어서 볼 것인지를 나타낸다. 기본단위는 1일 </br>
			hotspot_num : 상위 몇개만큼의 hotspot을 볼 것인지 그 갯수를 나타낸다. 단위는 '개'</br>
			Sampling rate 범위 : 0.01 <= Sampling rate <= 0.99 </br>
                     	권장 Sampling rate : 0.1 <br />
		      Sampling rate 가 높으면 정확도가 높아지지만 느려진다.<br />
	
                    </div>
                      
                    </div>

                    <input type="hidden" name="process" value="4"></input>
                    <div class = "text-center center-block">
                      <input type="submit" class = "btn btn-simple btn-primary btn-lg" value="spark-submit"> </input>
                    </div>
                  </form>


            </div>


          </div>
        </div>
      </div>
    </div>


  </div>

</div>

<script type="text/javascript">
<?php 
  if ($_SESSION['type'] == 'latitude_longitude' || $_SESSION['type'] == 'x_y'){
	echo 'document.getElementById("time").style.display="none";';
        echo 'document.getElementById("time").value = 0;';
}

?>
</script>
<!-- Material Thema ---------------------->
<!--   Core JS Files   -->
<script src="x_material_kit/assets/js/jquery.min.js" type="text/javascript"></script>
<script src="x_material_kit/assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="x_material_kit/assets/js/material.min.js"></script>

<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="x_material_kit/assets/js/nouislider.min.js" type="text/javascript"></script>

<!--  Plugin for the Datepicker, full documentation here: http://www.eyecon.ro/bootstrap-datepicker/ -->
<script src="x_material_kit/assets/js/bootstrap-datepicker.js" type="text/javascript"></script>

<!-- Control Center for Material Kit: activating the ripples, parallax effects, scripts from the example pages etc -->
<script src="x_material_kit/assets/js/material-kit.js" type="text/javascript"></script>

<!-- Material Thema ---------------------->


</body>
</html>
