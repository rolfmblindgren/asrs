<?php

require_once '/home/vds/www/vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;


$skjema = '/usr/share/php/ASRS_1-1_Page_2.png';

// Opprett bilde fra eksisterende PNG-fil
$image = imagecreatefrompng($skjema);

// Alloker en farge til krysset (her: svart)
$kryssfarge = imagecolorallocate($image, 0, 0, 0);



// Startposisjoner for svarfeltene
$baseStartX = 1191; // Start X-posisjon for det første spørsmålet
$baseStartY = 578; // Start Y-posisjon for det første spørsmålet
$deltaY = 80;  // Avstanden vertikalt mellom hvert svarfelt
$deltaX = 76;

// Avstanden horisontalt mellom hver kolonne basert på skjemaet
$avstandKolonne = 82;

// Størrelse på krysset
$kryssStørrelse = 10;

// Definer svaralternativene som tilsvarer kolonner i skjemaet
$svarAlternativer = ['Aldri', 'Sjelden', 'I blant', 'Ofte', 'Svært ofte'];

// Beregner total høyde for hver array for å justere startposisjonen vertikalt
$totalYOffset = 0;

// Gå gjennom hver $_POST-array og tegn et kryss basert på svaret
foreach ($_POST as $key => $typeArray) {
  if (strpos($key, 'typea') === 0) { // Sjekk om nøkkelen begynner med 'typea'
    // Beregn start Y-posisjon for denne gruppen
    $startY = $baseStartY + $totalYOffset;

    foreach ($typeArray as $index => $svar) {
      // Beregn X-posisjon basert på svaralternativet
      $xPos = $baseStartX + (array_search($svar, $svarAlternativer) * $avstandKolonne);
      // Beregn Y-posisjon for dette svaret i gruppen
      $yPos = $startY + ($index * $deltaY);

      // Tegn krysset
      imageline($image, $xPos, $yPos, $xPos + $kryssStørrelse, $yPos + $kryssStørrelse, $kryssfarge);
      imageline($image, $xPos, $yPos + $kryssStørrelse, $xPos + $kryssStørrelse, $yPos, $kryssfarge);
    }

    // Oppdater totalYOffset for neste gruppe
    $totalYOffset += $deltaY * count($typeArray);
  }
}

// Sørg for at ingen annen output er sendt før header
ob_clean();

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
