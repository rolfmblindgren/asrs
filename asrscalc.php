<?php

require_once '/home/vds/www/vendor/autoload.php';
require_once '/home/vds/www/vendor/tecnickcom/tcpdf/tcpdf.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;


$forside = '/usr/share/php/ASRS_1-1_Page_1.png';
$bakside = '/usr/share/php/ASRS_1-1_Page_3.png';
$skjema = '/usr/share/php/ASRS_1-1_Page_2.png';

// Opprett bilde fra eksisterende PNG-fil
$image = imagecreatefrompng($skjema);

// Alloker en farge til krysset (her: svart)
$kryssfarge = imagecolorallocate($image, 64, 224, 208);

// Sett tykkelsen på linjene som skal tegnes
$linjeTykkelse = 5;
imagesetthickness($image, $linjeTykkelse);


// Startposisjoner for svarfeltene
$baseStartX = 1196; // Start X-posisjon for det første spørsmålet
$baseStartY = 579; // Start Y-posisjon for det første spørsmålet
$deltaY = 82.5;  // Avstanden vertikalt mellom hvert svarfelt
$deltaX = 83;

// Størrelse på krysset
$kryssStørrelse = 20;

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

// Opprett et nytt PDF-dokument
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Sett dokumentinformasjon
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Ditt Navn');
$pdf->SetTitle('Skjema PDF');
$pdf->SetSubject('Skjema med Kryss');

// Fjern standard header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Legg til forside
$pdf->AddPage();
$pdf->Image($forside, 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, true);

// Lagre GD-bildet ($image) til en midlertidig fil
$tmpBilde = tempnam(sys_get_temp_dir(), 'gd_img') . '.png';
imagepng($image, $tmpBilde); // Lagre bildet som PNG
imagedestroy($image); // Frigjør minne

// Legg til skjema (GD-bildet) som side 2
$pdf->AddPage();
$pdf->Image($tmpBilde, 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, true);

// Slett den midlertidige bildfilen
unlink($tmpBilde);

// Legg til bakside
$pdf->AddPage();
$pdf->Image($bakside, 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, true);

// Lukk og skriv PDF-dokumentet til filsystemet eller send det direkte til nettleseren
$pdf->Output('fullt_skjema.pdf', 'I');

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
