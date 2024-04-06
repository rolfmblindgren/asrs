<?php

require_once '/home/vds/www/vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;


$skjema = 'ASRS_1-1_Page_2';

// Koordinater hvor krysset skal plasseres
$x = 100; // X-koordinat
$y = 100; // Y-koordinat
$størrelse = 20; // Størrelsen på krysset

// Opprett bilde fra eksisterende PNG-fil
$image = imagecreatefrompng($skjema);

// Alloker en farge til krysset (her: svart)
$kryssfarge = imagecolorallocate($image, 0, 0, 0);

// Tegn krysset
imageline($image, $x, $y, $x + $størrelse, $y + $størrelse, $kryssfarge);
imageline($image, $x, $y + $størrelse, $x + $størrelse, $y, $kryssfarge);

// Lagre bildet eller send til nettleseren
header('Content-Type: image/png');
imagepng($image);

// Frigjør minne
imagedestroy($image);

?>

/*
$_POST['typea0']	
Array
(
[0] => Svært ofte
[1] => Svært ofte
[2] => Svært ofte
[3] => Svært ofte
[4] => Svært ofte
[5] => Svært ofte
)
$_POST['typea1']	
Array
(
[0] => Svært ofte
[1] => Ofte
[2] => Ofte
[3] => Svært ofte
[4] => Svært ofte
[5] => Svært ofte
)
$_POST['typea2']	
Array
(
[0] => Ofte
[1] => Ofte
[2] => Svært ofte
[3] => Svært ofte
[4] => Svært ofte
[5] => Svært ofte
)
*/
