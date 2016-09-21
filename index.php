<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="convertj.js"></script>
	<meta charset="UTF-8">
	<title>Title</title>
</head>
<body>
<h3></h3>
<div id="map"></div>
<div id="output">waiting for action</div>
<button onclick="addOP()">Add OPs</button>
<button onclick="addTargets()">Add Targets</button>
<button onclick="addWaypoints()">Add waypoints</button>
<br>
From:
<input id="from">
<br>
To:
<input id="to">
<button onclick="genMission()">Generate Fire Mission</button>
<div id="grid"></div>
<div id="mission" class="hidden-sm-up" style="position:absolute;left:60%;margin-left:-250px;top:30%; background-color:white;">
	OBLOC: <span id="obloc"></span>
	Distance: <span id="distance"></span>
	<br>
	FDC this is FO, adjust fire, over.
	<br>
	Grid: <span id="gridFM"></span>, elevation:____, direction: <span id="direction"></span>, over.
	<br>
	__________________________________, over.
	<br>
	<button onclick="($('#mission').addClass('hidden-sm-up'))">Close</button>
</div>
<script>

	var chicago = {lat: 41.85, lng: -87.65};
	var opImg = './img/op.png';
	var targetImg = './img/target.png';
	var waypointImg = './img/waypoint.png';

	var opArray = [];
	var targetArray = [];
	var waypointArray = [];

	var opArrayLL = [];
	var targetArrayLL = [];
	var waypointArrayLL = [];


	function addOP(){
		action = 'op';
		document.getElementById('output').innerHTML = "Adding OPs";
	}

	function addTargets(){
		action = 'target';
		document.getElementById('output').innerHTML = "Adding targets";
	}

	function addWaypoints(){
		action = 'waypoint';
		document.getElementById('output').innerHTML = "Adding waypoints";
	}


	function CenterControl(controlDiv, map, center) {
		var control = this;
		control.center_ = center;
		controlDiv.style.clear = 'both';

		// Set CSS for the control border
		var goCenterUI = document.createElement('div');
		goCenterUI.id = 'goCenterUI';
		goCenterUI.title = 'Click to recenter the map';
		controlDiv.appendChild(goCenterUI);

		// Set CSS for the control interior
		var goCenterText = document.createElement('div');
		goCenterText.id = 'goCenterText';
		goCenterText.innerHTML = 'Present Pos';
		goCenterUI.appendChild(goCenterText);

		goCenterUI.addEventListener('click', function() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					var pos = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};



					//infoWindow.setPosition(pos);
					//infoWindow.setContent('Location found.');
					var marker = new google.maps.Marker({
						position: pos,
						label: "",
						map: map
					});


					map.setCenter(pos);


				}, function() {
					handleLocationError(true, infoWindow, map.getCenter());
				});
			} else {
				// Browser doesn't support Geolocation
				handleLocationError(false, infoWindow, map.getCenter());
			}
		});


	}


	CenterControl.prototype.center_ = null;

	CenterControl.prototype.getCenter = function() {
		return this.center_;
	};

	CenterControl.prototype.setCenter = function(center) {
		this.center_ = center;
	};



	function distance(lat1, lon1, lat2, lon2) {
		var R = 6371; // Radius of the earth in km
		var dLat = (lat2 - lat1) * Math.PI / 180;  // deg2rad below
		var dLon = (lon2 - lon1) * Math.PI / 180;
		var a =
			0.5 - Math.cos(dLat)/2 +
			Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
			(1 - Math.cos(dLon))/2;

		return R * 2 * Math.asin(Math.sqrt(a));
	}


	function radians(n) {
		return n * (Math.PI / 180);
	}
	function degrees(n) {
		return n * (180 / Math.PI);
	}

	function getBearing(startLat,startLong,endLat,endLong){
		startLat = radians(startLat);
		startLong = radians(startLong);
		endLat = radians(endLat);
		endLong = radians(endLong);

		var dLong = endLong - startLong;

		var dPhi = Math.log(Math.tan(endLat/2.0+Math.PI/4.0)/Math.tan(startLat/2.0+Math.PI/4.0));
		if (Math.abs(dLong) > Math.PI){
			if (dLong > 0.0)
				dLong = -(2.0 * Math.PI - dLong);
			else
				dLong = (2.0 * Math.PI + dLong);
		}

		return (degrees(Math.atan2(dLong, dPhi)) + 360.0) % 360.0;
	}






	function genMission(){
		var from1 = parseInt($("#from").val());
		var to1 = parseInt($("#to").val());

		var grid1 = opArray[from1];
		var grid2 = targetArray[to1];

		var p1 = opArrayLL[from1];
		var p2 = targetArrayLL[to1];
		var d = distance(p1[0],p1[1],p2[0],p2[1]).toFixed(2);
		var dir = getBearing(p1[0],p1[1],p2[0],p2[1]).toFixed(0)

		$("#distance").html(d + " km");
		$("#direction").html(dir);
		$("#obloc").html(grid1);
		$("#gridFM").html(grid2);
		$("#mission").removeClass('hidden-sm-up');



	}







	/*
	 * This demo illustrates the coordinate system used to display map tiles in the
	 * API.
	 *
	 * Tiles in Google Maps are numbered from the same origin as that for
	 * pixels. For Google's implementation of the Mercator projection, the origin
	 * tile is always at the northwest corner of the map, with x values increasing
	 * from west to east and y values increasing from north to south.
	 *
	 * Try panning and zooming the map to see how the coordinates change.
	 */

	/** @constructor */
	function CoordMapType(tileSize) {
		this.tileSize = tileSize;
	}

	CoordMapType.prototype.getTile = function(coord, zoom, ownerDocument) {
		var div = ownerDocument.createElement('div');
		div.innerHTML = coord;//getOutput1(coord.lat(),coord.lng());
		div.style.width = this.tileSize.width + 'px';
		div.style.height = this.tileSize.height + 'px';
		div.style.fontSize = '15';
		div.style.color = 'white';
		div.style.borderStyle = 'solid';
		div.style.borderWidth = '1px';
		div.style.borderColor = 'red';
		return div;
	};





var map;








	var action;

	function initMap() {
		map = new google.maps.Map(document.getElementById('map'), {
			center: chicago,
			zoom: 13,
			mapTypeId: 'hybrid',
			disableDefaultUI: true,
			scaleControl: true
		});



		map.addListener('click', function(e) {
			if(action != null)
				placeMarkerAndPanTo(e.latLng, map);
		});

		//var infoWindow = new google.maps.InfoWindow({map: map});

		// Try HTML5 geolocation.
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				var pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};



				//infoWindow.setPosition(pos);
				//infoWindow.setContent('Location found.');
				var marker = new google.maps.Marker({
					position: pos,
					label: "",
					map: map
				});


				map.setCenter(pos);

				var centerControlDiv = document.createElement('div');
				var centerControl = new CenterControl(centerControlDiv, map, pos);

				centerControlDiv.index = 1;
				centerControlDiv.style['padding-top'] = '10px';
				map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv);
				//centerControl.setCenter(map.getCenter());
			}, function() {
				handleLocationError(true, infoWindow, map.getCenter());
			});
		} else {
			// Browser doesn't support Geolocation
			handleLocationError(false, infoWindow, map.getCenter());
		}


		// Insert this overlay map type as the first overlay map type at
		// position 0. Note that all overlay map types appear on top of
		// their parent base map.
		//map.overlayMapTypes.insertAt(
		//	0, new CoordMapType(new google.maps.Size(256,256)));
		drawLines();
	}

















	function drawLines(){
		var nBoundLL;
		var eBoundLL;
		var sBoundLL;
		var wBoundLL;
		var ne;
		var sw;

		var bounds;
		var nBound;
		var eBound;
		var sBound;
		var wBound;

		var wDiff;
		var first;

		var easting;

		setTimeout(function(){
			 nBoundLL = map.getBounds().getNorthEast().lat();
			 eBoundLL = map.getBounds().getNorthEast().lng();
			 sBoundLL = map.getBounds().getSouthWest().lat();
			 wBoundLL = map.getBounds().getSouthWest().lng();
			ne = getOutput1( map.getBounds().getNorthEast().lat(), map.getBounds().getNorthEast().lng());
			sw = getOutput1( map.getBounds().getSouthWest().lat(), map.getBounds().getSouthWest().lng());

			nBound = ne.split(" ")[3];
			eBound = ne.split(" ")[2];
			sBound = sw.split(" ")[3];
			wBound = sw.split(" ")[2];

			wDiff = 1000 -((wBound) % 1000);
			first = parseInt(wBound) + parseInt(wDiff);
			easting = first;
			var ll =[];
			USNGtoLL(sw.split(" ")[0] + ' ' + sw.split(" ")[1] + " " + first + sBound,ll);

			var ll2 =[];
			USNGtoLL(sw.split(" ")[0] + ' ' + sw.split(" ")[1] + " " + first + nBound,ll2);

			console.log(wBound);
			console.log(wDiff);
			console.log(sw.split(" ")[0] + ' ' + sw.split(" ")[1] + " " + first + " " + sBound);

			var newArll = translateCoordinates(wDiff, sBoundLL,wBoundLL, 90);
			//var newArll2 = translateCoordinates(1000, newArll[0],newArll[1], 90);
			var gfr = parseFloat(wBoundLL) +.012;
			console.log(wBoundLL + ' ' + gfr);
			var gridLines = [
				{lat: sBoundLL, lng: wBoundLL},
				{lat: nBoundLL, lng: wBoundLL},
				{lat: sBoundLL, lng: gfr},
				{lat: nBoundLL, lng: gfr}
			];


			//while(easting < eBound){

			//}

			gridLinesDraw = new google.maps.Polyline({
				path: gridLines,
				strokeColor: '#FF0000',
				strokeOpacity: 1.0,
				strokeWeight: 2
			});


			gridLinesDraw.setMap(map);


		}, 1000)
	//setTimeout(function(){window.alert(ne);}, 5000)




	}

	function translateCoordinates(distance, Lat,Lng, angle) {
		var distanceNorth = Math.sin(angle) * distance;
		var distanceEast = Math.cos(angle) * distance;
		var earthRadius = 6371000;
		var newLat = Lat + (distanceNorth / earthRadius) * 180 / Math.PI;
		var newLon = Lng + (distanceEast / (earthRadius * Math.cos(newLat * 180 / Math.PI))) * 180 / Math.PI;

		return [newLat, newLon];
	}



	function placeMarkerAndPanTo(latLng, map) {
		if(action.localeCompare("op") == 0){
			opArrayLL.push([latLng.lat(),latLng.lng()]);
			var marker = new google.maps.Marker({
				position: latLng,
				map: map,
				icon: opImg
			});

		}

		else if(action.localeCompare("target") == 0){
			targetArrayLL.push([latLng.lat(),latLng.lng()]);
			var marker = new google.maps.Marker({
				position: latLng,
				map: map,
				icon: targetImg
			});

		}

		else if(action.localeCompare("waypoint") == 0){
			waypointArrayLL.push([latLng.lat(),latLng.lng()]);
			var marker = new google.maps.Marker({
				position: latLng,
				map: map,
				icon: waypointImg
			});

		}
		//map.panTo(latLng);
		//document.getElementById('grid').innerHTML =
		//        document.getElementById('grid').innerHTML + "\n" +
		//        latLng.toString();
		getOutput(latLng.lat(),latLng.lng());

	}

	function handleLocationError(browserHasGeolocation, infoWindow, pos) {
		infoWindow.setPosition(pos);
		infoWindow.setContent(browserHasGeolocation ?
			'Error: The Geolocation service failed.' :
			'Error: Your browser doesn\'t support geolocation.');
	}




	function getOutput(lat1,lng1) {
		$.ajax({
			url:'latlngtomgrs.php',
			type: "POST",
			dataType:'json', // add json datatype to get json
			data: ({lat: lat1,
				lng: lng1}),
			complete: function (response) {
				$('#grid').html(response.responseText);

				switch (action){
					case "op":
						opArray.push(response.responseText);
						break;
					case "target":
						targetArray.push(response.responseText);
						break;
					case "waypoint":
						waypointArray.push(response.responseText);
						break;
				}
			},
			error: function () {
				$('#grid').html('Bummer: there was an error!');
			}
		});
		return false;
	}

	function getOutput1(lat1,lng1) {
		var out = '';
		$.ajax({
			url:'latlngtomgrs.php',
			type: "POST",
			async: false,
			dataType:'json', // add json datatype to get json
			data: ({lat: lat1,
				lng: lng1}),
			complete: function (response) {

				out = response.responseText;
				},
		});
		return out;
	}









</script>
<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAaj2fDQUY37eczlFPwqbZFaJf7SgAjibg&callback=initMap">
</script>

</body>
</html>


