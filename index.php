<?php
$defLat = 43.1;
$defLang = 131.9;
$step=0.001;
$width=32;

$connection = new MongoClient();
$geo = $connection->geo->map;

$cur = $geo->find();
$cur->sort(['lat'=>-1]);
$minLat = $cur->getNext()['lat'];

$cur->reset();

$cur->sort(['lng'=>1]);
$minLng = $cur->getNext()['lng'];

$cur->reset();
$cur->sort(['lat'=>-1, 'lng'=>1]);

$pos=$cur->getNext();
$prevY=0;
while ($pos !== null) {

$x = round($width * (($pos['lng'] - $minLng) / $step));
$y = round($width * (-1 * ($pos['lat'] - $minLat) / $step));

//echo 'minLng'.$minLng.PHP_EOL;
//echo 'x='.$x.' lng='.$pos['lng'].PHP_EOL;
//echo 'y='.$y.' lat='.$pos['lat'].PHP_EOL;
if ($pos['type'] == 0) {
 $image = 'water';
} else {
 $image='ground';
}

if ($prevY != $y) echo '<br/>';

echo '<img src="'.$image.'.png" style="position: absolute; left: '.$x.'px; top: '.$y.'px"/>';


$prevY=$y;
$pos=$cur->getNext();
}

?>