<?php

include_once ('../fpdf.php');

class PDF extends FPDF {

  function hr( $ln = 10 ) {
    $this->Ln( $ln );
    $this->printCell( 0, 0, null, 'T' );
	  $this->Ln();
  }


  private $defaultFamily = null;
  private $defaultStyle = null;
  private $defaultSize = null;

  function SetFont( $family, $style = '', $size = 0 ) {
    $this->defaultFamily = $family;

    parent::SetFont( $family, $style, $size );
  }


  /**
  *    printCell method:
  * Receives all the Cell parameters and make an utf8_decode to the text.
  * http://www.fpdf.org/en/doc/cell.htm
  * Additional parameters are:
  *  $lnBefore = Receives a number (int or float) to specify the size of the line break before the text to print (if you send true the value equals the height of the last printed cell)
  *  font parameters = Family, Style and Size
  */
  function printCell( $width = 0, $height	= 0, $text = '', $border = 0, $ln = 0, $align = 'L', $fill = false, $link = null, $lnBefore = null, $fontFamily = null, $fontStyle = null, $fontSize = null ) {

    if ( $fontFamily === null ) {
      $fontFamily = ( ( $this->defaultFamily === null ) ? 'Arial' : $this->defaultFamily );
    }

    $this->SetFont( $fontFamily, $fontStyle, $fontSize );

    if ( is_numeric( $lnBefore ) || $lnBefore === true ) {
      if ( $lnBefore === true ) { $lnBefore = null; }
      $this->Ln( $lnBefore );
    }

    $textUtf8Decoded = utf8_decode( $text );
    $this->Cell( $width, $height, $textUtf8Decoded, $border, $ln, $align, $fill, $link );
  }


  function ipsData( $ipsDataArray ) {
    $ipsDictionary = [ 'name' => '', 'nit' => 'Nit', 'city' => 'Ciudad', 'adress' => 'Dirección', 'phoneNumber' => 'Teléfono' ];

    $toPrint = '';

    $ipsDictionarySize = count( $ipsDictionary );

    foreach ( $ipsDictionary as $key => $value ) {
      if ( trim ( $value ) !== '' ) {
        $toPrint = $value . ': ';
      }

      $this->printCell( 0, 5, $toPrint . $ipsDataArray[ $key ], null, 1 );

    }

    $this->SetLeftMargin( 10 );
    $this->hr();

  }

  function Header() {
    $ipsDataArray = yaml_parse_file ( 'ips_data.yaml' );
    $this->ipsData( $ipsDataArray );
  }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->Output();
