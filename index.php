<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
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
        // We set up a variable for this since we're adding event listeners
        // later.
        var control = this;

        // Set the center property upon construction
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
        goCenterText.innerHTML = 'Center Map';
        goCenterUI.appendChild(goCenterText);

        // Set CSS for the setCenter control border
        var setCenterUI = document.createElement('div');
        setCenterUI.id = 'setCenterUI';
        setCenterUI.title = 'Click to change the center of the map';
        controlDiv.appendChild(setCenterUI);

        // Set CSS for the control interior
        var setCenterText = document.createElement('div');
        setCenterText.id = 'setCenterText';
        setCenterText.innerHTML = 'Set Center';
        setCenterUI.appendChild(setCenterText);

        // Set up the click event listener for 'Center Map': Set the center of
        // the map
        // to the current center of the control.
        goCenterUI.addEventListener('click', function() {
          var currentCenter = control.getCenter();
          map.setCenter(currentCenter);
        });

        // Set up the click event listener for 'Set Center': Set the center of
        // the control to the current center of the map.
        setCenterUI.addEventListener('click', function() {
          var newCenter = map.getCenter();
          control.setCenter(newCenter);
        });
      }

      /**
       * Define a property to hold the center state.
       * @private
       */
      CenterControl.prototype.center_ = null;

      /**
       * Gets the map center.
       * @return {?google.maps.LatLng}
       */
      CenterControl.prototype.getCenter = function() {
        return this.center_;
      };

      /**
       * Sets the map center.
       * @param {?google.maps.LatLng} center
       */
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

	
	
	
	
	
	
	
	
	
	
	
	
	

	
	var action;
	
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
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









    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAaj2fDQUY37eczlFPwqbZFaJf7SgAjibg&callback=initMap">
    </script>

</body>
</html>


