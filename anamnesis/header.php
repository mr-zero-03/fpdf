<?php

include_once ('../fpdf.php');

function nullToTheArray( &$array ) {
  $array = null;
}

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


  function cellType( $type, $printCellArgs ) {

    $cellType = $printCellArgs;
    array_walk_recursive ( $cellType, 'nullToTheArray' );

    switch ( $type ) {      //Works for general groups of types
      case 'bigTitle': case 'mediumTitle': case 'smallTitle': //Titles
        $cellType[ 'options' ][ 'fontStyle' ] = 'B';
      break;
      case 'bigText': case 'mediumText': case 'smallTitle':  //Text
        $cellType[ 'options' ][ 'fontStyle' ] = '';
      break;
    }

    switch ( $type ) {      //Works for specific types
      case 'bigTitle':  //Titles
        $cellType[ 'align' ] = 'C';
        $cellType[ 'ln' ] = 1;
        $cellType[ 'options' ][ 'fontSize' ] = 16;
      break;

      case 'mediumTitle':
        $cellType[ 'options' ][ 'fontSize' ] = 10;
      break;

      case 'smallTitle':
        $cellType[ 'options' ][ 'fontSize' ] = 4;
      break;


      case 'bigText':  //Texts
        $cellType[ 'options' ][ 'fontSize' ] = 12;
      break;

      case 'mediumText':
        $cellType[ 'options' ][ 'fontSize' ] = 10;
      break;

      case 'smallText':
        $cellType[ 'options' ][ 'fontSize' ] = 7;
      break;
    }


    foreach ( $printCellArgs as $key => $value ) {

      if ( $key === 'options' ) {
        foreach ( $printCellArgs[ 'options' ] as $optionsKey => $optionsValue ) {
          if ( $printCellArgs[ 'options' ][ $optionsKey ] === null ) {
            $printCellArgs[ 'options' ][ $optionsKey ] = $cellType[ 'options' ][ $optionsKey ];
          }
        }
      }

      if ( $printCellArgs[ $key ] === null ) {
        $printCellArgs[ $key ] = $cellType[ $key ];
      }
    }

    return ( $printCellArgs );
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
  function printCell( $options = [ 'type' => null, 'lnBefore' => null, 'fontFamily' => null, 'fontStyle' => null, 'fontSize' => null ],
    $width = null, $height = null, $text = null, $border = null, $ln = null, $align = null, $fill = null, $link = null ) {

    $printCellParameters = get_defined_vars();
    if ( $printCellParameters[ 'options' ][ 'fontFamily' ] === null ) {
      $printCellParameters[ 'options' ][ 'fontFamily' ] = ( ( $this->defaultFamily === null ) ? 'Arial' : $this->defaultFamily );
    }

    if ( $options[ 'type' ] !== null ) {
      $printCellParameters = $this->cellType( $options[ 'type' ], $printCellParameters );
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
    $dictionary[ 'cell' ] = array ( 'name' => '', 'type' => 'bigTitle', 'lnBefore' => null, 'fontFamily' => null, 'fontStyle' => null, 'fontSize' => null, 'width' => null, 'height' => null, 'border' => null, 'ln' => null, 'align' => 'L', 'fill' => null, 'link' => null );

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
        $options = array( 'type' => $value[ 'type' ], 'lnBefore' => $value[ 'lnBefore' ], 'fontFamily' => $value[ 'fontFamily' ], 'fontStyle' => $value[ 'fontStyle' ], 'fontSize' => $value[ 'fontSize' ] );
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
      'name' => array ( 'type' => 'bigTitle', 'name' => '', 'align'=>'L', 'ln' => 1 ),
      'nit' => array ( 'type' => 'mediumText', 'name' => 'Nit', 'ln' => 1 ),
      'city' => array ( 'type' => 'mediumText', 'name' => 'Ciudad', 'ln' => 1 ),
      'adress' => array ( 'type' => 'mediumText', 'name' => 'Dirección', 'ln' => 1 ),
      'phoneNumber' => array ( 'type' => 'mediumText', 'name' => 'Teléfono' )
    ];
    $this->printHeader( $ipsDataArray, $ipsDictionary );
  }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->Output();
