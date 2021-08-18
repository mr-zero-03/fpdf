<?php
require('../fpdf.php');

class PDF extends FPDF
{
protected $col = 0; // Current column
protected $y0;   // Ordinate of column start

function Header() {
	// Page header
	global $title;

	$this->SetFont('Arial','B',15);
	$w = $this->GetStringWidth($title)+50;
	$this->SetX((210-$w)/2);
	$this->SetDrawColor(155, 30, 4);
	$this->SetFillColor(17, 17, 17);
	$this->SetTextColor(118, 0, 0);
	$this->SetLineWidth(1);
	$this->Cell($w,9,$title,1,1,'C',true);
	$this->Ln(10);
	$this->y0 = $this->GetY();
}

function Footer() {
	// Page footer
	$this->SetY(-15);
	$this->SetFont('Arial','I',8);
	$this->SetTextColor(128);
	$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}

function SetCol($col) {
	// Set position at a given column
	$this->col = $col;
	$x = 10+$col*40;
	$this->SetLeftMargin($x);
	$this->SetX($x);
}

function AcceptPageBreak() {
	// Method accepting or not automatic page break
	if($this->col<4) {
		// Go to next column
		$this->SetCol($this->col+1);
		// Set ordinate to top
		$this->SetY($this->y0); //39
		// Keep on page
		return false;
	}	else {
		// Go back to first column
		$this->SetCol(0);
		// Page break
		return true;
	}
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
	//Save the point where have to start the chapter text
	$this->y0 = $this->GetY();
}

function ChapterBody($chapterText) {
	// Read text file
	$txt = file_get_contents($chapterText);
	// Font
	$this->SetFont('Times','',12);
	// Output text in a 3 cm width column
  $this->MultiCell(30,5, $txt);
	$this->Ln();
	// Mention
	$this->SetFont('','I');
	$this->Cell(0,5,'(end of excerpt)');
	// Go back to first column
	$this->SetCol(0);
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
