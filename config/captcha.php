<?php

session_start();
$text = substr(md5(rand()), 0, 6);
$_SESSION['captcha'] = $text;

$img = imagecreate(120, 40);
$bg = imagecolorallocate($img, 255, 255, 255);
$textcolor = imagecolorallocate($img, 0, 0, 0);
imagestring($img, 5, 30, 12, $text, $textcolor);

header('Content-type: image/png');
imagepng($img);
imagedestroy($img);

?>