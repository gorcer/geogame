<?php

function isItWatter($lat,$lng) {
    $geokey='AIzaSyBm0l_5BV-bMcYZVMu4OEgxg56tKU8l9a0';
    $GMAPStaticUrl = "https://maps.googleapis.com/maps/api/staticmap?center=".$lat.",".$lng."&size=10x10&maptype=roadmap&sensor=false&zoom=16&key=".$geokey;  
    //echo $GMAPStaticUrl;
    $chuid = curl_init();
    curl_setopt($chuid, CURLOPT_URL, $GMAPStaticUrl);   
    curl_setopt($chuid, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($chuid, CURLOPT_SSL_VERIFYPEER, FALSE);
    $data = trim(curl_exec($chuid));
    curl_close($chuid);
    $image = imagecreatefromstring($data);

if ($image == false)
{
 echo 'unable to load image by url '.$GMAPStaticUrl;
 return;
}
    // this is for debug to print the image
    ob_start();
    imagepng($image);
    $contents =  ob_get_contents();
    ob_end_clean();
//    echo "<img src='data:image/png;base64,".base64_encode($contents)."' />";

    // here is the test : I only test 3 pixels ( enough to avoid rivers ... )
    $hexaColor = imagecolorat($image,0,0);
    $color_tran = imagecolorsforindex($image, $hexaColor);

    $hexaColor2 = imagecolorat($image,0,1);
    $color_tran2 = imagecolorsforindex($image, $hexaColor2);

    $hexaColor3 = imagecolorat($image,0,2);
    $color_tran3 = imagecolorsforindex($image, $hexaColor3);

    $red = $color_tran['red'] + $color_tran2['red'] + $color_tran3['red'];
    $green = $color_tran['green'] + $color_tran2['green'] + $color_tran3['green'];
    $blue = $color_tran['blue'] + $color_tran2['blue'] + $color_tran3['blue'];

    imagedestroy($image);
//    var_dump($red,$green,$blue);
    //int(492) int(570) int(660) 
// 534 - 624 - 762


    if($red == 537 && $green == 627 && $blue == 765)
        return 1;
    else
        return 0;
}

$connection = new MongoClient();
$geo = $connection->geo->map;

$defLat = 43.08;
$defLang = 131.88;
$step = 0.001;
for ($j=0;$j<100;$j++) {
for ($i=0;$i<100;$i++) {
 $lat = $defLat + $step*$j;
 $lng = $defLang + $step*$i;

 if ($geo->findOne(['lat'=>$lat, 'lng'=>$lng]) == null) {
 $isWater = isItWatter($lat,$lng);
 $groundType = abs($isWater-1);
 $doc = array(
    "lat" => round($lat, 6),
    "lng" => round($lng, 6),
    "type" => $groundType,
 );
//var_dump($doc);
  $geo->insert($doc);
}

// echo $lat.','.$lng.'=>'.isItWatter($lat,$lng).PHP_EOL.'<br/>';
}
//echo "<br/>";
}


?>