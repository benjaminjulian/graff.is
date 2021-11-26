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
    <link rel="stylesheet" type="text/css" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
    <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js'></script>
    <script type='text/javascript' src='http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js'></script>
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
            <ul>
<?php
    require "../creds.php";
    $mysqli = new mysqli("localhost", $mysql_user, $mysql_pass);
    $mysqli -> select_db("base_db");
    $mysqli -> query("SET time_zone = '+00:00'");

    $q = "SELECT * FROM graffiti";

    if ($result = $mysqli -> query($q)) {
        while ($row = $result -> fetch_row()) {
            echo '<li><a href="https://graff.s3.eu-west-1.amazonaws.com/fullres/'.$row[0].'">';
            echo '<img src="https://graff.s3.eu-west-1.amazonaws.com/fullres/'.$row[0].'">';
            echo '</li>';
        }
    }
?>
            </ul>
        </div>
    </div>
    <script type='text/javascript' src='maps/markers.js'></script>
    <script type='text/javascript' src='maps/leaflet.js'></script>
    <script>
    function upgo() {
// This is input object which type of file.  
var uploader = document.getElementById("file_to_upload"); 

// We'll send a new post for each file.
for(var i=0, j=uploader.files.length; i<j; i++)
{
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