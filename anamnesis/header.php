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
  * Additional parameters are received in the $options array:
  *  $options[ 'type' ] = Recieves a type (you can see and modify them on the printCellType method)
  *  $options[ 'lnBefore' ] = Receives a number (int or float) to specify the size of the line break before the text to print (if you send true the value equals the height of the last printed cell)
  *  Font parameters = $options[ 'fontFamily' ], $options[ 'fontStyle' ], $options[ 'fontSize' ]
  */
  function printCell( $options = [ 'lnBefore' => null, 'fontFamily' => null, 'fontStyle' => null, 'fontSize' => null ],
    $width = null, $height = null, $text = null, $border = null, $ln = null, $align = null, $fill = null, $link = null ) {

    $printCellParameters = get_defined_vars();
    if ( $printCellParameters[ 'options' ][ 'fontFamily' ] === null ) {
      $printCellParameters[ 'options' ][ 'fontFamily' ] = ( ( $this->defaultFamily === null ) ? 'Arial' : $this->defaultFamily );
    }

    $this->SetFont( $printCellParameters[ 'options' ][ 'fontFamily' ], $printCellParameters[ 'options' ][ 'fontStyle' ], $printCellParameters[ 'options' ][ 'fontSize' ] );

    if ( is_numeric( $printCellParameters[ 'options' ][ 'lnBefore' ] ) || $printCellParameters[ 'options' ][ 'lnBefore' ] === true ) {
      if ( $printCellParameters[ 'options' ][ 'lnBefore' ] === true ) { $printCellParameters[ 'options' ][ 'lnBefore' ] = null; }
      $this->Ln( $printCellParameters[ 'options' ][ 'lnBefore' ] );
    }

    $textUtf8Decoded = utf8_decode( $text );
    $this->Cell( $printCellParameters[ 'width' ], $printCellParameters[ 'height' ], $textUtf8Decoded, $printCellParameters[ 'border' ], $printCellParameters ['ln'], $printCellParameters['align'], $printCellParameters[ 'fill' ], $printCellParameters[ 'link' ] );
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
        $options = array( 'lnBefore' => $value[ 'lnBefore' ], 'fontFamily' => $value[ 'fontFamily' ], 'fontStyle' => $value[ 'fontStyle' ], 'fontSize' => $value[ 'fontSize' ] );
        $this->printCell( $options, 0, 5, $toPrint . $dataArray[ $key ], $value[ 'border' ], $value[ 'ln' ], $value[ 'align' ], $value[ 'fill' ], $value[ 'link' ] );

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
