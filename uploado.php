<?php
    require "../creds.php";
    require 'vendor/autoload.php';
    function getGps($exifCoord, $hemi) {

        $degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;

        $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);

    }

    function gps2Num($coordPart) {

        $parts = explode('/', $coordPart);

        if (count($parts) <= 0)
            return 0;

        if (count($parts) == 1)
            return $parts[0];

        return floatval($parts[0]) / floatval($parts[1]);
    }

    $file_name = $_FILES['image']['name'];   
    $temp_file_location = $_FILES['image']['tmp_name']; 

    try {
        $exif = exif_read_data($temp_file_location);

        $lon = getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
        $lat = getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
        $date = $exif['DateTimeOriginal'];

        $prefix = date("Y-m-d--H-i-s ", strtotime($date));
    } catch (Exception $e) {
        echo "Villa í EXIF lestri";
    }

    $mysqli = new mysqli("localhost", $mysql_user, $mysql_pass);
    $mysqli -> select_db("base_db");
    $mysqli -> query("SET time_zone = '+00:00'");

    $q_filename = $mysqli -> real_escape_string($prefix."_".$file_name);
    $q_ip_address = $mysqli -> real_escape_string($_SERVER['REMOTE_ADDR']);
    $q_date_taken = $mysqli -> real_escape_string($date);
    $q_latitude = $mysqli -> real_escape_string($lat);
    $q_longitude = $mysqli -> real_escape_string($lon);


    $q = "INSERT INTO graffiti (file_name, ip_address, date_taken, date_uploaded, gps_latitude, gps_longitude) values
            ('$q_filename', '$q_ip_address', '$q_date_taken', NOW(), $q_latitude, $q_longitude);";
    
    if ($mysqli -> query($q) === TRUE) {
        $maxDim = 300;
        list($width, $height, $type, $attr) = getimagesize( $temp_file_location );
        if ( $width > $maxDim || $height > $maxDim ) {
            $target_filename = $file_name;
            $ratio = $width/$height;
            if( $ratio > 1) {
                $new_width = $maxDim;
                $new_height = $maxDim/$ratio;
            } else {
                $new_width = $maxDim*$ratio;
                $new_height = $maxDim;
            }
            $src = imagecreatefromstring( file_get_contents( $temp_file_location ) );
            $dst = imagecreatetruecolor( $new_width, $new_height );
            imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
            imagedestroy( $src );
            $exif = exif_read_data($temp_file_location);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($orientation != 1)
                {
                    $deg = 0;
                    switch ($orientation)
                    {
                        case 3:
                            $deg = 180;
                        break;
                        case 6:
                            $deg = 270;
                        break;
                        case 8:
                            $deg = 90;
                        break;
                    }
                    if ($deg)
                    {
                        $dst = imagerotate($dst, $deg, 0);
                    }
                } // if there is some rotation necessary
                
            } // if have the exif orientation info
            imagejpeg( $dst, $target_filename ); // adjust format as needed
            imagedestroy( $dst );
        }

        $s3 = new Aws\S3\S3Client([
            'region'  => 'eu-west-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => $creds_key,
                'secret' => $creds_secret,
            ]
        ]);

        $result = $s3->putObject([
            'Bucket' => 'graff',
            'Key'    => "thumbs/".$prefix."_".$file_name,
            'SourceFile' => $target_filename
        ]);		

        $result = $s3->putObject([
            'Bucket' => 'graff',
            'Key'    => "fullres/".$prefix."_".$file_name,
            'SourceFile' => $temp_file_location		
        ]);

        unlink($target_filename);

        echo "Skráð."
    } else {
        die("Engin EXIF gögn!");
    }
?>