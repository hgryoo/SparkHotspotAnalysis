<!DOCTYPE html>
<html>
<head>
	<title>Leaflet Test</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css"/>
</head>
<body>
	<div id="mapid" style="width: 1800px; height: 900px"></div>
	<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
	<script>
		var mymap = L.map('mapid').setView([40.75, -73.99], 13);

		L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {maxZoom: 18, attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' + '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' + 'Imagery © <a href="http://mapbox.com">Mapbox</a>', id: 'mapbox.streets'}).addTo(mymap);

		<?php
		   session_start();

		   $fp = fopen($_SESSION['target_dir']."result_hotspot.csv","r");
		   $first_line = fgets($fp);
		   $tokenFirst = explode(",",$first_line);
		   for($i = 0; $i < $tokenFirst[0]; $i++){
			$readLine = fgets($fp);
			$tokenLine = explode(",",$readLine);
			echo ("L.circle([".($tokenLine[0] * $tokenFirst[1] + $tokenFirst[1] * 0.5).", ".($tokenLine[1] * $tokenFirst[1] + $tokenFirst[1] * 0.5)."], ".($tokenFirst[1] * 50000).", {color: 'red'}).bindPopup('latitude: '+".($tokenLine[0] * $tokenFirst[1] + $tokenFirst[1] * 0.5)."+' longitude: '+".($tokenLine[1] * $tokenFirst[1] + $tokenFirst[1] * 0.5)."+' timestep: '+".$tokenLine[2].").addTo(mymap);\n");
		   }
		   fclose($fp);
		?>
	</script>
</body>
</html>
