<?php include("header.php"); ?>
    <style>
        #uploadlabel {
            border: 1px solid #ccc;
            display: inline-block;
            vertical-align: middle;
            padding: 7px 15px;
            cursor: pointer;
            border-radius: 5px;
            background-color: white;
            color: #404040;
            margin-left: auto;
            margin-right: auto;
            font-size: 16px
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
            height: 80vh;
            border: 1px solid #AAA;
        }

        #options {
            position: relative;
            overflow: hidden;
            height: 48px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        #date-selection {
            position: absolute;
            width: 80%;
            max-width: 500px;
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

        .ui-slider-horizontal {
            height: 8px;
            background: #D7D7D7;
            border: 1px solid #BABABA;
            box-shadow: 0 1px 0 #FFF, 0 1px 0 #CFCFCF inset;
            clear: both;
            margin: 8px 0;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            -ms-border-radius: 6px;
            -o-border-radius: 6px;
            border-radius: 6px;
        }
        .ui-slider {
            position: relative;
            text-align: left;
        }
        .ui-slider-horizontal .ui-slider-range {
            top: -1px;
            height: 100%;
        }
        .ui-slider .ui-slider-range {
            position: absolute;
            z-index: 1;
            height: 8px;
            font-size: .7em;
            display: block;
            border: 1px solid #5BA8E1;
            box-shadow: 0 1px 0 #AAD6F6 inset;
            -moz-border-radius: 6px;
            -webkit-border-radius: 6px;
            -khtml-border-radius: 6px;
            border-radius: 6px;
            background: #81B8F3;
            background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgi…pZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
            background-size: 100%;
            background-image: -webkit-gradient(linear, 50% 0, 50% 100%, color-stop(0%, #A0D4F5), color-stop(100%, #81B8F3));
            background-image: -webkit-linear-gradient(top, #A0D4F5, #81B8F3);
            background-image: -moz-linear-gradient(top, #A0D4F5, #81B8F3);
            background-image: -o-linear-gradient(top, #A0D4F5, #81B8F3);
            background-image: linear-gradient(top, #A0D4F5, #81B8F3);
        }
        .ui-slider .ui-slider-handle {
            border-radius: 50%;
            background: #F9FBFA;
            background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgi…pZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
            background-size: 100%;
            background-image: -webkit-gradient(linear, 50% 0, 50% 100%, color-stop(0%, #C7CED6), color-stop(100%, #F9FBFA));
            background-image: -webkit-linear-gradient(top, #C7CED6, #F9FBFA);
            background-image: -moz-linear-gradient(top, #C7CED6, #F9FBFA);
            background-image: -o-linear-gradient(top, #C7CED6, #F9FBFA);
            background-image: linear-gradient(top, #C7CED6, #F9FBFA);
            width: 22px;
            height: 22px;
            -webkit-box-shadow: 0 2px 3px -1px rgba(0, 0, 0, 0.6), 0 -1px 0 1px rgba(0, 0, 0, 0.15) inset, 0 1px 0 1px rgba(255, 255, 255, 0.9) inset;
            -moz-box-shadow: 0 2px 3px -1px rgba(0, 0, 0, 0.6), 0 -1px 0 1px rgba(0, 0, 0, 0.15) inset, 0 1px 0 1px rgba(255, 255, 255, 0.9) inset;
            box-shadow: 0 2px 3px -1px rgba(0, 0, 0, 0.6), 0 -1px 0 1px rgba(0, 0, 0, 0.15) inset, 0 1px 0 1px rgba(255, 255, 255, 0.9) inset;
            -webkit-transition: box-shadow .3s;
            -moz-transition: box-shadow .3s;
            -o-transition: box-shadow .3s;
            transition: box-shadow .3s;
        }
        .ui-slider .ui-slider-handle {
            position: absolute;
            z-index: 2;
            width: 22px;
            height: 22px;
            cursor: default;
            border: none;
            cursor: pointer;
        }
        .ui-slider .ui-slider-handle:after {
            content:"";
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            top: 50%;
            margin-top: -4px;
            left: 50%;
            margin-left: -4px;
            background: #30A2D2;
            -webkit-box-shadow: 0 1px 1px 1px rgba(22, 73, 163, 0.7) inset, 0 1px 0 0 #FFF;
            -moz-box-shadow: 0 1px 1px 1px rgba(22, 73, 163, 0.7) inset, 0 1px 0 0 white;
            box-shadow: 0 1px 1px 1px rgba(22, 73, 163, 0.7) inset, 0 1px 0 0 #FFF;
        }
        .ui-slider-horizontal .ui-slider-handle {
            top: -.5em;
            margin-left: -.6em;
        }
        .ui-slider a:focus {
            outline:none;
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
    <script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="jpegmeta.js"></script>
    <title>graff.is</title>
 
</head>
 <!-- þessi lausn var þróuð í miklum flýti. heimasíða höfundar er benjaminjulian.com. kvartanir beinist þangað. -->
 <!-- pakkar notaðir: jQuery, Leaflet&OSM með Stamen watercolor layer, github.com/bennoleslie/jsjpegmeta, aws-sdk-php -->
<body>
    <div id="content">
        <h1>
            graff.is
            &mdash;
            <label id="uploadlabel">
                <span>
                    senda mynd
                </span>
                <input type="file" name="file_to_upload" id="file_to_upload" class="upload" onchange="upgo()">
            </label>
        </h1>
        <div id="progress_status"></div><div style="clear: left"></div>
        <hr>
        <div id="options">
            <div id="date-selection">
            <p><span id="date_from"></span> til <span id="date_to"></span></p>
            
            <div class="sliders_step1">
                <div id="slider-range"></div>
            </div>
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

        function reloadMap() {
            document.getElementById("album").innerHTML = '<div id="map"></div>';
            center = map.getCenter();
            zoom = map.getZoom();
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

            markers = data.markers;
            
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
                console.log("uploading: file before and after");
                upload_data = uploader.files[i];
                dataurl_reader.readAsDataURL(uploader.files[i]);
                dataurl_reader.onloadend = function(e) {
                    var jpeg = new $j(atob(this.result.replace(/^.*?,/,'')), uploader.files[i]);

                    if (jpeg.gps.longitude) {
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