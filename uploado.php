
<?php
require "creds.php";

$album = $_POST["album"];
$file_name = $_FILES['image']['name'];   
$temp_file_location = $_FILES['image']['tmp_name']; 
		
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
    if ($exif && isset($exif['Orientation']))
    {
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

try {
    $data = exif_read_data($temp_file_location);
    var_dump($data);
    $prefix = date("Y-m-d--H-i-s ", strtotime($data['DateTimeOriginal']));
} catch (exception $e) {
    $prefix = rand(1000000, 9999999);
}

		require '../vendor/autoload.php';

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
?>