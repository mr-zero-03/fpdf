<?php
require('fpdf.php');

$pdf = new FPDF('L', 'mm','Letter');
$pdf->AddPage();
$pdf->SetFont('Arial','I',25);
$pdf->Cell(80, 10, null, 1);
$pdf->Cell(100, 10,'ABCDEFGHIJKLMNOPQRSTUVXYZ!', 1, null, 'L', false, 'www.google.com'	);
$pdf->Cell(80, 10, null, 1);
$pdf->Ln(30);
$pdf->SetFont('Arial','BIU',16);
$pdf->Cell(42, 10,'OTRO STRING', 1);

$pdf->AddPage('P', 'A4', 270);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'PAGE 2!');
$pdf->Output();
?>
