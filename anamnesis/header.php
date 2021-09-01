<?php

include_once ('../fpdf.php');

class PDF extends FPDF {

  function hr( $ln = 10 ) {
    $this->Ln( $ln );
    $this->Cell( 0, 0, null, 'T' );
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


  function fillDictionaryArray( $array, $type ) {
    $dictionary = array();
    $dictionary[ 'cell' ] = array ( 'name' => '', 'lnBefore' => null, 'fontFamily' => null, 'fontStyle' => null, 'fontSize' => null, 'width' => null, 'height' => null, 'border' => null, 'ln' => null, 'align' => 'L', 'fill' => null, 'link' => null );

    foreach ( $dictionary[ $type ] as $key => $value ) {
      if ( !isset( $array[ $key ] ) ) {
        $array[ $key ] = null;
      }
    }

    return ( $array );

  }

  function printHeader( $dataArray, $dictionary ) {
    $toPrint = '';

    foreach ( $dictionary as $key => $value ) {
      if ( $value[ 'type' ] !== 'image' ) {

        $value = $this->fillDictionaryArray( $value, 'cell' );

        if ( trim ( $value[ 'name' ] ) !== '' ) {
          $toPrint = $value[ 'name' ] . ': ';
        }

        $this->printCell( 0, 5, $toPrint . $dataArray[ $key ], $value[ 'border' ], $value[ 'ln' ], $value[ 'align' ], $value[ 'fill' ], $value[ 'link' ], $value[ 'lnBefore' ], $value[ 'fontFamily' ], $value[ 'fontStyle' ], $value[ 'fontSize' ] );

      } else {
        $this->Image( $dataArray[ 'logo' ], $value[ 'x' ], $value[ 'y' ], $value[ 'width' ], $value[ 'height' ] );
        $this->SetLeftMargin( 65 );
      }

    }

    $this->SetLeftMargin( 10 );
    $this->hr();

  }

  function Header() {
    $ipsDataArray = yaml_parse_file ( 'ips_data.yaml' );
    $ipsDictionary = [
      'logo' => array ( 'type' => 'image', 'fileName' => 'logo.png', 'x' => 10, 'y' => 10, 'width' => 50, 'height' => 30, 'imageFormat' => null, 'link' => null ),
      'name' => array ( 'type' => 'text', 'name' => '', 'align'=>'L', 'ln' => 1 ),
      'nit' => array ( 'type' => 'text', 'name' => 'Nit', 'ln' => 1 ),
      'city' => array ( 'type' => 'text', 'name' => 'Ciudad', 'ln' => 1 ),
      'adress' => array ( 'type' => 'text', 'name' => 'Dirección', 'ln' => 1 ),
      'phoneNumber' => array ( 'type' => 'text', 'name' => 'Teléfono' )
    ];
    $this->printHeader( $ipsDataArray, $ipsDictionary );
  }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->Output();
