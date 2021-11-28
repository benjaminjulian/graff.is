<?php 
    require "../../creds.php";
    include("../header.php");
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
?>
<style>
    #preview {
        max-width: 80%;
        height: auto;
    }
    a {
        text-decoration: none;
    }
</style>
</head>
<body>
<div id="content">
<h1><a href="//graff.is">graff.is</a></h1>
<?php
    $mysqli = new mysqli("localhost", $mysql_user, $mysql_pass);
    $mysqli -> select_db("base_db");
    $mysqli -> query("SET time_zone = '+00:00'");

    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];

        $q = "SELECT * FROM graffiti WHERE id = '$id'";
    
        if ($result = $mysqli -> query($q)) {
            $row = $result -> fetch_row();
            
            echo "<ul>";
            echo "<li>Tímasetning myndar: ".$row[3]."</li>";
            echo "<li>Hnit: ".$row[5].", ".$row[6]."</li>";
            echo "</ul>";
            echo '<img id="preview" src="https://graff.s3.eu-west-1.amazonaws.com/fullres/'.$row[1].'">';
        }
    } else {
        echo "<h2>Engin mynd valin.</h2>";
    }
?>
</div>
</body>
</html>