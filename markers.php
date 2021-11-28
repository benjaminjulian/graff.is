<?php
    require "../creds.php";
    header('Content-Type: application/json; charset=utf-8');

    $mysqli = new mysqli("localhost", $mysql_user, $mysql_pass);
    $mysqli -> select_db("base_db");
    $mysqli -> query("SET time_zone = '+00:00'");

    $date_from = "";
    $date_to = "";
    
    if (isset($_GET['from'])) {
        $date_from = $mysqli -> real_escape_string($_GET['from']);
    } 
    if ($date_from == "") {
        $date_from = "1900-01-01";
    }
    if (isset($_GET['to'])) {
        $date_to = $mysqli -> real_escape_string($_GET['to']);
    } 
    if ($date_to == "") {
        $date_to = "2100-12-12";
    }

    $json = [];
    $markers = [];
    $meta = [];

    $q = "SELECT * FROM graffiti WHERE DATE(date_taken) >= '$date_from' AND DATE(date_taken) <= '$date_to'";

    if ($result = $mysqli -> query($q)) {
        while ($row = $result -> fetch_row()) {
            array_push($markers, array("id" => $row[0], "file_name" => $row[1], "lat" => (float)$row[5], "lng" => (float)$row[6], "date_taken" => $row[3]));
        }
    }

    $q = "SELECT MIN(date_taken), MAX(date_taken) FROM graffiti";

    if ($result = $mysqli -> query($q)) {
        $row = $result -> fetch_row();

        $meta = array("min_date" => $row[0], "max_date" => $row[1]);
    }

    $json = array("markers" => $markers, "meta" => $meta);

    echo json_encode($json);
?>