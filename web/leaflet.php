<!DOCTYPE html>
<html>
<head>
	<title>Leaflet Test</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css"/>
	<style>
		button{
			color: black;
			width: 10vw;
			height: 10vh;
			background: #CCEEFF;
			border-radius: 1vw;
			font-size: 2vw;
		}
		body{
			padding: 0;
			margin: 0;
		}
		html, body {
			height: 100vh;
			width: 100vw;
		}
		#mapid {
			height: 99vh;
			width: 99vw;
		}
		.legend {
			padding: 1vw 1vh;
			font: 1vw Arial, Helvetica, sans-serif;
			background: white;
			background: rgba(255,255,255,0.8);
			box-shadow: 0 0 3px rgba(0,0,0,0.2);
			border-radius: 1vh;
			text-align: left;
			line-height: 3vh;
			color: #555;
		}
		.legend i {
			text-align: left;
			width: 3vw;
			height: 3vh;
			float: left;
			margin-right: 1vh;
			opacity: 0.7;
		}
	</style>
</head>
<body>
	<div id="mapid"></div><br />
	<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
	<script>
		var mainLand = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {maxZoom: 18, attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' + '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' + 'Imagery © <a href="http://mapbox.com">Mapbox</a>', id: 'mapbox.streets'});

		var mymap = L.map('mapid', {
			center: [40.75, -73.99],
			zoom: 13,
			layers: [mainLand]
		}).fitWorld();
		
		var backButton = L.control({position: 'bottomleft'});
		backButton.onAdd = function(map){
			var back = L.DomUtil.create('div','backbutton');
			back.innerHTML = '<button onclick=' + 'window.location.href=' + '"./menu.php">To Main</button>';
			return back;
		}
		backButton.addTo(mymap);

		var degreeStep;
		var hotspotNum;
		var timeStep;
		<?php
		   session_start();
		   $fp = fopen($_SESSION['project_dir']."result_hotspot.csv","r");
		   $first_line = fgets($fp);
		   $tokenFirst = explode(",",$first_line);
		   echo("degreeStep = ".$tokenFirst[1].";");
		   echo("hotspotNum = ".$tokenFirst[0].";");
		   echo("timeStep = ".$tokenFirst[2].";");
		   echo("var layerTmp = [];");
		   for($i = 0; $i < $tokenFirst[0]; $i++){
			$readLine = fgets($fp);
			$tokenLine = explode(",",$readLine);
			echo ("layerTmp.push([".($tokenLine[0] * $tokenFirst[1]).", ".($tokenLine[1] * $tokenFirst[1]).", ".$tokenLine[2].", ".($i + 1)."]);");
		   }
		   fclose($fp);
		?>
		
		//mymap.setView([layerTmp[0][0],layerTmp[0][1]],14);
		layerTmp.sort(function(a,b){return a[2] - b[2]});
		mymap.setView([layerTmp[0][0],layerTmp[0][1]],14);
		var layerTimeStamp = layerTmp[0][2];
		var tmpGroup = [];
		var groupsID = [layerTimeStamp];
		var groups = [];
		for (var i = 0; i < hotspotNum; i++) {
			if(layerTimeStamp != layerTmp[i][2]){
				groups.push(tmpGroup);
				tmpGroup = [layerTmp[i]];
				layerTimeStamp = layerTmp[i][2];
				groupsID.push(layerTimeStamp);
			}
			else{
				tmpGroup.push(layerTmp[i]);
			}
		}
		groups.push(tmpGroup);
		var layerGroups = [];
		for (var i = 0; i < groups.length; i++){
			groups[i].sort(function(a,b){return a[3] - b[3]});
			var tmpLayerGroup = L.layerGroup();
			for (var j = 0; j < groups[i].length; j++){
				if(j == 0){
					tmpLayerGroup.addLayer(L.polygon([[groups[i][j][0],groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1] + degreeStep],[groups[i][j][0],groups[i][j][1] + degreeStep]], {color: 'red', weight: 1, fillOpacity: (groups[i].length - j) / groups[i].length}).bindPopup('Rank: ' + groups[i][j][3] + '\nlatitude: ' + groups[i][j][0] + '\nlongitude: ' + (groups[i][j][1])));
				}
				else if (j < groups[i].length / 10){
					tmpLayerGroup.addLayer(L.polygon([[groups[i][j][0],groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1] + degreeStep],[groups[i][j][0],groups[i][j][1] + degreeStep]], {color: '#ff4000', weight: 1, fillOpacity: (4*(groups[i].length - j)/5) / groups[i].length}).bindPopup('Rank: ' + groups[i][j][3] + '\nlatitude: ' + groups[i][j][0] + '\nlongitude: ' + (groups[i][j][1])));
				}
				else if (j < groups[i].length / 4){
					tmpLayerGroup.addLayer(L.polygon([[groups[i][j][0],groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1] + degreeStep],[groups[i][j][0],groups[i][j][1] + degreeStep]], {color: '#ff8000', weight: 1, fillOpacity: (4*(groups[i].length - j)/5) / groups[i].length}).bindPopup('Rank: ' + groups[i][j][3] + '\nlatitude: ' + groups[i][j][0] + '\nlongitude: ' + (groups[i][j][1])));
				}
				else if (j < groups[i].length / 2){
					tmpLayerGroup.addLayer(L.polygon([[groups[i][j][0],groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1] + degreeStep],[groups[i][j][0],groups[i][j][1] + degreeStep]], {color: '#ffbf00', weight: 1, fillOpacity: (4*(groups[i].length - j)/5) / groups[i].length}).bindPopup('Rank: ' + groups[i][j][3] + '\nlatitude: ' + groups[i][j][0] + '\nlongitude: ' + (groups[i][j][1])));
				}
				else{
					tmpLayerGroup.addLayer(L.polygon([[groups[i][j][0],groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1]],[groups[i][j][0] + degreeStep,groups[i][j][1] + degreeStep],[groups[i][j][0],groups[i][j][1] + degreeStep]], {color: 'yellow', weight: 1, fillOpacity: (4*(groups[i].length - j)/5) / groups[i].length}).bindPopup('Rank: ' + groups[i][j][3] + '\nlatitude: ' + groups[i][j][0] + '\nlongitude: ' + (groups[i][j][1])));
				}
			}
			layerGroups.push(tmpLayerGroup);
		}
		
		var baseMap = {"Main": mainLand};
		var overlayMaps = new Object();
		if (timeStep == 0){
			layerGroups[0].addTo(mymap);
		}
		else{
			for(var i = 0; i < layerGroups.length; i++){
				overlayMaps[groupsID[i]] = layerGroups[i];
			}

			L.control.layers(overlayMaps).addTo(mymap);
		}
		var legend = L.control({position: 'bottomright'});
		legend.onAdd = function(map){
			var div = L.DomUtil.create('div', 'info legend');
				div.innerHTML += '<p><i style="background: red"></i>first</p>'; 
				div.innerHTML += '<p><i style="background: #ff4000"></i>' + 0 + '%&ndash;' + 10 + '%</p>';
				div.innerHTML += '<p><i style="background: #ff8000"></i>' + 10 + '%&ndash;' + 25 + '%</p>'; 
				div.innerHTML += '<p><i style="background: #ffbf00"></i>' + 25 + '%&ndash;' + 50 + '%</p>'; 
				div.innerHTML += '<p><i style="background: yellow"></i>' + 50 + '%&ndash;' + 100 + '%</p>'; 
			return div;
		}
		legend.addTo(mymap);
	
	</script>

</body>
</html>
