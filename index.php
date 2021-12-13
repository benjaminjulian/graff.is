<?php include("header.php"); ?>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="https://use.typekit.net/upb2iby.css">
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
    <script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="jpegmeta.js"></script>
    <link rel="stylesheet" href="../dist/MarkerCluster.css" />
	<link rel="stylesheet" href="../dist/MarkerCluster.Default.css" />
	<script src="../dist/leaflet.markercluster.js"></script>
    <title>graff.is</title>
</head>
 <!-- þessi lausn var þróuð í miklum flýti. heimasíða höfundar er benjaminjulian.com. kvartanir beinist þangað. -->
 <!-- pakkar notaðir: jQuery, Leaflet&OSM með Stamen watercolor layer, github.com/bennoleslie/jsjpegmeta, aws-sdk-php -->
<body>
    <div class="content">
        <div class="header">
            <h1>
                graff.is
            </h1>
            <div class="upload">
                <label id="uploadlabel">
                    <span>
                        senda mynd
                    </span>
                    <input type="file" name="file_to_upload" id="file_to_upload" class="upload" onchange="upgo()">
                </label>
                <div id="progress_status"></div>
            </div>
            <div id="options">
                <div id="date-selection">
                <p><span id="date_from"></span> til <span id="date_to"></span></p>
                
                <div class="sliders_step1">
                    <div id="slider-range"></div>
                </div>
                </div>
                <div id="display"><p id="img-link"></p></div>
            </div>
        </div>
        <div id="album">
            <div id="map"></div>
        </div>
        <hr>
        <p><a href="#" onclick="huntDown();">súmma hingað</a></p>
    </div>
    <script>
        var $j = this.JpegMeta.JpegFile;
        var map;
        
        loadMarkers();

        function formatDT(__dt) {
            var year = __dt.getFullYear();
            var month = __dt.getMonth()+1;
            var date = __dt.getDate();
            return year + '-' + month.toString() + '-' + date.toString();
        }

        function setSlider(dt_from, dt_to) {
            $('#date_from').html(dt_from);
            $('#date_to').html(dt_to);
            var min_val = Date.parse(dt_from)/1000/60/60/24;
            var max_val = Date.parse(dt_to)/1000/60/60/24;

            $("#slider-range").slider({
                range: true,
                min: min_val,
                max: max_val,
                step: 1,
                values: [min_val, max_val],
                stop: function (e, ui) {
                    var dt_cur_from = new Date(ui.values[0]*1000*60*60*24); //.format("yyyy-mm-dd hh:ii:ss");
                    $('#date_from').html(formatDT(dt_cur_from));

                    var dt_cur_to = new Date(ui.values[1]*1000*60*60*24); //.format("yyyy-mm-dd hh:ii:ss");                
                    $('#date_to').html(formatDT(dt_cur_to));

                    reloadMap();
                }
            });
        }

        function centerMap(position) {
            target = L.latLng(position.coords.latitude, position.coords.longitude);
            map.setView(target, 17);
        }

        function huntDown() {
            navigator.geolocation.getCurrentPosition(centerMap);
        }

        function reloadMap(lat, lon) {
            document.getElementById("album").innerHTML = '<div id="map"></div>';
            if (lat == undefined) {
                center = map.getCenter();
                zoom = map.getZoom();
            } else {
                center = [lat, lon];
                zoom = 17;
            }
            loadMarkers(center, zoom);
        }

        function reloadClean() {
            reloadMap();
        }

        function loadMarkers(center, zoom) {
            dt_from = document.getElementById('date_from').innerText;
            dt_to = document.getElementById('date_to').innerText;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'markers.php?from=' + dt_from + '&to=' + dt_to, true);
            xhr.responseType = 'json';
            xhr.onload = function() {
                initMap(xhr.response, center, zoom);
            }
            xhr.send();
        }

        function sendDisplayID(id) {
            return function() {
                this.openPopup();
                display(id);
            }
        }

        function initMap(data, center, zoom) {
            if (center == undefined) {
                setSlider(data.meta.min_date.substring(0,10), data.meta.max_date.substring(0,10));
                center = [65.0, -19.0];
            }
            if (zoom == undefined) {
                zoom = 6;
            }
            map = L.map('map', {
                center: center,
                minZoom: 2,
                zoom: zoom,
            });
            
            L.tileLayer('https://stamen-tiles.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg', {
                attribution:
                '<a href="http://maps.stamen.com">Stamen</a> | <a href="https://www.openstreetmap.org/copyright">OSM</a>',
                subdomains: ['a', 'b', 'c'],
            }).addTo(map);

            var mapAssetUrl = 'maps/';
            
            var myIcon = L.icon({
                iconUrl: mapAssetUrl + 'marker.png',
                iconRetinaUrl: mapAssetUrl + 'markerxl.png',
                iconSize: [18, 30],
                iconAnchor: [9, 5],
                popupAnchor: [0, -10],
            });

            var popups = [];
            var pins = [];

            var markers = data.markers;
            var cluster = L.markerClusterGroup();
            var fire = -1;
            
            for (var i = 0; i < markers.length; ++i) {
                popups[i] = L.popup({maxWidth: "auto", autoPan: false, className: 'popup-box'})
                    .setContent('<img src="https://graff.s3.eu-west-1.amazonaws.com/thumbs/'
                                    + markers[i].file_name
                                    + '"><br><span>' 
                                    + markers[i].date_taken + '</span>');
                pins[i] = L.marker([markers[i].lat, markers[i].lng], { icon: myIcon });//.addTo(map);
                pins[i].bindPopup(popups[i]);
                popups[i].on('remove', function() { display(); });
                pins[i].on('click', sendDisplayID(markers[i].id));
                if (window.location.hash.substr(1) == markers[i].id) {
                    fire = i;
                    centerMap({'coords':{'latitude':markers[i].lat,'longitude':markers[i].lng}});
                }

                cluster.addLayer(pins[i]);
            }

            map.addLayer(cluster);
            if (fire >= 0) {
                pins[fire].fire('click');
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
                upload_data = uploader.files[i];
                dataurl_reader.readAsDataURL(uploader.files[i]);
                dataurl_reader.onloadend = function(e) {
                    var jpeg = new $j(atob(this.result.replace(/^.*?,/,'')), uploader.files[i]);

                    if (jpeg.gps) {
                        var uploaderForm = new FormData(); // Create new FormData
                        uploaderForm.append("action", "upload"); // append extra parameters if you wish.
                        uploaderForm.append("image", upload_data); // append the next file for upload
                        uploaderForm.append("longitude", jpeg.gps.longitude.value);
                        uploaderForm.append("latitude", jpeg.gps.latitude.value);
                        uploaderForm.append("date_taken", jpeg.exif.DateTimeOriginal.value);

                        var xhr = new XMLHttpRequest();
                        xhr.onreadystatechange = function() {       
                            if(xhr.readyState==4 && xhr.status==200) {
                                document.getElementById('progress_status').innerHTML = xhr.responseText;
                                reloadMap(jpeg.gps.latitude.value, jpeg.gps.longitude.value);
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
                        $("#progress_status").append('<span class="progressor">vantar staðsetningargögn</span>');
                    }
                }
            }
        }
    </script>
</body>
 
</html>