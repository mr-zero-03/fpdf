<?php

include_once ('../fpdf.php');

class PDF extends FPDF {

  function ipsData( $ipsDataArray ) {

    $this->Image( $ipsDataArray[ 'logo' ], 10, 10, 50, 30 );

    $this->SetLeftMargin( 65 );
    $this->SetFont( 'Arial', 'B', 16 );
    $this->Cell( 0, 5, $ipsDataArray[ 'name' ] );

    $this->Ln( 8 );
    $this->SetFont( 'Arial', '', 10 );
    $this->Cell( 0, 5, 'Nit: ' . $ipsDataArray[ 'nit' ], null, 1 );

    $this->Cell( 0, 5, 'Ciudad: ' . $ipsDataArray[ 'city' ], null, 1 );

    $this->Cell( 0, 5, 'Dirección: ' . $ipsDataArray[ 'adress' ], null, 1 );

    $this->Cell( 1, 5, 'Teléfono: ' . $ipsDataArray[ 'phoneNumber' ] );

    $this->SetX( -70 );
    $this->Cell( 0, 5, 'Número de impresión: aaaa', 1);

    $this->SetLeftMargin( 10 );
    $this->Ln(10);
    $this->Cell( 0, 5, null, 'T' );
  }

  function Header() {
    global $ipsDataArray;
    $this->ipsData( $ipsDataArray );
  }

}

$ipsDataArray = array(
  'logo' => 'logo.png',
  'name' => 'EL OJO QUE TODO LO VE',
  'nit' => '123456789',
  'city' => 'Ibagué',
  'adress' => 'Cra 1 N 2-3',
  'phoneNumber' => 987654
);

$pdf = new PDF();
$pdf->AddPage();
$pdf->Output();
