<?php 
    require "../../creds.php";
    include("../header.php");
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
?>
<style>
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
            
            echo "<p>".$row[3]."</p>";
            echo "<p>hnit: ".$row[5].", ".$row[6]."</p>";
            echo "<br>";
            echo '<img id="preview" style="max-width: 80%;" onclick="fullview();" src="https://graff.s3.eu-west-1.amazonaws.com/fullres/'.$row[1].'">';
        }
    } else {
        echo "<h2>Engin mynd valin.</h2>";
    }
?>
<script>
    function fullview() {
        if (document.getElementById('preview').style.maxWidth == "80%") {
            document.getElementById('preview').style.maxWidth = "";
        } else {
            document.getElementById('preview').style.maxWidth = "80%"
        }
    }
</script>
</div>
</body>
</html>