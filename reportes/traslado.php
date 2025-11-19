<?php
//============================================================+
// File name   : requisicion.php
// Description : Formato de Requisición
//============================================================+

// Image method signature:
// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

// Include the main TCPDF library (search for installation path).
require_once "vendor/autoload.php";

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $logo;
	public $empresaId;
	public $empresa;
	public $folio;
	public $fechaCreacion;
	public $maquinaria;
	public $mantenimientoTipo;

	//Page header
	public function Header() {
		
		$extension = mb_strtoupper(substr($this->logo, -3, 3));
		if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen
			// Logo
        $this->Image($this->logo, 6, 5, 50, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
        $this->SetFillColor(126, 194, 36);
        $this->setFont('helvetica', 'B', 14); // Fuente, Tipo y Tamaño
        $this->MultiCell(110, 20, "REPORTE DE TRASLADO", 1, 'C', 1, 0, 60, 7, true, 0, false, true, '20', 'M');
        $this->setFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
        $this->MultiCell(30, 5, "REPORTE", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(255, 0, 0);
        $this->setFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
        $this->MultiCell(30, 15, "{$this->folio}", 1, 'C', 1, 1, 170, '', true, 0, false, true, '15', 'M');
        
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

if ( is_null($empresa->imagen) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
else $pdf->logo = Route::rutaServidor().$empresa->imagen;
$pdf->folio = $traslado->requisicionFolio;
// set document information
$pdf->setTitle("{$traslado->requisicionFolio}");
// remove default header/footer
$pdf->setPrintFooter(false);

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->setCellPaddings(1, 1, 1, 1); // set cell padding
// $this->setCellMargins(1, 1, 1, 1); // set cell margins

$pdf->AddPage(); // Agregar nueva página
$nombreOperador = mb_strtoupper($operador->nombreCompleto);
$pdf->SetXY(10, 35);
$pdf->SetFont('helvetica', 'B', 7.8); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "OPERADOR", 1, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');
$pdf->SetFont('helvetica', 'B', 7.8); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(90, 5, "{$nombreOperador}", 1, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->setFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(60, 20, "COMBUSTIBLE SALIDA", 1, 'C', 1, 1, '', '', true, 0, false, true, '20', 'M');
$pdf->MultiCell(60, 20, "COMBUSTIBLE LLEGADA", 1, 'C', 1, 0, 140, 80, true, 0, false, true, '20', 'M');

$pdf->SetFont('helvetica', 'B', 7.8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(40, 5, "RUTA DE MOVIMIENTO", 1, 'C', 1, 0, 10, 40, true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(90, 5, "{$traslado->ruta}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M',1);
$pdf->rect(140, 35, 60, 89, 'D');	

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "FECHA", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(90, 5, "{$traslado->fecha}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "KM INICIAL", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 5, "{$traslado->kmInicial}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "KM FINAL", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 5, "{$traslado->kmFinal}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "KILOMETRAJE RECORRIDO", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 5, "{$traslado->kmRecorrido}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "COMBUSTIBLE INICIAL", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 5, "{$traslado->combustibleInicial}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "COMBUSTIBLE FINAL", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 5, "{$traslado->combustibleFinal}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "COMBUSTIBLE GASTADO", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 5, "{$traslado->combustibleGastado}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "RENDIMIENTO TEORICO", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 5, "{$traslado->rendimientoTeorico}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(40, 5, "RENDIMIENTO REAL", 1, 'C', 1, 0, 10, '', true, 0, false, true, '5', 'M');
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(45, 5, "{$traslado->rendimientoReal}", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->MultiCell(45, 5, "KM", 1, 'C', 0, 1, 95, 51, true, 0, false, true, '5', 'M');
$pdf->MultiCell(45, 5, "KM", 1, 'C', 0, 1, 95, '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(45, 5, "LT", 1, 'C', 0, 1, 95, '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(45, 5, "LT", 1, 'C', 0, 1, 95, '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(45, 5, "LT", 1, 'C', 0, 1, 95, '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(45, 5, "KM / L", 1, 'C', 0, 1, 95, '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(45, 5, "KM / L", 1, 'C', 0, 1, 95, '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(45, 5, "KM / L", 0, 'C', 0, 1, 95, '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(45, 43.5, "", 1, 'C', 1, 1, 95, 50.5, true, 0, false, true);


$pdf->SetFillColor(126, 194, 36);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', 'B', 7.2); // Fuente, Tipo y Tamaño
$pdf->MultiCell(130, 4, "CALCULO DE RENDIMIENTO DE COMBUSTIBLE", 1, 'C', 1, 1, 10, '', true, 0, false, true, '4', 'M');

$pdf->Rect(10, 99, 130, 25, 'D');

$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(130, 8, "RENDIMIENTO ( R ) = ( KM RECORRIDO (____KM____) / COMBUSTIBLE GASTADO (___LTS___) \n RENDIMIENTO= ___2.17___KM / LTS", 0, 'C', 1, 1, 10, '', true, 0, false, true, '8', 'M');

$pdf->MultiCell(32, 8, "1 / 4 = 213 LTS", 0, 'C', 1, 0, 10, '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(32, 8, "1 / 2 = 426 LTS", 0, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(32, 8, "3 / 4 = 639 LTS", 0, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(32, 8, "1 Tanque = 852 LTS", 0, 'C', 1, 1, '', '', true, 0, false, true, '8', 'M');

$pdf->MultiCell(130, 8, "PARA CALCULAR LOS LITROS DEL NIVEL DE COMBUSTIBLE DE MI TANQUE, ES NECESARIO SABER QUE CADA RALLA DE
MI INDICADOR DE COMBUSTIBLE EN EL TABLERO DE INSTRUMENTOS EQUIVALE A 106.5 LTS", 0, 'C', 1, 1, 10, '', true, 0, false, true, '8', 'M',1);

$pdf->SetFont('helvetica', 'B', 10.44); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(255, 255, 0);
$pdf->MultiCell(140, 9, "RESUMEN DE GASTOS ", 1, 'C', 1, 0, 10, '', true, 0, false, true, '9', 'M');
$pdf->SetFont('helvetica', 'B', 9.12); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(126, 194, 36);
$numeroEconomico = mb_strtoupper($maquinaria->numeroEconomico);
$pdf->MultiCell(30, 9, "{$numeroEconomico}", 1, 'C', 1, 0, '', '', true, 0, false, true, '9', 'M');
$pdf->SetFont('helvetica', 'B', 7.8); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(20, 9, "DEPOSITO \n $ {$traslado->deposito}", 1, 'C', 1, 1, '', '', true, 0, false, true, '9', 'M');

$pdf->Ln(3); // Salto de Línea
$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(45, 7, "DEDUCIBLE", 1, 'C', 1, 1, 10, '', true, 0, false, true, '7', 'M');
$pdf->SetFillColor(126, 194, 36);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(15, 8, "PARTIDA", 1, 'C', 1, 0, 10, '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(15, 8, "FECHA", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(60, 8, "PROVEEDOR", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(15, 8, "FOLIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(20, 8, "TOTAL", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(65, 8, "DESCRIPCION", 1, 'C', 1, 1, '', '', true, 0, false, true, '8', 'M');

$pdf->setX(20);
$totalDeducible = 0.00;
foreach($deducibles as $key => $detalle) {
	$totalDeducible += $detalle['total'];
    $proveedor = mb_strtoupper($detalle['proveedor']);
	$total = number_format($detalle['total'], 2);
	$folio = mb_strtoupper($detalle['folio']);
	$descripcion = mb_strtoupper($detalle['descripcion']);

	$partida = $key + 1;

	$y_start = $pdf->GetY();
	if ( $y_start > 223 && $partida == count($deducibles) ) {
		$pdf->AddPage();

		$pdf->SetXY(10, 51);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->SetFillColor(12, 34, 63);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->MultiCell(45, 7, "DEDUCIBLE", 1, 'C', 1, 1, 10, '', true, 0, false, true, '7', 'M');
		$pdf->SetFillColor(126, 194, 36);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->MultiCell(15, 8, "PARTIDA", 1, 'C', 1, 0, 10, '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(15, 8, "FECHA", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(60, 8, "PROVEEDOR", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(15, 8, "FOLIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(20, 8, "TOTAL", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(65, 8, "DESCRIPCION", 1, 'C', 1, 1, '', '', true, 0, false, true, '8', 'M');

		$y_start = $pdf->GetY();
	}
	$pdf->SetFont('helvetica', '', 5.5); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(65, 3, "{$descripcion}", 1, 'C', 0, 1, 135, '', true);
	$y_end = $pdf->GetY();
	$altoFila = $y_end - $y_start;
	$pdf->MultiCell(15, $altoFila, "{$partida}", 1, 'C', 0, 0, 10, $y_start, true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(15, $altoFila, "", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(60, $altoFila, "{$proveedor}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M',1);
	$pdf->MultiCell(15, $altoFila, "{$folio}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "$ {$total}", 1, 'R', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');

	if ( $y_end > 270 ) {
		$pdf->AddPage();

		$pdf->SetXY(15, 51);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->SetFillColor(12, 34, 63);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->MultiCell(45, 7, "DEDUCIBLE", 1, 'C', 1, 1, 10, '', true, 0, false, true, '7', 'M');
		$pdf->SetFillColor(126, 194, 36);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->MultiCell(15, 8, "PARTIDA", 1, 'C', 1, 0, 10, '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(15, 8, "FECHA", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(60, 8, "PROVEEDOR", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(15, 8, "FOLIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(20, 8, "TOTAL", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(65, 8, "DESCRIPCION", 1, 'C', 1, 1, '', '', true, 0, false, true, '8', 'M');

		$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	}
}

$y = $pdf->getY();
if ( $y > 228 ) {
	$pdf->AddPage();

	$pdf->SetXY(10, 51);
	$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
	$pdf->SetFillColor(12, 34, 63);
	$pdf->SetTextColor(255, 255, 255);
	$pdf->MultiCell(45, 7, "DEDUCIBLE", 1, 'C', 1, 1, 10, '', true, 0, false, true, '7', 'M');
	$pdf->SetFillColor(126, 194, 36);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->MultiCell(15, 8, "PARTIDA", 1, 'C', 1, 0, 10, '', true, 0, false, true, '8', 'M');
	$pdf->MultiCell(15, 8, "FECHA", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
	$pdf->MultiCell(60, 8, "PROVEEDOR", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
	$pdf->MultiCell(15, 8, "FOLIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
	$pdf->MultiCell(20, 8, "TOTAL", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
	$pdf->MultiCell(65, 8, "DESCRIPCION", 1, 'C', 1, 1, '', '', true, 0, false, true, '8', 'M');

	$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	$y = $pdf->getY();
}

while ($y <= 220) {
	
	$pdf->MultiCell(15, 5, "", 1, 'C', 0, 0, 10, '', true, 0, false, true, '5', 'M');
	$pdf->MultiCell(15, 5, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '5', 'M');
	$pdf->MultiCell(60, 5, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '5', 'M');
	$pdf->MultiCell(15, 5, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '5', 'M');
	$pdf->MultiCell(20, 5, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '5', 'M');
	$pdf->MultiCell(65, 5, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '5', 'M');

    $y = $pdf->getY();
}
$FormatedtotalDeducible = number_format($totalDeducible, 2);
$pdf->SetFont('helvetica', 'B', 7.8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(15, 6, "TOTAL", 1, 'C', 0, 0, 100, '', true, 0, false, true, '6', 'M');
$pdf->MultiCell(20, 6, "$ {$FormatedtotalDeducible}", 1, 'R', 0, 1, '', '', true, 0, false, true, '6', 'M');

/*=============================================
=            No deducibles            =
=============================================*/

$pdf->SetFillColor(12, 34, 63);
$pdf->SetTextColor(255, 255, 255);
$pdf->MultiCell(45, 7, "NO DEDUCIBLE", 1, 'C', 1, 1, 10, '', true, 0, false, true, '7', 'M');
$pdf->SetFillColor(126, 194, 36);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(15, 8, "PARTIDA", 1, 'C', 1, 0, 10, '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(15, 8, "FECHA", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(75, 8, "DESCRIPCION", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
$pdf->MultiCell(20, 8, "$ -", 1, 'C', 1, 1, '', '', true, 0, false, true, '8', 'M');

$totalNoDeducible = 0.00;
foreach($nodeducibles as $key => $detalle) {
	$totalNoDeducible += $detalle['total'];
    $proveedor = mb_strtoupper($detalle['proveedor']);
	$total = number_format($detalle['total'], 2);
	$folio = mb_strtoupper($detalle['folio']);
	$descripcion = mb_strtoupper($detalle['descripcion']);

}
$pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño
$formatedtotalNoDeducible = number_format($totalNoDeducible, 2);

$pdf->MultiCell(15, 7, "1", 1, 'C', 0, 0, 10, '' , true, 0, false, true, '7', 'M');
$pdf->MultiCell(15, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(75, 7, "NO DEDUCIBLES", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "$ {$formatedtotalNoDeducible}", 1, 'R', 0, 1, 115, '', true, 0, false, true, '7', 'M');


$y = $pdf->getY();
while ($y <= 255) {
	
	$pdf->MultiCell(15, 5, "", 1, 'C', 0, 0, 10, '', true, 0, false, true, '5', 'M');
	$pdf->MultiCell(15, 5, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '5', 'M');
	$pdf->MultiCell(75, 5, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '5', 'M');
	$pdf->MultiCell(20, 5, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '5', 'M');

    $y = $pdf->getY();
}

$pdf->SetFont('helvetica', 'B', 7.8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(75, 6, "TOTAL", 1, 'C', 0, 0, 40, '', true, 0, false, true, '6', 'M');
$pdf->MultiCell(20, 6, "$ {$formatedtotalNoDeducible}", 1, 'R', 0, 1, '', '', true, 0, false, true, '6', 'M');

$totalGastos = $totalDeducible + $totalNoDeducible;
$totalGastos = number_format($totalGastos, 2);
$pdf->SetFont('helvetica', '', 6); // Fuente, Tipo y Tamaño
$pdf->MultiCell(20, 8, "$ {$totalGastos}", 0, 'R', 0, 0, 115, 228, true, 0, false, true, '8', 'M');

if ( !is_null($realizaFirma) ) {
	$extension = mb_strtoupper(substr($realizaFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($realizaFirma, 10, 270, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

// if ( !is_null($revisoFirma) ) {
// 	$extension = mb_strtoupper(substr($revisoFirma, -3, 3));
// 	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

// 	$pdf->Image($revisoFirma, 70, 265, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
// }

// if ( !is_null($responsableFirma) ) {
// 	$extension = mb_strtoupper(substr($responsableFirma, -3, 3));
// 	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

// 	$pdf->Image($responsableFirma, 140, 265, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
// }

// $solicito = mb_strtoupper(fString($solicito));
// $puestoSolicito = "Mantenimiento Preventivo";
// $reviso = mb_strtoupper(fString($reviso));
// $puestoReviso = "Mantenimiento Preventivo";
// $responsable = mb_strtoupper(fString($responsable));


$pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño
$pdf->MultiCell(55, 5, "$realiza \n REALIZA", 'T', 'C', 0, 0, 25, 280, true);
$pdf->MultiCell(45, 5, "\n CONFORMIDAD", 'T', 'C', 0, 0, 85, '', true);
$pdf->MultiCell(50, 5, "\n REVISA", 'T', 'C', 0, 1, 140, '', true);

// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output("{$servicio->folio}.pdf", 'I');

//============================================================+
// END OF FILE
//============================================================+
