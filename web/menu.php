<?php
session_start();
//print_r($_SESSION);
shell_exec("rm ".$_SESSION['project_dir']."session.txt 2>&1" );
$session_file = fopen($_SESSION['project_dir']."session.txt","w");
fwrite($session_file, $_SESSION['column_size']."\n");
fwrite($session_file, $_SESSION['file_number']."\n");
for ($i = 0 ; $i < $_SESSION['file_number'] ; $i++){
  fwrite($session_file, $_SESSION['file_list'][$i]."\n");
}
fwrite($session_file, $_SESSION['process']."\n");
fwrite($session_file, $_SESSION['project_dir']."\n");
fwrite($session_file, $_SESSION['project_name']."\n");
fwrite($session_file, $_SESSION['type']."\n");
fclose($session_file);
chmod($_SESSION['project_dir']."session.txt",0777);

$file_number = $_SESSION['file_number'];
?>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content= "width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Spark HotSpot Analysis by ABC </title>

  <!-- Bootstrap Core CSS -->
  <link href="startbootstrap-agency-gh-pages/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Fonts -->
  <link href="startbootstrap-agency-gh-pages/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
  <link href='https://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>



  <!-- Theme CSS -->
  <link href="startbootstrap-agency-gh-pages/css/agency.min.css" rel="stylesheet">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->


  <style>
  #home_back {
  
    background-image: url('./background_edit2.png');
    background-size : cover;
    height: inherit;
    overflow: auto;
  }

  /*loading icon*/
  #spark_load_item, #hdfs_load_item {
    margin:5px 5px 5px;
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACWUlEQVQ4y21TS2saURR28qBQ2gZSaCniVhDc2H0XFQOGQP6AP0B0IzTVRVIKFmsMLlxEQ3ZBRDSpUkUqEkMVRTc+FiKi8UEMEVFUhL4mrfHk3GFumEwzcOY+z3e/893vSgBAQuLk5GSR9vFbKBaLy3QsCkY4Fie/wliji+PxeG0wGFixvyQCWbgDSKVS3GKpVNIeHBywu7u7EIlEznBOm8lkwOPxYBeeCZKfCpnQSTUmE+4k/qnVami32387nY6zXC43cH2FZxr1+/2z6XS6RplzAKPRyG02m0nyH4VCMcd2lkgkrnnwR3z7xOVyzWw2GwyHQzuZIzpRBm/j8TgBIMmg1+vh/Pz8m0AwruZaraZpNBp2QTlL5LfcbDa/BINBsFgsNz6f77pSqUQfEOxOfZZlN2az2SanweXl5Q6hhSezKpUKTk9P0w+pTW+qXq9rrVYrOBwO6Ha7Gkk0Gv2kVCqBYRiW0N/f3yeqvxSfSn2Bpe0ZjUbY2toCPPyzZDKZbKOy3A2sr69DoVA4E5mGERstnU5v53K5D9h/TCZWrq6u3sVisfzx8TGg+gMCKlD/zisYq1j/+/+cyG/qyOVyjgkKCQKAVb7VBAKBn6T2fD4fJ8xwL0MBXrjdbpJ8Q8Q0mUwwn8/DoVDoazKZhIuLi9f9fn/PYDBwXvF6veSA5/cYVKvVmN1uB51OB2jrX71eD6RSKcfo8PBwgHs20Ss/nE4n4N6QsASq9CI+nI+tVmuHeB+1+C6TyTiAo6Oj35Sp8LFxZTz0RPl4k81mIRwOk3excU84rJ3m3AINH73kCxNs+wAAAABJRU5ErkJggg==);
    height: 16px;

    width: 16px;
    z-index: 100;
  }


  .hide{
    display: none;
  }
  </style>
  <meta charset="utf-8">
  <script type="text/javascript" src="menu.js">



  </script>

</head>
<body id="page-top" class="index" onload="check_status()" style="height: 100vh;width: 100vw;">


  <!-- its show project name-->
  <div >
  </div>
  <br />


  <!-- Navigation -->
  <nav id="mainNav" class="navbar navbar-default navbar-custom navbar-fixed-top">
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header page-scroll">

        <a class="navbar-brand page-scroll" href="#page-top">HotSpot with Spark - Make your own work!</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->

      <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
  </nav>

  <!-- Header -->
  <header id = "home_back">
    <div class="container">
      <div class="intro-text" style="padding-top:100px; padding-bottom:50px; ">
        <div id="hidden_from_container" style="display:none;"></div>

        <div class="intro-heading" style = "color : #000000">MENU</div>
         
        <div class="intro-lead-in" style = "color : #000000"> <?php echo json_encode($_SESSION['project_name']); ?></div>
        <!-- Button trigger modal -->
        <div class="row">

<div class = "col-md-4">
        <button class="btn btn-primary btn-xl" style="width:200px" data-toggle="modal" data-target="#myModal">
          Add More Files
        </button>
</div>
        <!-- Modal Core -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
              </div>
              <div class="modal-body">
                <?php
                require('add_files.php');
                ?>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default btn-simple" data-dismiss="modal">Close</button>

              </div>
            </div>
          </div>
        </div>
        <!-- show hdfs_uploading-->



         <div class = "col-md-4">

        <button type="button" onclick="check_available_submit()" style="width:200px" class="btn btn-primary" >Spark Submit </button>


                 </div>






         <div class = "col-md-4">
        <button type="button" onclick="check_available_leaflet()" style="width:200px" class="btn btn-primary" value="View Leaflet" >View Leaflet </button>
      </div>


       </div>
<div class = "row">
 <div class = "col-md-4">

        <button type="button" value="Get Result file" style="width:200px" class="btn btn-primary" onclick="get_result('<?php echo $_SESSION['project_dir']?>result_hotspot.csv')">Get Result file</button>
         </div>


<div class = "col-md-4">

        <button type="button" value="Delete Project" style = "width : 200px" class="btn btn-primary" onclick="delete_project()">Delete Project</button>
         </div>      

<div class = "col-md-4">

        <button type="button" value="return home" style = "width : 200px" class="btn btn-primary" onclick="return_home()">RETURN HOME</button>
         </div>  
	<iframe id="my_ifram" style="display:none;"></iframe>
</div>
    
</div>
   
   <div class = "row ">
              <div class = "panel panel-default" style="padding:0 0 0px; background-color:rgba(50,50,50,0.7);">
          <div class = "panel-heading">
             Uploaded Files
          </div>
          <div class = "panel-body">
            
            <div id="file_list"></div>

               </div>
               <div id="hdfs_status" style="color:black"> </div>

               <div id="hdfs_load_item" style="display:none"></div>

          </div>

         
               <!-- show spark submitting-->

      </div>

    <div class = "row">
	<div class = "col-md-6">

      <div id="spark_status" style = "color : black"></div></div>
<div class ="col-md-6">
      <div id="spark_load_item" style="display:none"></div></div>
        </div>
    </div>
   </div>
</div></div>

</div>

  </header>


  <!-- jQuery -->
  <script src="startbootstrap-agency-gh-pages/vendor/jquery/jquery.min.js"></script>

  <!-- Bootstrap Core JavaScript -->
  <script src="startbootstrap-agency-gh-pages/vendor/bootstrap/js/bootstrap.min.js"></script>

  <!-- Plugin JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

  <!-- Contact Form JavaScript -->
  <script src="startbootstrap-agency-gh-pages/js/jqBootstrapValidation.js"></script>
  <script src="startbootstrap-agency-gh-pages/js/contact_me.js"></script>

  <!-- Theme JavaScript -->
  <script src="startbootstrap-agency-gh-pages/js/agency.min.js"></script>



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
  <script type="text/javascript">

  function show_uploaded_files(){
    var fileList = document.getElementById("file_list");
    fileList.innerHTML="";
    var phpArray = <?php echo json_encode($_SESSION['file_list']); ?>;

    for (var i = 0; i < <?php echo $file_number ?>  ;i++){


      fileList.innerHTML += phpArray[i]+"<br />";

    }

  }

  show_uploaded_files();
  </script>
</body>
</html>
