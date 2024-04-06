<?php

require_once '/home/vds/www/vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;


$skjema = '/usr/share/php/ASRS_1-1_Page_2.png';

// Koordinater hvor krysset skal plasseres
$x = 100; // X-koordinat
$y = 100; // Y-koordinat
$størrelse = 40; // Størrelsen på krysset

// Opprett bilde fra eksisterende PNG-fil
$image = imagecreatefrompng($skjema);

// Alloker en farge til krysset (her: svart)
$kryssfarge = imagecolorallocate($image, 0, 0, 0);



// Startposisjoner for svarfeltene
$startX = 1404; // Start X-posisjon for det første spørsmålet
$startY = 578; // Start Y-posisjon for det første spørsmålet
$deltaY = 40;  // Avstanden vertikalt mellom hvert svarfelt

// Avstanden horisontalt mellom hver kolonne basert på skjemaet
$avstandKolonne = 50;

// Størrelse på krysset
$kryssStørrelse = 10;

// Definer svaralternativene som tilsvarer kolonner i skjemaet
$svarAlternativer = ['Aldri', 'Sjelden', 'I blant', 'Ofte', 'Svært ofte'];

// Gå gjennom hver $_POST-array og tegn et kryss basert på svaret
foreach ($_POST as $key => $typeArray) {
  if (strpos($key, 'typea') === 0) { // Sjekk om nøkkelen begynner med 'typea'
    $yPos = $startY + (intval(substr($key, 5)) * $deltaY); // Beregn Y-posisjon basert på spørsmålsnummeret

    foreach ($typeArray as $index => $svar) {
      $xPos = $startX + (array_search($svar, $svarAlternativer) * $avstandKolonne); // Beregn X-posisjon basert på svaralternativet

      // Tegn krysset
      imageline($image, $xPos, $yPos, $xPos + $kryssStørrelse, $yPos + $kryssStørrelse, $kryssfarge);
      imageline($image, $xPos, $yPos + $kryssStørrelse, $xPos + $kryssStørrelse, $yPos, $kryssfarge);

      $yPos += $deltaY; // Gå til neste linje
    }
  }
}




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
