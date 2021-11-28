<!DOCTYPE html>
<html>
 
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        html {
            font-family: sans-serif;
        }

        a {
            color: #000;
        }

        #content {
            width: 90%;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
        
        #uploadlabel {
            border: 1px solid #ccc;
            display: inline-block;
            padding: 7px 15px;
            cursor: pointer;
            border-radius: 5px;
            background-color: blue;
            color: #ffffff;
            margin-left: auto;
            margin-right: auto;
        }
        #file_to_upload {
            display: none;
        }
        .progressor {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            float: left;
            margin: 3px;
        }

        #map {
            height: 75vh;
            border: 1px solid #AAA;
        }

        hr {
            margin-top: 4px;
            margin-bottom: 4px;
            width: 50%;
            min-width: 400px;
        }
        #options {
            position: relative;
            overflow: hidden;
            min-width: 400px;
            height: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        #date-selection {
            position: absolute;
            top: 50%; right: 50%;
            transform: translate(50%,-50%);
            transition: 0.3s;
        }

        #display {
            position: absolute;
            top: 50%; right: 150%;
            transform: translate(50%,-50%);
            transition: 0.3s;
        }

        .show-display #display {
            transition: 0.3s;
            right: 50%;
        }

        .show-display #date-selection {
            transition: 0.3s;
            right: -50%;
        }

        .show-date-selection #display {
            transition: 0.3s;
            right: 150%;
        }

        .show-date-selection #date-selection {
            transition: 0.3s;
            right: 50%;
        }

        p {
            font-size: 12px;
        }
    </style>
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
      integrity="sha384-VzLXTJGPSyTLX6d96AxgkKvE/LRb7ECGyTxuwtpjHnVWVZs2gp5RDjeM/tgBnVdM"
      crossorigin="anonymous"
    />

   <script
      src="https://unpkg.com/jquery@3.6.0/dist/jquery.min.js"
      integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
      integrity="sha384-RFZC58YeKApoNsIbBxf4z6JJXmh+geBSgkCQXFyh+4tiFSJmJBt+2FbjxW7Ar16M"
      crossorigin="anonymous"
    ></script>
    <script type="text/javascript" src="jpegmeta.js"></script>
    <title>graff.is</title>
 
</head>
 <!-- þessi lausn var þróuð í miklum flýti. heimasíða höfundar er benjaminjulian.com. kvartanir beinist þangað. -->
 <!-- pakkar notaðir: jQuery, Leaflet&OSM með Stamen watercolor layer, github.com/bennoleslie/jsjpegmeta, aws-sdk-php -->
<body>
    <div id="content">
        <h1>graff.is</h1>
        <label id="uploadlabel">
            <span>
                senda inn mynd
            </span>
            <input type="file" name="file_to_upload" id="file_to_upload" class="upload" onchange="upgo()">
        </label>
        <div id="progress_status"></div><div style="clear: left"></div>
        <hr>
        <div id="options">
            <div id="date-selection">
                <input type="date" id="date_from" onfocusout="reloadMap();">
                &mdash;
                <input type="date" id="date_to" onfocusout="reloadMap();">
                &nbsp;
                <button type="button" onclick="reloadClean();">&#9003;</button>
            </div>
            <div id="display"><p id="img-link"></p></div>
        </div>
        <hr>
        <div id="album">
            <div id="map"></div>
        </div>
        <hr>
        <p><a href="#" onclick="huntDown();">súmma hingað</a></p>
        <hr>
    </div>
    <script>
        var $j = this.JpegMeta.JpegFile;
        var map;
        
        loadMarkers();

        function centerMap(position) {
            target = L.latLng(position.coords.latitude, position.coords.longitude);
            map.setView(target, 17);
        }

        function huntDown() {
            navigator.geolocation.getCurrentPosition(centerMap);
        }

        function reloadMap() {
            document.getElementById("album").innerHTML = '<div id="map"></div>';
            loadMarkers();
        }

        function reloadClean() {
            document.getElementById('date_from').value = "";
            document.getElementById('date_to').value = "";
            reloadMap();
        }

        function loadMarkers() {
            dt_from = document.getElementById('date_from').value;
            dt_to = document.getElementById('date_to').value;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'markers.php?from=' + dt_from + '&to=' + dt_to, true);
            xhr.responseType = 'json';
            xhr.onload = function() {
                initMap(xhr.response);
            }
            xhr.send();
        }

        function sendDisplayID(id) {
            return function() {
                this.openPopup();
                display(id);
            }
        }

        function initMap(markers) {
            map = L.map('map', {
                center: [20.0, 5.0],
                minZoom: 2,
                zoom: 2,
            });
            
            L.tileLayer('https://stamen-tiles.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg', {
                attribution:
                '<a href="http://maps.stamen.com">Stamen</a> | <a href="https://www.openstreetmap.org/copyright">OSM</a>',
                subdomains: ['a', 'b', 'c'],
            }).addTo(map);

            var myURL = 'maps/';
            
            var myIcon = L.icon({
                iconUrl: myURL + 'marker.png',
                iconRetinaUrl: myURL + 'markerxl.png',
                iconSize: [18, 30],
                iconAnchor: [9, 5],
                popupAnchor: [0, -10],
            });

            var popups = [];
            var pins = [];
            
            for (var i = 0; i < markers.length; ++i) {
                popups[i] = L.popup({maxWidth: "auto", autoPan: false, className: 'popup-box'})
                    .setContent('<img src="https://graff.s3.eu-west-1.amazonaws.com/thumbs/'
                                    + markers[i].file_name
                                    + '"><br><span>' 
                                    + markers[i].date_taken + '</span>');
                pins[i] = L.marker([markers[i].lat, markers[i].lng], { icon: myIcon }).addTo(map);
                pins[i].bindPopup(popups[i]);
                popups[i].on('remove', function() { display(); });
                if (window.location.hash.substr(1) == markers[i].id) {
                    pins[i].fire('click');
                    sendDisplayID(markers[i].id)
                }
                pins[i].on('click', sendDisplayID(markers[i].id));
            }

            document.querySelector(".leaflet-popup-pane").addEventListener("load", function (event) {
                var tagName = event.target.tagName,
                    popup = map._popup; // Currently open popup, if any.

                if (tagName === "IMG" && popup) {
                    popup.update();
                }
                }, true);
        }

        function display(id) {
            if (id === undefined) {
                window.location.hash = "";
                document.getElementById('options').className = "show-date-selection";
            } else {
                document.getElementById('img-link').innerHTML = '<a href="/img?id=' + id + '">skoða mynd</a>';
                document.getElementById('options').className = "show-display";
                window.location.hash = id;
            }
        }

        function upgo() {
            // This is input object which type of file.  
            var uploader = document.getElementById("file_to_upload"); 
            var dataurl_reader = new FileReader();
            var fname;

            // We'll send a new post for each file.
            for(var i=0, j=uploader.files.length; i<j; i++) {
                fname = uploader.files[i].name;
                dataurl_reader.readAsDataURL(uploader.files[i]);
                dataurl_reader.onloadend = function() {
                    var jpeg = new $j(atob(this.result.replace(/^.*?,/,'')), uploader.files[i]);

                    if (jpeg.gps.longitude) {
                        var uploaderForm = new FormData(); // Create new FormData
                        uploaderForm.append("action", "post"); // append extra parameters if you wish.
                        uploaderForm.append("image", dataurl_reader.result); // append the next file for upload
                        uploaderForm.append("longitude", jpeg.gps.longitude);
                        uploaderForm.append("latitude", jpeg.gps.latitude);
                        uploaderForm.append("date_taken", jpeg.exif.DateTimeOriginal.value);
                        uploaderForm.append("name", fname);

                        var xhr = new XMLHttpRequest();
                        xhr.onreadystatechange = function() {       
                            if(xhr.readyState==4 && xhr.status==200) {
                                document.getElementById('progress_status').innerHTML = xhr.responseText;
                                reloadMap();
                            }
                        }
                        
                        xhr.addEventListener("loadstart", function(e){
                            this.progressId = "progress_" + Math.floor((Math.random() * 100000)); 

                            
                        });

                        xhr.upload.onprogress = function(e) 
                        {
                            var completed = 0;
                            if (e.lengthComputable) {
                                var done = e.position || e.loaded,
                                    total = e.totalSize || e.total;
                                
                                if (document.getElementById("nr_" + total) == null) {
                                    $("#progress_status").append('<span id="nr_' + total + '" class="progressor" ></span>');
                                }
                                completed = Math.round((done / total * 1000) / 10);

                                if (completed == 100) {
                                    document.getElementById('nr_' + total).innerHTML = 'Skrái mynd...';
                                } else {
                                    document.getElementById('nr_' + total).innerHTML = completed + '%';
                                }
                            }
                        };

                        xhr.open("POST","uploado.php");
                        xhr.send(uploaderForm);
                    } else {
                        $("#progress_status").append('<span class="progressor" >Ekkert EXIF</span>');
                    }
                }
            }
        }
    </script>
</body>
 
</html>