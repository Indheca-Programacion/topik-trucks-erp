<?php
require_once "vendor/autoload.php";

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $logo;
	public $empresa;
	public $folio;

	//Page header
	public function Header() {
		// Logo
		$extension = mb_strtoupper(substr($this->logo, -3, 3));
		if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen
		
		$this->Image($this->logo, 6, 6, 66, 23, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
		$this->Ln(24);
	
		$this->setCellPaddings(1, 1, 1, 1); // set cell padding
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

if ( is_null($empresa->logo) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
else $pdf->logo = $empresa->logo;
$extension = mb_strtoupper(substr($pdf->logo, -3, 3));
if ( $extension == 'JPG') $pdf->setJPEGQuality(75); // Calidad de imágen
$pdf->cliente = mb_strtoupper($cliente->nombreCompleto);

// set document information
$pdf->setTitle("Presupuesto {$presupuesto->id}");

// $pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(20, PDF_MARGIN_TOP, 10);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
// $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

// ---------------------------------------------------------

$pdf->setCellPaddings(1, 1, 1, 1); // set cell padding

$pdf->AddPage(); // Agregar nueva página

// Title
$pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño
$pdf->SetTextColor(0, 0, 0); // Color del texto
$pdf->SetFillColor(165, 164, 157); // Color de fondo
$pdf->MultiCell(90, 4, "Estimado " . $pdf->cliente, 0, '', 0, 0, '', '', true);
$pdf->MultiCell(0, 4, "Fecha " . fFechaLarga($presupuesto->fechaCreacion), 0, '', 0, 1, '', '', true);
$pdf->MultiCell(0, 4, "Cotización No " . $presupuesto->id, 0, '', 0, 1, '110', '', true);
$pdf->MultiCell(0, 4, "Atención " . 'Ing.Emilio Mixtega / Carlos Villatoro', 0, '', 0, 1, '110', '', true);
$pdf->MultiCell(0, 4, "Presente ", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(0, 4, "Telefono " . '-', 0, '', 0, 1, '110', '', true);
$pdf->MultiCell(0, 4, "Dirección " . 'Conocida.', 0, '', 0, 1, '110', '', true);

$pdf->Ln(5);
$pdf->MultiCell(0, 5, " Indicamos a continuación una cotización de los productos y servicios solicitados.", 0, '', 0, 1, '', '', true);

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(15, 7, "Codigo", 'B', '', 0, 0, 20, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(80, 7, "Concepto", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(15, 7, "cant.", 'B', 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(15, 7, "U.Med", 'B', 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "P.U.", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(15, 7, "Desc", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "Total", 'B', 'R', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño
$subtotal =0;
foreach($serviciosPresupuesto as $key => $detalle) {
	$partidas = $detalle["partidas"];
	foreach($partidas as $key => $partida) {
		$codigo = mb_strtoupper($partida['codigo']??'');
		$cantidad = number_format($partida['cantidad'], 2);
		$unidad = mb_strtoupper($partida['unidad']);
		$concepto = mb_strtoupper($partida['descripcion']);
		$precioUnitario = number_format($partida['costo_base'], 2);
		$descuento = number_format($partida['descuento']??0, 2);
		$importe = number_format($partida['costo_base']* $partida['cantidad'], 2);

		$part = $key + 1;
		
		$y_start = $pdf->GetY();

		if ( $y_start > 223 && $part == count($partidas) ) {
			$pdf->AddPage();

			$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
			$pdf->MultiCell(15, 7, "Codigo", 'B', '', 0, 0, 20, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(80, 7, "Concepto", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(15, 7, "cant.", 'B', 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(15, 7, "U.Med", 'B', 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 7, "P.U.", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(15, 7, "Desc", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 7, "Total", 'B', 'R', 0, 1, '', '', true, 0, false, true, '7', 'M');

			$pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño

			$y_start = $pdf->GetY();
		}

		$pdf->MultiCell(80, 0, "{$concepto}", 'B', '', 0, 1, 35, '', true, 0);
		$y_end = $pdf->GetY();
		$altoFila = $y_end - $y_start;
		$pdf->MultiCell(10, $altoFila, "{$codigo}", 0, 'C', 0, 0, 20, $y_start, true, 0, false, true, $altoFila, 'M');
		$pdf->MultiCell(15, $altoFila, "{$cantidad}", 'B', 'C', 0, 0, 115, '', true, 0, false, true, $altoFila, 'M');
		$pdf->MultiCell(15, $altoFila, "{$unidad}", 'B', 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
		$pdf->MultiCell(20, $altoFila, "$ {$precioUnitario}", 'B', 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
		$pdf->MultiCell(15, $altoFila, "$ {$descuento}", 'B', 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
		$pdf->MultiCell(20, $altoFila, "$ {$importe}", 'B', 'R', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');
		$subtotal += $partida['costo_base']* $partida['cantidad'];

		if ( $y_end > 270 ) {
			$pdf->AddPage();

			$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
			$pdf->MultiCell(15, 7, "Codigo", 'B', '', 0, 0, 20, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(80, 7, "Concepto", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(15, 7, "cant.", 'B', 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(15, 7, "U.Med", 'B', 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 7, "P.U.", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(15, 7, "Desc", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 7, "Total", 'B', 'R', 0, 1, '', '', true, 0, false, true, '7', 'M');

			$pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño
		}
	}
}

$totalPresupuesto = $subtotal * 1.16; // Incluye IVA

$pdf->MultiCell(60, '', "SUB-TOTAL: $ ", 'T', 'R', 0, 0, 120, '', true, 0, false, true, 7, 'M');
$pdf->MultiCell(0, '', number_format($subtotal, 2), 0, 'R', 0, 1, '', '', true, 0, false, true, 7, 'M');
$pdf->MultiCell(60, '', "IVA: $ " , '', 'R', 0, 0, 120, '', true, 0, false, true, 7, 'M');
$pdf->MultiCell('', '', number_format($subtotal * 0.16, 2), 0, 'R', 0, 1, '', '', true, 0, false, true, 7, 'M');
$pdf->MultiCell(60, '', "TOTAL: $ " , '', 'R', 0, 0, 120, '', true, 0, false, true, 7, 'M');
$pdf->MultiCell('', '', number_format($totalPresupuesto, 2), 0, 'R', 0, 1, '', '', true, 0, false, true, 7, 'M');

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(30, 14, "Condiciones de Pago:", 0, '', 0, 0, '', '');
$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(30, 7, $presupuesto->condicionesPago??'Credito 30 Dias', 0, '', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(0, 7, 'Precios Reflejados en Moneda Nacional', 0, '', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->MultiCell(30, 7, 'Observaciones:', 0, '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(120, 14, $presupuesto->observaciones??' UNIDAD MC-551   CICE CABINA DE MONTACARGA  SIN  PLACAS        NO SE INCLUYE 
CAMBIO DE CRISTALES O ACRILICOS                                            LA UNIDAD DEBERA SER 
LLEVADA A NUESTRAS INSTALACIONES           ', 0, '', 0, 1, '', '', );

$pdf->MultiCell(30, 14, 'VALIDEZ DE LA OFERTA:', 0, '', 0, 0, '', '', );
$pdf->MultiCell(0, 7, $presupuesto->validezOferta??'1 MES A PARTIR DE EXPEDIDA', 0, '', 0, 1, '', '');

$pdf->MultiCell(30, 14, 'TIEMPO ESTIMADO:', 0, '', 0, 0, '', '', );
$pdf->MultiCell(0, 7, $presupuesto->tiempoEstimado??'65 Dias Habiles', 0, '', 0, 1, '', '');

$pdf->MultiCell(30, 14, 'GARANTIA EN PINTURA:', 0, '', 0, 0, '', '', );
$pdf->MultiCell(0, 7, $presupuesto->garantia??'1 AÑO', 0, '', 0, 1, '', '');

$pdf->MultiCell(0, 7, 'Sin mas que hacer referencia estamos a sus completas órdenes', 0, 'C', 0, 1, '', '');

$pdf->Ln(20);

$y_end = $pdf->GetY();

if ( ($y_end + 30) > $pdf->getPageHeight() ) {
	$pdf->AddPage(); // Agregar nueva página
	$pdf->Ln(20);
}

$pdf->MultiCell(60, 7, ' Aprobada (Nombre, Firma y Sello', 'T', 'C', 0, 1, 120, '');

$pdf->Output("Cotizacion-".$presupuesto->id.".pdf", 'I');