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
$deltaY = 81.5;  // Avstanden vertikalt mellom hvert svarfelt
$deltaX = 82;

// Størrelse på krysset
$kryssStørrelse = 10;

// Definer svaralternativene som tilsvarer kolonner i skjemaet
$svarAlternativer = ['Aldri', 'Sjelden', 'I blant', 'Ofte', 'Svært ofte'];

// Beregner total høyde for hver array for å justere startposisjonen vertikalt
$totalYOffset = 0;
$spørsmålTeller = 0;  // Holder styr på antall spørsmål behandlet

// Anta at vi legger til ekstra vertikal plass etter det sjette spørsmålet
$ekstraLuftEtterSeks = 40;  // Størrelsen på den ekstra plassen

// Gå gjennom hver $_POST-array og tegn et kryss basert på svaret
foreach ($_POST as $key => $typeArray) {
  if (strpos($key, 'typea') === 0) { // Sjekk om nøkkelen begynner med 'typea'
    
    foreach ($typeArray as $index => $svar) {
      if ($spørsmålTeller === 6) { // Etter det sjette spørsmålet, legg til ekstra luft
        $totalYOffset += $ekstraLuftEtterSeks;
      }

      // Beregn Y-posisjon for dette svaret i gruppen
      $yPos = $baseStartY + $totalYOffset + ($index * $deltaY);

      // Beregn X-posisjon basert på svaralternativet
      $xPos = $baseStartX + (array_search($svar, $svarAlternativer) * $deltaX);

      // Tegn krysset
      imageline($image, $xPos - $kryssStørrelse, $yPos - $kryssStørrelse, $xPos, $yPos, $kryssfarge);
      imageline($image, $xPos - $kryssStørrelse, $yPos, $xPos, $yPos - $kryssStørrelse, $kryssfarge);

      // Øk spørsmålTeller med 1
      $spørsmålTeller++;
    }

    // Oppdater totalYOffset for neste gruppe spørsmål
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
