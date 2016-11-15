
<?php
session_start();


$column_list = $_SESSION['column_list'];
$column_size = $_SESSION['column_size'];

$_SESSION['process'] = 1;
?>

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
  <style type="text/css">
  #card-signup .content {
    padding: 0px 10px 0px 10px;
  }
  </style>

  <script type="text/javascript">
  var key_num = 0;
  var key_attribute="";

  //dynamically change selects
  function change_key_num(value){
    key_num = value;
    show_key_select();
  }

  function change_key_attribute(value){
    key_attribute = value;
    show_key_select();
  }

  function show_key_select(){
    var form = document.getElementById('key_select');
    form.innerHTML = "";

    var column_list = <?php echo json_encode($column_list); ?>;
    var column_size = <?php echo $column_size; ?>;

    //key has 3 attribute
    if (key_attribute == 'latitude_longitude_time' || key_attribute == 'x_y_t'){
      for (var i = 0 ; i < key_num ; i ++){
        for (var j = 0 ; j < 3 ; j++){
          var selectList = document.createElement("select");
          selectList.name = 'key['.concat(i.toString(),'][',j.toString(),']');
          form.appendChild(selectList);
          for (var k = 0 ; k < column_size ; k++) {
            var option = document.createElement("option");
            option.value = k;
            option.text = column_list[k];
            selectList.appendChild(option);
          }

        }
        form.appendChild(document.createElement("br"));
      }
    }
    //key has 2 attribute
    else if ( key_attribute == 'latitude_longitude' || key_attribute == 'x_y' ){
      for (var i = 0 ; i < key_num ; i ++){
        for (var j = 0 ; j < 2 ; j++){
          var selectList = document.createElement("select");
          selectList.name = 'key['.concat(i.toString(),'][',j.toString(),']');
          form.appendChild(selectList);//move file to hdfs
          for (var k = 0 ; k < column_size ; k++) {
            var option = document.createElement("option");
            option.value = k;
            option.text = column_list[k];
            selectList.appendChild(option);
          }

        }
        form.appendChild(document.createElement("br"));
      }
    }
  }


  //key_num select



  </script>



</head>
<body class="signup-page">
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
          <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3" style="width:100%; margin-right:auto; margin-left:auto;">
            <div class="card card-signup center-block" id = "card-signup">

              <div class="header header-primary text-center">
                <h4>Select Columns</h4>

              </div>

              <div class="content" style = "padding-left : auto ; padding-right : auto">
                <form name="key_select" action="save_column.php" method="get" enctype="multipart/form-data">

                  <div id="column_set" class = "text-center center-block">
                    <div class = "panel panel-default center-block" >


                  <div class = "panel-heading">
                    Please select the number of key in each line<br />
                    </div>
                    <div class = "panel-body">
                    <select id="key_num" name = "key_num" onchange="change_key_num(this.value)">

                    </select>
                    </div>
                    <br />
                    </div>
                    <div class = "panel panel-default center-block">
                     <div class = "panel-heading">
                    Please select the number of attribute in each key.<br />
                    </div>
                    <div class = "panel-body">


                    <select name = "attribute" onchange="change_key_attribute(this.value)">
                      <option value="none"> select type... </option>
                      <option value="latitude_longitude"> latitude, longitude</option>
                      <option value="latitude_longitude_time"> latitude, longitude, time</option>
                      <option value="x_y_t"> x, y, t</option>
                      <option value="x_y"> x, y </option>
                    </select>
                    <br />
                    <br />

                  </div>
                  </div>
                  </div>
                  <div id="key_select" class = "text-center center-block">



                  </div>
                  <div class = "text-center center-block">
                  <input class = "btn btn-simple btn-primary btn-lg" type="submit" value="Complete"></input>
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
  (function () {
    var selectList = document.getElementById("key_num");
    for (var i = 0; i < <?php echo $column_size ?> /2 ;i++){
      var option = document.createElement("option");
      option.value = i;
      option.text = i;
      selectList.appendChild(option);
    }

  })();
  </script>

</body>
</html>
