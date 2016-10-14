<?php
session_start();



?>


<html>
<body>
  <?php

  $column_size =  $_SESSION["column_size"];
  //echo $column_size;

  $max_pair_size = $column_size/2;
  if ($_SESSION['process']==0){
    echo '<form method = "post" action = "pick_column.php">';
    echo 'Please select the number of key in each line<br />';

    echo '<select name = "key_num">';

    for($count = 1 ; $count < $max_pair_size; $count++){

      echo '<option value="'.$count.'">'.$count.'</option>';
    }


    echo '</select>';
    echo '<br />';
    echo 'Please select the number of attribute in each key.<br />';
    echo 'Yet y, x, time is available Only <br />';

    echo '<select name = "attribute">';

    echo '<option value="x_y"> y, x</option>';
    echo '<option value="y_x_time"> y, x, time</option>';
    echo '<option value="x_y_z"> y, x, z</option>';

    echo '</select>';
    echo '<br />';
    echo '<br />';
    echo '<input type="submit" name = "submit" value="submit">';
    $_SESSION['process'] = 1;

    echo '</form>';
  }

//  $selected_att_num = 0;
  else if($_SESSION['process']==1  &&  $_POST['attribute'] == "y_x_time"){
    $selected_key_num = $_POST['key_num'];
    $selected_att_type = $_POST['attribute'];
    $column_list = $_SESSION["column_list"];


    echo '<form method = "post" action = "pick_column.php">';

    for($count = 0 ; $count < $selected_key_num ; $count++){
      for($count2 = 0 ; $count2 < 3 ; $count2++){

        echo '<select name = "key_'.$count.'att_'.$count2.'">';
        for($count3 = 0 ; $count3 < $column_size ; $count3++){


          echo '<option value = "'.$count3.'">'.$column_list[$count3].'</option>';
        }
        echo '</select>';
      }
      echo '<br /><br />';
    }
    echo '<input type="submit" name = "spark_submit" value="submit_column">';
    echo '</form>';
    $_SESSION['key_num']=$selected_key_num;
    $_SESSION['process']=2;

  }
  else if ($_SESSION['process']==3){
    $mkdir_output = shell_exec("/usr/local/hadoop/bin/hdfs dfs -mkdir -p /".$_SESSION['user']."/".$_SESSION['pro_name']." 2>&1");
    $put_output = shell_exec("/usr/local/hadoop/bin/hdfs dfs -moveFromLocal "
    .$_SESSION['target_file']." /".$_SESSION['user']
    ."/".$_SESSION['pro_name']."/ 2>&1");


  //  echo $mkdir_output."<br/>";
  //  echo $put_output;

    echo("<script>location.replace('main.html');</script>");
  }
  else if ($_SESSION['process']==2){
    $column_file = fopen($_SESSION['target_dir']."column.txt", "w");

    //fwrite($column_file, (string)$selected_key_num."\n");
  //  fwrite($column_file, (string)$selected_att_num."\n");
    for ($key = 0 ; $key < $_SESSION['key_num']; $key++){
      fwrite($column_file, (string)$_POST['key_'.$key.'att_0']." ".(string)$_POST['key_'.$key.'att_1']." ".(string)$_POST['key_'.$key.'att_2']);
	if ($key != $_SESSION['key_num']-1 )
	      fwrite($column_file,"\n");

    }
    fclose($column_file);
    chmod($_SESSION['target_dir']."column.txt",0777);

    $_SESSION['process'] = 3;
    echo("<script>location.replace('pick_column.php');</script>");
  }
  else{
    $_SESSION['process'] = 0;
  }


  ?>
</body>
</html>
