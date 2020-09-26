<?php
include ("server.php"); 
$objServer = new Server();
header("Content-type: image/png");
 
$img_width = 800;
$img_height = 600;
 
$img = imagecreatetruecolor($img_width, $img_height);
 
$black = imagecolorallocate($img, 0, 0, 0);
$white = imagecolorallocate($img, 255, 255, 255);
$red   = imagecolorallocate($img, 255, 0, 0);
$green = imagecolorallocate($img, 0, 255, 0);
$blue  = imagecolorallocate($img, 0, 0, 255);
$orange = imagecolorallocate($img, 255, 200, 0);
$cx=400;
$cy=300;
imagefill($img, 0, 0, $white);
 
//imagerectangle($img, $cx-50, $cy-50,$cx+50, $cy+50, $red);

imageellipse($img, $cx, $cy, 400, 400, $orange);
$objServer->regularPolygon($img,$cx, $cy, 100, 4, $red);
$objServer->regularPolygon($img,$img_width/2,$img_height/2,300,6,0xffff54);  
imagepng($img);
 
?>
