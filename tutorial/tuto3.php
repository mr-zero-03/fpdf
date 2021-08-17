<?php
require('../fpdf.php');

class PDF extends FPDF {

function Header() {
	global $title;

	// Arial bold 15
	$this->SetFont('Arial','B',20);
	// Calculate width of title and position
	$w = $this->GetStringWidth($title)+5;
	$this->SetX((210-$w)/2);
	// Colors of frame, background and text respectively
	$this->SetDrawColor(155, 30, 4);
	$this->SetFillColor(17 ,17, 17);
	$this->SetTextColor(118, 0, 0);
	// Thickness of frame (2 mm)
	$this->SetLineWidth(2);
	// Title
	$this->Cell($w,9,$title,1,1,'C',true);
	// Line break
	$this->Ln(10);
}

function Footer() {
	// Position at 1.5 cm from bottom
	$this->SetY(-15);
	// Arial italic 8
	$this->SetFont('Arial','I',8);
	// Text color in gray
	$this->SetTextColor(128);
	// Page number
	$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}

function ChapterTitle($chapterNum, $chapterTitle) {
	// Arial 12
	$this->SetFont('Arial','BI',12);
	// Background color
	$this->SetFillColor(245, 51, 51);
	// Title
	$this->Cell(0, 6, "Chapter $chapterNum : $chapterTitle", 0, 1, 'L', true);
	// Line break
	$this->Ln(4);
}

function ChapterBody($chapterText) {
	// Read text file
	$txt = file_get_contents($chapterText);
	
	$bold = false;  // Parsing the text to use bold
	if ( $txt[0] === '*' ) {
	  $bold = true;
	}
	$txtArray = explode('*', $txt);

  $countTxtArray = count( $txtArray );
  for ( $i=0; $i < $countTxtArray; $i++ ) {
   if ( $bold ) {
      $this->SetFont('Times','B',12);
      $this->Write( 5, $txtArray[$i] );
      $bold = false;
    } else {
      $this->SetFont('Times','',12);
   	  $this->Write( 5, $txtArray[$i] );
      $bold = true;
    }
  }
  
	// Line break
	$this->Ln();
	// Mention in italics
	$this->SetFont('','I');
	$this->Cell(0,10,'(end of excerpt)');

	$y = $this->GetY();
	$y += 10;
	$this->Image('tuto3.jpg', 10, $y, 190, 0, 'jpg');
}

function PrintChapter($chapterNum, $chapterTitle, $chapterText) {
	$this->AddPage();
	$this->ChapterTitle($chapterNum, $chapterTitle);
	$this->ChapterBody($chapterText);
}
}


$pdf = new PDF();

$title = '1984';
$pdf->SetTitle($title);
$pdf->SetAuthor('George Orwell');

$pdf->PrintChapter(1,'War is Peace, Freedom is Slavery, Ignorance is Strength','c1-1984.txt');
$pdf->PrintChapter(2,'Thoughtcrime','c2-1984.txt');

$pdf->Output();
?>
