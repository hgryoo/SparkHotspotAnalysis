var send_function;

function toggleBarVisibility() {
  var e = document.getElementById("bar_blank");
  e.style.display = (e.style.display == "block") ? "none" : "block";
}

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

function sendRequest() {
  var http = createRequestObject();
  http.open("GET", "progress.php");
  http.onreadystatechange = function () { handleResponse(http); };
  http.send(null);
}

function handleResponse(http) {
  var response;
  if (http.readyState == 4) {
    response = http.responseText;
    console.log(response);
    //error
    if (response < 0){
      toggleBarVisibility();
      document.getElementById('userfile').value="";
	document.getElementById("status").innerHTML="";
    }
    else{
      document.getElementById("bar_color").style.width = response + "%";
      document.getElementById("status").innerHTML = response + "%";
      if (response <= 100) {
        send_function = setTimeout("sendRequest()", 1000);
      }
      else {

        toggleBarVisibility();
        document.getElementById("status").innerHTML = "Done.";
	if (response > 100){
	        if (response == 101)
       	 	{
       		   location.replace('pick_column.php');
        	}
        	else
        	{
          	   location.replace('put_hdfs.php');

       		}	
	}
      }
    }

  }
}


function startUpload() {
  if (document.getElementById("process").value == "0")
  {
    var name = document.getElementById("project").value;
    if (document.getElementById("userfile").files.length == 0){
      alert("no file select");
    }
    else if (name == null || name =="" || name=="project_name"){
      alert("no project name");
    }
    else{
      toggleBarVisibility();
      send_function = setTimeout("sendRequest()", 1000);
    }
  }
  else
  {
    //document.getElementById("file_list").innerHTML += document.getElementById("userfile").value + "<br />";
    if (document.getElementById("userfile").files.length == 0){
      alert("no file select");
    }
    else{
      toggleBarVisibility();
      send_function = setTimeout("sendRequest()", 1000);
    }
  }

}

(function () {
  document.getElementById("myForm").onsubmit = startUpload;
})();
