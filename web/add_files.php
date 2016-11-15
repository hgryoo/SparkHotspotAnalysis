
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Material thema ------------------------>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="x_material_kit/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="x_material_kit/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />



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

<body>
  <div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES["userfile"])) {
      session_start();

      require('proj_function.php');
      if ($_POST['process']==3){

        $_SESSION['process'] = 3;

        //no file select
        if (!is_uploaded_file($_FILES['userfile']['tmp_name'])){
          echo '<script language="javascript">';
          echo 'self.alert("file null!");';
          echo '</script>';
          $_SESSION['process'] = -2;
          exit();
        }

        $target_file = file_upload($_FILES['userfile'], $_SESSION['project_dir']);

        //if upload something wrong
        if ($target_file==null){
          $_SESSION['process'] = -2;
          exit();
        }
        ini_set('auto_detect_line_endings',TRUE);
        //check it has same column
        $handle = fopen("$target_file", "r");
        if ($handle) {
          $buffer = fgetcsv($handle);
          $column_size = count($buffer);

          if ($column_size != $_SESSION['column_size']){
            echo '<script language="javascript">self.alert("column is not same!");</script>';
            $_SESSION['process'] = -2;
            shell_exec("rm $target_file");
            exit();
          }

        }
        fclose($handle);
        ini_set('auto_detect_line_endings',FALSE);
        //change target_file session var.
	for ($i = 0 ; $i < $_SESSION['file_number'] ;$i++){
		if (basename($target_file) == $_SESSION['file_list'][$i])
		{
			shell_exec("rm $target_file");
			$_SESSION['process'] = -2;
			exit();
		}
	}
        $_SESSION['target_file'] = $target_file;
        array_push($_SESSION['file_list'],basename($target_file));
        $_SESSION['file_number'] += 1;
	$_SESSION['process'] = 102;
      }
      else{
        //maybe not
      }
    }

    ?>

  </div>
  <br />

<div class = "card card-signup">
   <div class = "content">
     <div class="header header-primary text-center">
	  	    <h4>Add More Files</h4>
		    </div>
      <div class = "panel panel-default center-block" >
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST"
  id="myForm" enctype="multipart/form-data" target="hidden_iframe">
    <div id="bar_blank" class = "progress center-block">

      <div id="bar_color" class = "progress-bar" role="progressbar">
      </div>
      </div>
      <div id="status"></div>




      <input type="hidden" value="myForm"
          name="<?php echo ini_get("session.upload_progress.name"); ?>">
    <div class = "input-group center-block" style="width:200px;">
      <span class = "input-group-addon">
        <i class = "material-icons">attach_file</i>
       </span>
     <input type="file" id="userfile" name="userfile" class = "form_control">

    </div>
    </br>
  </div>
    <input type="hidden" id="process" name="process" value="3">
    <div class = "text-center">
      <input type="submit" class="btn btn-simple btn-primary btn-lg" value="Start Upload">

   </div>


 </form>
		    
     
     



     
 <iframe id="hidden_iframe" name="hidden_iframe" src="about:blank"></iframe>
 <script type="text/javascript" src="upload.js"></script>
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



</body>
</html>
