<?php
    require "../creds.php";
    $mysqli = new mysqli("localhost", $mysql_user, $mysql_pass);
    $mysqli -> select_db("base_db");
    $mysqli -> query("SET time_zone = '+00:00'");

    $q = "SELECT * FROM graffiti";

    if ($result = $mysqli -> query($q)) {
        while ($row = $result -> fetch_row()) {
            echo "{";
            echo 'file_name: "'.$row[0].'",';
            echo 'lat: '.$row[4].',';
            echo 'lon: '.$row[5].',';
            echo 'date_taken: "'.$row[2].'"';
            echo '},';
        }
    }
?>