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

		if ( $this->empresaId == 1 || $this->empresaId == 3 ) {

			// Logo
			// $this->Rect(5, 5, 65, 22, 'DF', array(), array(255,255,255));
			$this->Image($this->logo, 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
		
			$this->setCellPaddings(1, 1, 1, 1); // set cell padding

			// Title
			// $this->Rect(70, 5, 95, 11, 'D', array(), array(222,222,222));
			$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(165, 164, 157); // Color de fondo
			$this->MultiCell(135, 22, "COMPRA DE REFACCIONES Y SERVICIOS DE MANTENIMIENTO", 0, 'C', 0, 1, 70, 5, true, 0, false, true, '22', 'M');

		} elseif ( $this->empresaId <> 7 ) {

			// Logo
			$this->Rect(5, 5, 65, 22, 'DF', array(), array(222,222,222));
			$this->Image($this->logo, 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
		
			$this->setCellPaddings(1, 1, 1, 1); // set cell padding

			// Title
			// $this->Rect(70, 5, 95, 11, 'D', array(), array(222,222,222));
			$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(165, 164, 157); // Color de fondo
			$this->MultiCell(95, 11, "REQUISICIÓN DE COMPRA DE REFACCIONES Y \n SERVICIOS DE MANTENIMIENTO", 1, 'C', 1, 0, 70, 5, true);

			// $this->Rect(165, 5, 40, 11, 'D', array(), array(222,222,222));
			$this->SetTextColor(255, 255, 255); // Color del texto
			$this->SetFillColor(126, 126, 126); // Color de fondo
			$this->MultiCell(40, 11, "FO-IGC-AD-06.02 \n REV 05", 1, 'C', 1, 1, '', '', true);

			// $this->Rect(70, 16, 95, 11, 'D', array(), array(222,222,222));
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(222, 222, 222); // Color de fondo
			$this->MultiCell(95, 11, "SISTEMA DE GESTIÓN INTEGRAL \n ISO 9001:2015, ISO 14001:2015 Y 45001:2018", 1, 'C', 1, 0, 70, 16, true);

			// $this->Rect(165, 16, 40, 11, 'D', array(), array(222,222,222));
			$this->MultiCell(40, 11, "PÁGINA {$this->getPage()} DE {$this->getNumPages()}", 1, 'C', 1, 1, '', '', true, 0, false, true, '11', 'M');

		} else{
			$this->Image($this->logo, 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
			$this->Ln(24);
		}

		$this->Ln(2); // Salto de Línea
		// $fechaCreacion = fFechaLarga($this->fechaCreacion);
		$fecha = strtotime($this->fechaCreacion);
		$diaSemana = fNombreDia(date("w", $fecha));
		$dia = date("d", $fecha);
		$mes = fNombreMes(date("n", $fecha));
		$year = date("Y", $fecha);
		$folio = $this->folio;
		$this->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$this->MultiCell(40, 5, "FECHA DE SOLICITUD:", 0, '', 0, 0, 5, '', true);
		$this->MultiCell(60, 5, "{$diaSemana}, {$dia} de {$mes} de {$year}", 0, 'C', 0, 0, '', '', true);
		$this->Line(45, 35, 105, 35, false);
		$this->MultiCell(20, 5, "FOLIO N°", 0, 'R', 0, 0, 145, '', true);
		$this->MultiCell(40, 5, "{$folio}", 0, 'C', 0, 1, '', '', true);
		$this->Line(165, 35, 205, 35, false);

		$this->Ln(3); // Salto de Línea
		// $this->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$this->MultiCell(45, 5, "MAQUINARIA O EQUIPO", 1, 'C', 0, 0, 5, '', true);
		$this->MultiCell(30, 5, "MARCA", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(30, 5, "MODELO", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(35, 5, "SERIE", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(25, 5, "ODO / HOR", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(35, 5, "NUM. ECONÓMICO", 1, 'C', 0, 1, '', '', true);

		$equipo = mb_strtoupper(fString($this->maquinaria['maquinaria_tipos.descripcion'] ?? 'NA'));
		$marca = mb_strtoupper(fString($this->maquinaria['marcas.descripcion'] ?? 'NA'));
		$modelo = mb_strtoupper(fString($this->maquinaria['modelos.descripcion'] ?? 'NA'));
		$serie = mb_strtoupper(fString($this->maquinaria['serie'] ?? 'NA'));
		$odoHora = mb_strtoupper(fString($this->maquinaria['horoOdometroActual'] ?? 'NA'));
		$numeroEconomico = mb_strtoupper(fString($this->maquinaria['numeroEconomico'] ?? 'NA'));

		$this->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
		$this->MultiCell(45, 5, "{$equipo}", 1, 'C', 0, 0, 5, '', true);
		$this->MultiCell(30, 5, "{$marca}", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(30, 5, "{$modelo}", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(35, 5, "{$serie}", 1, 'C', 0, 0, '', '', true, 0, false, true, '5');
		$this->MultiCell(25, 5, "{$odoHora}", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(35, 5, "{$numeroEconomico}", 1, 'C', 0, 1, '', '', true, 0, false, true, '5.5', 'm',1);
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
$pdf->empresaId = $empresa->id;
$pdf->empresa = mb_strtoupper(fString($empresa->razonSocial, 'UTF-8'));
$pdf->folio = mb_strtoupper(fString($requisicion->folio));
$pdf->fechaCreacion = $requisicion->fechaCreacion;
$pdf->maquinaria = isset($requisicion->maquinaria) ? $requisicion->maquinaria : 'NA' ;
$pdf->mantenimientoTipo = isset($requisicion->mantenimientoTipo) ? $requisicion->mantenimientoTipo : 'NA' ; 

// set document information
$pdf->setTitle("Requisición {$pdf->folio}");
// remove default header/footer
// $pdf->setPrintHeader(false);
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
// $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->setFont('times', 'BI', 12); // Fuente, Tipo y Tamaño

$pdf->setCellPaddings(1, 1, 1, 1); // set cell padding
// $this->setCellMargins(1, 1, 1, 1); // set cell margins

$pdf->AddPage(); // Agregar nueva página

$pdf->SetXY(5, 51);
$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(20, 7, "PARTIDA", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(80, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(60, 7, "N° DE PARTE", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
foreach($requisicion->detalles as $key => $detalle) {
	$cantidad = number_format($detalle['cantidad'], 2);
	$unidad = mb_strtoupper($detalle['unidad']);
	$concepto = mb_strtoupper($detalle['concepto']);
	$numeroParte = mb_strtoupper($detalle['numeroParte']);

	$partida = $key + 1;

	$y_start = $pdf->GetY();
	if ( $y_start > 223 && $partida == count($requisicion->detalles) ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 51);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(20, 7, "PARTIDA", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(80, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(60, 7, "N° DE PARTE", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

		$y_start = $pdf->GetY();
	}

	$pdf->MultiCell(80, 0, "{$concepto}", 1, 'C', 0, 1, 65, '', true, 0);
	$y_end = $pdf->GetY();
	$altoFila = $y_end - $y_start;
	$num_detalle = count($requisicion->detalles);
	$pdf->MultiCell(20, $altoFila, "{$partida}", 1, 'C', 0, 0, 5, $y_start, true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "{$cantidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(60, $altoFila, "{$numeroParte}", 1, 'C', 0, 1, 145, '', true, 0, false, true, $altoFila, 'M');

	if ( $y_end > 270 ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 51);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(20, 7, "PARTIDA", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(80, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(60, 7, "N° DE PARTE", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	}
}

$y = $pdf->getY();
if ( $y > 228 ) {
	$pdf->AddPage();

	$pdf->SetXY(5, 51);
	$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(20, 7, "PARTIDA", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(80, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(60, 7, "N° DE PARTE", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

	$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	$y = $pdf->getY();
}

while ($y <= 220) {
	$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(80, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(60, 7, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

    $y = $pdf->getY();
}

// $pdf->Ln(2); // Salto de Línea
$pdf->setY(228);

$justificacion = mb_strtoupper(fString($requisicion->servicio['descripcion'] ?? 'NA'));
$mantenimientoTipoDescripcion = mb_strtoupper(fString($mantenimientoTipo->descripcion ?? 'NA'));

$pdf->Rect(5, 228, 100, 21, 'D', array(), array(222,222,222));
$pdf->Rect(105, 228, 100, 21, 'D', array(), array(222,222,222));

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(100, 5, "JUSTIFICACIÓN:", 0, '', 0, 0, '5', '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(100, 5, "TIPO DE REPARACIÓN:", 0, '', 0, 1, '', '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(100, 12, "{$justificacion}", 0, 'C', 0, 0, '5', '', true, 0, false, true, '12', 'M');
$pdf->Line(10, 247, 100, 247, false);
$pdf->MultiCell(100, 12, "{$mantenimientoTipoDescripcion}", 0, 'C', 0, 1, '', '', true, 0, false, true, '12', 'M');
$pdf->Line(110, 247, 200, 247, false);

$pdf->setY(250);

// $ubicacion = mb_strtoupper(fString($requisicion->maquinaria['ubicaciones.descripcion']));

$ubicacion = mb_strtoupper(fString($obra->descripcion ?? $requisicion->ubicacion['descripcion'] ));
// $mantenimientoTipoDescripcion = mb_strtoupper(fString($mantenimientoTipo->descripcion));

$pdf->Rect(5, 250, 200, 11, 'D', array(), array(222,222,222));
// $pdf->Rect(105, 250, 100, 11, 'D', array(), array(222,222,222));

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(30, 10, "OBRA O DESTINO:", 0, '', 0, 0, 5, '', true, 0, false, true, '10', 'M');
$pdf->MultiCell(165, 10, "{$ubicacion}", 0, 'C', 0, 1, '', '', true, 0, false, true, '10', 'M');
$pdf->Line(35, 259, 200, 259, false);
// $pdf->MultiCell(40, 10, "TIPO DE REPARACIÓN:", 0, 'R', 0, 0, '', '', true, 0, false, true, '10', 'M');
// $pdf->MultiCell(60, 10, "{$mantenimientoTipoDescripcion}", 0, 'C', 0, 1, '', '', true, 0, false, true, '10', 'M');
// $pdf->Line(140, 259, 200, 259, false);

if ( !is_null($solicitoFirma) ) {
	$extension = mb_strtoupper(substr($solicitoFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($solicitoFirma, 10, 265, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

if ( !is_null($revisoFirma) ) {
	$extension = mb_strtoupper(substr($revisoFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($revisoFirma, 70, 265, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

if ( !is_null($responsableFirma) ) {
	$extension = mb_strtoupper(substr($responsableFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($responsableFirma, 140, 265, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

$solicito = mb_strtoupper(fString($solicito));
$puestoSolicito = "Mantenimiento Preventivo";
$reviso = mb_strtoupper(fString($reviso));
$puestoReviso = "Mantenimiento Preventivo";
$responsable = mb_strtoupper(fString($responsable));


$pdf->SetFont('helvetica', '', 10); // Fuente, Tipo y Tamaño
$pdf->Ln(2); // Salto de Línea
$pdf->MultiCell(66, 5, "SOLICITÓ:", 0, 'C', 0, 0, 5, '', true);
$pdf->MultiCell(66, 5, "{$almacen}", 0, 'C', 0, 0, '', '', true);
$pdf->MultiCell(66, 5, "OK. RESPONSABLE:", 0, 'C', 0, 1, '', '', true);

$pdf->Ln(12); // Salto de Línea
$pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
// $pdf->Line(15, 278, 95, 278, false);
$pdf->MultiCell(66, 5, "{$solicito}", 0, 'C', 0, 0, 5, '', true);

$pdf->Line(76, 280, 132, 280, false);
$pdf->MultiCell(66, 5, "{$reviso}", 0, 'C', 0, 0, '', '', true);

$pdf->Line(142, 280, 200, 280, false);
$pdf->MultiCell(66, 5, "{$responsable}", 0, 'C', 0, 1, '', '', true);


// $pdf->SetFont('helvetica', 'N', 8); // Fuente, Tipo y Tamaño
// $pdf->MultiCell(100, 5, "{$puestoSolicito}", 0, 'C', 0, 0, 5, 282, true);
// $pdf->MultiCell(100, 5, "{$puestoReviso}", 0, 'C', 0, 1, '', '', true);

// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output("Requisición {$pdf->folio}.pdf", 'I');

//============================================================+
// END OF FILE
//============================================================+


