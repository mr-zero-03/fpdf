<?php

include_once ('../fpdf.php');

class PDF extends FPDF {

  /**
  *    printCell method:
  * Receives all the Cell parameters and make an utf8_decode to the text.
  * http://www.fpdf.org/en/doc/cell.htm
  */
  function printCell( $width = 0, $height = 0, $text = '', $border = 0, $ln = 0, $align = 'L', $fill = false, $link = null ) {
    $textUtf8Decoded = utf8_decode( $text );
    $this->Cell( $width, $height, $textUtf8Decoded, $border, $ln, $align, $fill, $link );
  }


  function ipsData( $ipsDataArray ) {
    $ipsDictionary = [ 'name' => '', 'nit' => 'Nit', 'city' => 'Ciudad', 'adress' => 'Dirección', 'phoneNumber' => 'Teléfono' ];

    $this->Ln( 8 );
    $this->SetFont( 'Arial', '', 10 );

    $toPrint = '';

    $ipsDictionarySize = count( $ipsDictionary );

    foreach ( $ipsDictionary as $key => $value ) {
      if ( trim ( $value ) !== '' ) {
        $toPrint = $value . ': ';
      }

      $this->printCell( 0, 5, $toPrint . $ipsDataArray[ $key ], null, 1 );

    }

    $this->SetLeftMargin( 10 );
    $this->Ln( 10 );
    $this->printCell( 0, 0, null, 'T' );
   
  }

  function Header() {
    $ipsDataArray = yaml_parse_file ( 'ips_data.yaml' );
    $this->ipsData( $ipsDataArray );
  }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->Output();
