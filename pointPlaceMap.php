<?php
session_start();
require_once ("config.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  $conn;
  if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
  }
  $result = mysqli_query($conn,"SELECT lat, lng,comments,level FROM markers");   
  while($row = mysqli_fetch_assoc( $result)){
      $json[] = $row;
  }

  $json_encoded = json_encode($json,JSON_NUMERIC_CHECK  );
  $html = "<!DOCTYPE html>
    <meta name='viewport' content='initial-scale=1.0, user-scalable=no' />
    <meta http-equiv='content-type' content='text/html; charset=UTF-8'/>
    <title>From Info Windows to a Database: Saving User-Added Form Data</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
    <div id='map' height='460px' width='100%' ></div>
    <div id='form' style = 'display: none'>
      <table>
       
        <tr><td> Level </td>


        <td> 
        Level Of Pollution <br>
        <input type='radio' name='lev' value='1'> 1 &nbsp; &nbsp;
        <input type='radio' name='lev' value='2'> 2 &nbsp; &nbsp;
        <input type='radio' name='lev' value='3'> 3 &nbsp; &nbsp;
        <input type='radio' name='lev' value='4'> 4 &nbsp; &nbsp;
        <input type='radio' name='lev' value='5'> 5 &nbsp; &nbsp;

        <br>    
        </td></tr>

        <tr><td> Comment </td><td> <textarea id='comment' name='comment' rows='10' cols='40'> </textarea> </td></tr>
  
            <tr><td></td><td><input type='button' value='Save' onclick='saveData()'/></td></tr>
      </table>
    </div>
    <div id='message'>Location saved</div>
    <script>

      var map;
      var marker;
      var infowindow;
      var messagewindow;
      var statewindow = null;
      var labels = 'DASTAN';


      function initMap() {

        initWindows();

        map = new google.maps.Map(document.getElementById('map'), {
           zoom: 12,
            center: {lat: 42.8640117, lng: 74.5460088 }
         });
        
        initListeners();
 
      }
      function initWindows(){
 $('#form').show();
        infowindow = new google.maps.InfoWindow({
	   
            content: document.getElementById('form')
        });

        messagewindow = new google.maps.InfoWindow({
          content: document.getElementById('message')
        });

      }

      function initListeners(){
          google.maps.event.addListener(map, 'click', function(event) {
          
           placeMarker(event.latLng);
              google.maps.event.addListener(marker, 'click', function() {
              infowindow.open(map, marker);
            });

          });
      
        var markers = locations.map(function(location, i) {

           var markerPointed = new google.maps.Marker({
             position: ({lat: location.lat,lng: location.lng}),
          });
         
          markerPointed.addListener('click', function(){
             
             if (statewindow ) {
                statewindow.close();
              }

              statewindow= new google.maps.InfoWindow({
               content: '<h3> comment:' + location.comments + '<br>Level of Pollution: ' + location.level.toString()  + '<h3>'
             })

              statewindow.open(map,markerPointed);
              
          });

          return markerPointed;
        });

          // Add a marker clusterer to manage the markers.
        var markerCluster = new MarkerClusterer(map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});


      }
      function placeMarker(location) {
	      if (!marker || !marker.setPosition) {
	        marker = new google.maps.Marker({
	          position: location,
	          map: map,
	        });
	      } else {
	        marker.setPosition(location);
	      }
	      if (infowindow && infowindow.close) {
	        infowindow.close();
	      }
           

      		infowindow.open(map, marker);
  		}
       var locations = $json_encoded

      function saveData() {

        var comment = escape(document.getElementById('comment').value);
        var latlng = marker.getPosition();
        var levels = document.getElementsByName('lev');
        var level = 3;

        for (var i = 0, length = levels.length; i < length; i++)
      {
       if (levels[i].checked)
       {

        level = levels[i].value;
        break;
       }
    }
        
        var url = 'http://5.59.11.66/~zveri/pointPlace.php?comment=' + comment +  '&level=' + level +'&lat=' + latlng.lat() + '&lng=' + latlng.lng();

         messagewindow.open(map, marker);
          marker = new google.maps.Marker({

            position: event.latLng,
            map: map

          });
    	window.location.replace( url );      

        downloadUrl(url, function(data, responseCode) {

          if (responseCode == 200 && data.length <= 1) {
            infowindow.close();
                       
          }
        });
        
      }

      function downloadUrl(url, callback) {
        var request = window.ActiveXObject ?
            new ActiveXObject('Microsoft.XMLHTTP') :
            new XMLHttpRequest;

        request.onreadystatechange = function() {
          if (request.readyState == 4) {
            request.onreadystatechange = doNothing;
            callback(request.responseText, request.status);
          }
        };

      }


      function doNothing () {
      }

    </script>
     <script src='https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js'>
    </script>
    <script async defer
    src='https://maps.googleapis.com/maps/api/js?key=AIzaSyBlLms-yD7lNgRk3z4LIpv79WvNTP2aY1I&callback=initMap'>
    </script>

</html>";

  echo $html;

?>