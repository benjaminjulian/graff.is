<!DOCTYPE html>
<html>
 
<head>
    <meta name="viewport" content="width=device-width, initial-scale=0.9">

    <style>
        html {
            font-family: sans-serif;
        }

        #content {
            width: 450px;
            margin-left: auto;
            margin-right: auto;

        }
        
        #uploadlabel {
            border: 1px solid #ccc;
            display: inline-block;
            padding: 7px 15px;
            cursor: pointer;
            border-radius: 5px;
            background-color: blue;
            color: #ffffff;
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
    <title>graff.is</title>
 
</head>
 
<body>
    <div id="content">
        <h1>graff.is</h1>
        <h2>Senda inn myndir af graffi</h2>
        <label id="uploadlabel">
            <span>
                Veldu myndir
            </span>
            <input type="file" name="file_to_upload" id="file_to_upload" class="upload" onchange="upgo()" accept=".jpg, .jpeg, .png" multiple>
        </label>
        <div id="progress_status"></div>
        <hr>
        <div id="album">
            <div id="map" style="height: 440px; border: 1px solid #AAA;"></div>
        </div>
    </div>
    <script>
        function loadMarkers() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'markers.php', true);
            xhr.responseType = 'json';
            xhr.onload = function() {      
                console.log(xhr.response); 
                initMap(xhr.response);
            }
            xhr.send();
        }
        function initMap(markers) {
            var map = L.map('map', {
                center: [20.0, 5.0],
                minZoom: 2,
                zoom: 2,
            });
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
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
            
            for (var i = 0; i < markers.length; ++i) {
                L.marker([markers[i].lat, markers[i].lng], { icon: myIcon })
                .bindPopup(
                    L.popup().setContent(
                    'link r sum'//'<img src="https://graff.s3.eu-west-1.amazonaws.com/thumbs/' + markers[i].file_name + '">'
                    ).openOn(map)
                )
                .addTo(map)
            }
        }

        function upgo() {
            // This is input object which type of file.  
            var uploader = document.getElementById("file_to_upload"); 

            // We'll send a new post for each file.
            for(var i=0, j=uploader.files.length; i<j; i++) {
                var uploaderForm = new FormData(); // Create new FormData
                uploaderForm.append("action","upload"); // append extra parameters if you wish.
                uploaderForm.append("image",uploader.files[i]); // append the next file for upload

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {       
                    if(xhr.readyState==4 && xhr.status==200)
                    {
                    document.getElementById('progress_status').innerHTML = "Komið!";
                    }
                }
                
                xhr.addEventListener("loadstart", function(e){
                    console.log(e)
                    // generate unique id for progress bars. This is important because we'll use it on progress event for modifications
                    this.progressId = "progress_" + Math.floor((Math.random() * 100000)); 

                    
                });

                /*xhr.upload.onprogress = function(e){
                    var done = e.position || e.loaded, total = e.totalSize || e.total
                    var present = Math.floor(done/total*100)
                    
                    document.getElementById('progress_status').innerHTML = present + '%'
                }*/
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
                        document.getElementById('nr_' + total).innerHTML = completed + '%';
                    }
                };

                xhr.open("POST","uploado.php");
                xhr.send(uploaderForm);
            }
        }
    </script>
</body>
 
</html>