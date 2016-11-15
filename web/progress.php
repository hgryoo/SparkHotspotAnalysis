<?php
session_start();
if ($_SESSION['process'] < 0){
  echo $_SESSION['process'];
}else{
  $key = ini_get("session.upload_progress.prefix") . "myForm";
  if (!empty($_SESSION[$key])) {
      $current = $_SESSION[$key]["bytes_processed"];
      $total = $_SESSION[$key]["content_length"];
      echo $current < $total ? ceil($current / $total * 100) : 100;
  }
  else {
	if ($_SESSION['process'] == 101){ echo 101;}
	else if ($_SESSION['process'] == 102){
		 echo 102;
		$_SESSION['process'] = 3;
	}
	else    {  echo 100;}
  	}

}

?>
