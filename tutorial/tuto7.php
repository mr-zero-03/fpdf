<?php
define('FPDF_FONTPATH','.');
require('../fpdf.php');

$pdf = new FPDF();
$pdf->AddFont('Roboto','','Roboto-Black.php');
$pdf->AddPage();
$pdf->SetFont('Roboto','',35);
$pdf->Cell(0,10,'Enjoy new fonts with FPDF!');
$pdf->SetFont('Roboto','',12);
$pdf->Ln(20);
$pdf->Cell(0,10,'Now I am using Roboto');
$pdf->Output();
?>
