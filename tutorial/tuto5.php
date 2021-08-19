<?php
require('../fpdf.php');

class PDF extends FPDF {

// Load data
function LoadData($file) {
	// Read file lines
	$lines = file($file);
	$data = array();
	foreach($lines as $line) {
		$data[] = explode(';',trim($line));
	}
	return $data;
}

// Simple table
function BasicTable($header, $data) {
	// Header
	foreach($header as $col) {
		$this->Cell(40,7,$col,1);
	}
	$this->Ln();
	// Data
	foreach($data as $row) {
		foreach($row as $col) {
			$this->Cell(40,6,$col,1);
		}
		$this->Ln();
	}
}

// Better table
function ImprovedTable($header, $data)
{
	// Column widths
	$w = array(40, 35, 40);
	// Header
	$headerCount = count($header);
	for( $i=0; $i < $headerCount; $i++ ) {
		$this->Cell($w[$i],7,$header[$i],1,0,'C');
	}
	$this->Ln();
	// Data
	foreach($data as $row) {
		for ( $i=0; $i < $headerCount; $i++ ) {
			$this->Cell($w[$i],6,$row[$i],'LR');
		}
		$this->Ln();
	}
	// Closing line
	$this->Cell(array_sum($w),0,'','T');
}

// Colored table
function FancyTable($header, $data) {
	// Colors, line width and bold font
	$this->SetFillColor(255,0,0);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,0,0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	// Header
	$width = array();
	$colsCount = count($header);
	$rowsCount = count($data);

  $stringWidth = '';
	for ( $i=0; $i < $colsCount; $i++ ) {  //To set the size of the columns depending on the strings width

	  for ( $j=0; $j < $rowsCount; $j++ ) {
	    $string = $data[$j][$i];
	    $stringWidth = $this->GetStringWidth( $string );
      
      if ( !isset($width[$i]) || $stringWidth > $width[$i] ) {
  	    $width[$i] = $stringWidth;

        $headerStringWidth = $this->GetStringWidth( $header[$i] );
  	    if ( $headerStringWidth > $stringWidth ) {
  	      $width[$i] = $headerStringWidth;
  	    }

	    }
	    
	  }

	}
  
	for( $i=0;$i < $colsCount; $i++ ){
		$this->Cell($width[$i],7,$header[$i],1,0,'C',true);
	}
	$this->Ln();
	// Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	// Data
	$fill = false;

	foreach($data as $row) {
		for ( $i=0; $i < 	$colsCount; $i++ ) {
			$this->Cell($width[$i],6,$row[$i],'LR',0,'L',$fill);
		}
		$this->Ln();
		$fill = !$fill;
	}
	// Closing line
	$this->Cell(array_sum($width),0,'','T');
}
}

$pdf = new PDF();
// Column headings
$header = array('Name', 'Country', 'Age');
// Data loading
$data = $pdf->LoadData('people.txt');
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->BasicTable($header,$data);
$pdf->Ln(20);
$pdf->ImprovedTable($header,$data);
$pdf->Ln(20);
$pdf->FancyTable($header,$data);
$pdf->Output();
?>
