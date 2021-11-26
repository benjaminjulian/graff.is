<?php
    require "../creds.php";
    header('Content-Type: application/json; charset=utf-8');

    $mysqli = new mysqli("localhost", $mysql_user, $mysql_pass);
    $mysqli -> select_db("base_db");
    $mysqli -> query("SET time_zone = '+00:00'");

    $q = "SELECT * FROM graffiti";

    $json = [];

    if ($result = $mysqli -> query($q)) {
        while ($row = $result -> fetch_row()) {
            array_push($json, array("file_name" => $row[0], "lat" => (float)$row[4], "lng" => (float)$row[5], "date_taken" => $row[2]));
        }
    }

    echo json_encode($json);
?>