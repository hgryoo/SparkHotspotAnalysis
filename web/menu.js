
var server_is_working = true;
var timeout;
var hdfs=false;
var spark=false;
//check hdfs_upload and spark_sumit is working
function check_status(){

  var http = createRequestObject();
  http.open("GET", "check_status.php");
  http.onreadystatechange = function () { handleResponse_status(http); };
  http.send(null);
}

function return_home(){
  location.replace('home.php'); 
}

function get_result(url){
 if (!server_is_working){
	 document.getElementById('my_ifram').src = url;
  }
else{

    alert("Not Yet");
}

};
//check in one second
function handleResponse_status(http) {
  clearTimeout(timeout);
  var response;
  if (http.readyState == 4) {
    response = http.responseText; //response has hdfs, spark status.
    var list = response.split(" ");
    server_is_working = false;

    if (list[0] == 0) {
      document.getElementById("hdfs_status").style.display="none";
      if (hdfs){
        hdfs_loading("stop");
        hdfs=false;
      }

    }
    else{
      server_is_working = true;
      document.getElementById("hdfs_status").style.display="block";
      document.getElementById("hdfs_status").innerHTML = "hdfs uploading.. the number of remain files : " +list[0].toString();
      if (!hdfs)
        hdfs_loading("start");
      hdfs = true;
    }
    if (list[1] == 0){
      document.getElementById("spark_status").style.display="none";
      if (spark)
        spark_loading("stop");
        spark =false;
    }
    else {
      server_is_working = true;
      document.getElementById("spark_status").style.display="block";
      document.getElementById("spark_status").innerHTML = "spark working..";
      if (!spark)
        spark_loading("start");
        spark = true;
    }
    timeout = setTimeout("check_status()", 1000);

  }
}

function check_available_submit(){
  if (server_is_working){
    alert("We are Still uploading files Or already spark_submit is submitted");
  }else{
    clearTimeout(timeout);

    location.href='submit.php';
  }

}

function check_available_leaflet(){
  if (server_is_working){
    alert("We are Still uploading files Or already spark_submit is submitted");
  }else{
    clearTimeout(timeout);

    location.href='leaflet.php';

  }

}
//..
function createRequestObject() {
  var http;
  if (navigator.appName == "Microsoft Internet Explorer") {
    http = new ActiveXObject("Microsoft.XMLHTTP");
  }
  else {
    http = new XMLHttpRequest();
  }
  return http;
}

//loding icon function
var spark_timer;
function spark_loading(mode){
  var loading = document.getElementById("spark_load_item"),angle = 0, offsetLeft = 0, offsetTop = 0;
  if (mode == "start"){

    loading.style.display="block";
    rotate();
    spark_timer = window.setInterval( function () {

      rotate();

    }, 100 );

    function rotate() {

      angle = ( angle == 360 ? 45 : angle + 45 );
      loading.style.webkitTransform = 'rotate(' + angle + 'deg)';

    };
  }
  else{
    window.clearInterval( spark_timer );
    loading.style.display="none";
  }


}

//loding icon function -hdfs
var hdfs_timer;
function hdfs_loading(mode){
  var loading = document.getElementById("hdfs_load_item"),angle = 0, offsetLeft = 0, offsetTop = 0;
  if (mode == "start"){

    loading.style.display="block";
    rotate();

    hdfs_timer = window.setInterval( function () {
      rotate();
    }, 100 );

    function rotate() {
      angle = ( angle == 360 ? 45 : angle + 45 );
      loading.style.webkitTransform = 'rotate(' + angle + 'deg)';
    };
  }
  else{
    window.clearInterval( hdfs_timer );
    loading.style.display="none";
  }


}


function delete_project(){
  if (server_is_working){
    alert("We are Still uploading files Or already spark_submit is submitted");
  }else{
    clearTimeout(timeout);

    location.replace('delete_project.php');

  }
}
