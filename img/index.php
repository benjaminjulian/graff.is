<?php include("../header.php"); ?>
<style>
    #preview {
        max-width: 80%;
        height: auto;
    }
    a {
        text-decoration: none;
    }
</head>
<body>
<div id="content">
<h1><a href="//graff.is">graff.is</a></h1>
<?php
    require "../../creds.php";

    $mysqli = new mysqli("localhost", $mysql_user, $mysql_pass);
    $mysqli -> select_db("base_db");
    $mysqli -> query("SET time_zone = '+00:00'");

    if (isset($_GET['id'])) {
        $id = (int) $_GET['id']);

        $q = "SELECT * FROM graffiti WHERE id = '$id'";
    
        if ($result = $mysqli -> query($q)) {
            $row = $result -> fetch_row();
            
            echo '<img src="https://graff.s3.eu-west-1.amazonaws.com/fullres/' + $row[1] + '">'
        }
    } else {
        echo "<h2>Engin mynd valin.</h2>"
    }
?>
</div>
</body>
</html>