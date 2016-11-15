<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">


  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Material thema ------------------------>

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
  <style>
  #bar_blank {
    border: solid 1px #000;
    height: 20px;
    width: 300px;
  }

  #bar_color {
    background-color: #006666;
    height: 20px;
    width: 0px;
  }

  #bar_blank, #hidden_iframe {
    display: none;
  }
  </style>


</head>
<body class="signup-page">
  <?php

  session_start();
  if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES["userfile"])) {

    require('proj_function.php');


    //extract column from file.
    if ($_POST['process']==0){
      $project_name = '';
      $_SESSION['process'] = 0;
      if (isset($_POST['project'])){
        $project_name = $_POST['project'];
      }
      if (!is_uploaded_file($_FILES["userfile"]['tmp_name'])){
        echo '<script language="javascript">';
        echo 'alert("file null!");';
        echo '</script>';
        $_SESSION['process'] = -1;
        exit();
      }
      if ( $project_name==''){
        echo '<script language="javascript">';
        echo 'alert("project_name null!");';
        echo '</script>';
        $_SESSION['process'] = -1;
        exit();
      }
      $project_dir = "/home/daemon/".$project_name."/";

      if ($_SESSION['process'] != -1){
        if (!file_exists($project_dir)){
          mkdir($project_dir,0777);
        }
        else{
          echo '<script language="javascript">';
          echo 'alert("project_exist!");';
          echo '</script>';

          $_SESSION['process'] = -1;
          exit();
        }
      }

      //Function is moving uploaded file to project home. return value is file path.
      $target_file = file_upload($_FILES["userfile"], $project_dir);


      if ($target_file==null){

        $_SESSION['process'] = -1;

        exit();
      }

      ini_set('auto_detect_line_endings',TRUE);
      //get column info
      $handle = fopen("$target_file", "r");
      if ($handle) {
        $buffer = fgetcsv($handle);
        $column_size = count($buffer);
        $column_list = $buffer;
        urlencode(serialize($buffer));
      }
      fclose($handle);
      ini_set('auto_detect_line_endings',FALSE);
      $_SESSION['file_list'] = array();
      array_push($_SESSION['file_list'],basename($target_file));
      $_SESSION['file_number'] = 1;
      $_SESSION['target_file'] = $target_file;
      $_SESSION['project_dir'] = $project_dir;
      $_SESSION['project_name'] = $project_name;
      $_SESSION['column_list'] = $column_list;
      $_SESSION['column_size'] = $column_size;
      //  echo ("<script>location.replace('pick_column.php');</script>");
      $_SESSION['process'] = 101;
    }else{

    }



  }
  ?>






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
          <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
            <div class="card card-signup">

              <div class="header header-primary text-center">
                <h4>Create Your Project</h4>

              </div>

              <div class="content">

                <div id="bar_blank" class = "progress center-block">

                  <div id="bar_color" class = "progress-bar" role="progressbar">
                  </div>
                </div>
                <div id="status"></div>

                <form action="column_setting.php" method="POST"
                  id="myForm" enctype="multipart/form-data" target="hidden_iframe">


                  <input type="hidden" value="myForm"
                  name="<?php echo ini_get("session.upload_progress.name"); ?>">

                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="material-icons">face</i>
                    </span>
                    <input type="text" class="form-control" placeholder="project_name" id="project" name = "project">
                  </div>



                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="material-icons">attach_file</i>
                    </span>
                    <input type="file" id="userfile" class = "form_control" name="userfile" />
                  </div>
                  <div class = "input-group">
                    <span class = "input-group-addon">
                      <input type="hidden" id="process" class = "form_control" name="process" value="0">
                      <div class = "text-center">
                        <input type="submit" class="btn btn-simple btn-primary btn-lg" style="margin-bottom :1px;" value="Start Upload">
                        <br/>

                      </div>
                    </span>
                  </div>
                </form>
                <iframe id="hidden_iframe" name="hidden_iframe" src="about:blank"></iframe>


                <!-- load project form -->
                <form id="load_project_form" name="load_project_form" action="load_project.php" method="POST">
                  <div class = "text-center">
                  <input type="hidden" id="load_proj_name" name="load_proj_name">
                  <input type="button" class="btn btn-simple btn-primary btn-lg" value="Load Project" style="margin-top:1px; padding-top:1px" onclick="load_project()">
                </div>
                </form>

                <!-- If you want to add a checkbox to this form, uncomment this code

                <div class="checkbox">
                <label>
                <input type="checkbox" name="optionsCheckboxes" checked>
                Subscribe to newsletter
              </label>
            </div> -->
          </div>

        </div>
      </div>
    </div>
  </div>


</div>

</div>





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
<script type="text/javascript" src="upload.js"></script>
<script type="text/javascript" src="load_project.js"></script>

</body>
</html>
