<?php
//============================================================+
// File name   : servicio.php
// Description : Formato de Orden de Trabajo
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
	public $empresa;
	public $folio;
	public $fechaSolicitud;
	public $maquinaria;
	public $mantenimientoTipo;
	public $horoOdometro;


	//Page header
	public function Header() {
		// Logo
		$this->Rect(5, 5, 65, 22, 'D', array(), array(222,222,222));
		$extension = mb_strtoupper(substr($this->logo, -3, 3));
		if ( $extension == 'JPG')  $this->setJPEGQuality(75); // Calidad de imágen
		$this->Image($this->logo, 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
	
		$this->setCellPaddings(1, 1, 1, 1); // set cell padding

		// Title
		$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
		$this->SetTextColor(0, 0, 0); // Color del texto
		$this->SetFillColor(165, 164, 157); // Color de fondo
		$this->MultiCell(135, 22, "ORDEN DE TRABAJO", 1, 'C', 0, 1, 70, 5, true, 0, false, true, '22', 'M');

		$this->Ln(2); // Salto de Línea
		// $fechaSolicitud = fFechaLarga($this->fechaSolicitud);
		$fecha = strtotime($this->fechaSolicitud);
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

		$equipo = mb_strtoupper(fString($this->maquinaria['maquinaria_tipos.descripcion']));
		$marca = mb_strtoupper(fString($this->maquinaria['marcas.descripcion']));
		$modelo = mb_strtoupper(fString($this->maquinaria['modelos.descripcion']));
		$serie = mb_strtoupper(fString($this->maquinaria['serie']));
		$horoOdometro = ( is_null($this->horoOdometro) ) ? '' : number_format($this->horoOdometro, 1, '.', ',');
		$numeroEconomico = mb_strtoupper(fString($this->maquinaria['numeroEconomico']));

		$this->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
		$this->MultiCell(45, 5, "{$equipo}", 1, 'C', 0, 0, 5, '', true);
		$this->MultiCell(30, 5, "{$marca}", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(30, 5, "{$modelo}", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(35, 5, "{$serie}", 1, 'C', 0, 0, '', '', true, 0, false, true, '5');
		$this->MultiCell(25, 5, "{$horoOdometro}", 1, 'C', 0, 0, '', '', true);
		$this->MultiCell(35, 5, "{$numeroEconomico}", 1, 'C', 0, 1, '', '', true);
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
$pdf->empresa = mb_strtoupper(fString($empresa->razonSocial, 'UTF-8'));
$pdf->folio = mb_strtoupper(fString($servicio->folio));
$pdf->fechaSolicitud = $servicio->fechaSolicitud;
$pdf->maquinaria = $servicio->maquinaria;
$pdf->mantenimientoTipo = $mantenimientoTipo;
$pdf->horoOdometro = $servicio->horoOdometro;

// set document information
$pdf->setTitle("Orden de Trabajo {$pdf->folio}");
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

$pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(75, 5, "Empresa:", 0, 'L', 0, 0, 5, '', true);
$pdf->MultiCell(3, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(67, 5, "Centro de Servicio:", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(3, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(52, 5, "Ubicación:", 0, 'L', 0, 1, '', '', true);

// $empresa = mb_strtoupper(fString($this->maquinaria['maquinaria_tipos.descripcion']));
$servicioCentroDescripcion = mb_strtoupper(fString($servicioCentro->descripcion));
// $ubicacionDescripcion = mb_strtoupper(fString($servicio->maquinaria['ubicaciones.descripcion']));
$ubicacionDescripcion = mb_strtoupper(fString($servicio->ubicacion['descripcion']));

$pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(222, 222, 222); // Color de fondo
$pdf->MultiCell(75, 5, "{$pdf->empresa}", 1, 'L', 1, 0, 5, '', true);
$pdf->MultiCell(3, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(67, 5, "{$servicioCentroDescripcion}", 1, 'L', 1, 0, '', '', true);
$pdf->MultiCell(3, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(52, 5, "{$ubicacionDescripcion}", 1, 'L', 1, 1, '', '', true);

$pdf->Ln(3); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(65, 5, "Tipo de Mantenimiento:", 0, 'L', 0, 0, 5, '', true);
$pdf->MultiCell(3, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(67, 5, "Tipo de Servicio:", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(3, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(62, 5, "Estatus:", 0, 'L', 0, 1, '', '', true);

$mantenimientoTipoDescripcion = mb_strtoupper(fString($mantenimientoTipo->descripcion));
$servicioTipoDescripcion = mb_strtoupper(fString($servicioTipo->descripcion));
$estatusDescripcion = mb_strtoupper(fString($servicio->estatus['descripcion']));

$pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(65, 5, "{$mantenimientoTipoDescripcion}", 1, 'L', 1, 0, 5, '', true);
$pdf->MultiCell(3, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(67, 5, "{$servicioTipoDescripcion}", 1, 'L', 1, 0, '', '', true);
$pdf->MultiCell(3, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(62, 5, "{$estatusDescripcion}", 1, 'L', 1, 1, '', '', true);

$pdf->Ln(3); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(65, 5, "DESCRIPCIÓN:", 0, 'L', 0, 1, 5, '', true);

// $descripcion = mb_strtoupper(fString($servicio->descripcion));
$descripcion = mb_strtoupper($servicio->descripcion);

$pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
// $pdf->MultiCell(200, 50, "{$descripcion}", 1, 'L', 0, 1, 5, '', true,0, false, true, '50', 'T');
$pdf->MultiCell(200, 18, "{$descripcion}", 1, 'L', 0, 1, 5, '', true,0, false, true, '18', 'T');

$pdf->Ln(3); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(65, 5, "REQUISICIONES:", 0, 'L', 0, 1, 5, '', true);

$pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(20, 7, "#", 1, 'L', 0, 0, 5, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(55, 7, "Folio", 1, 'L', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(55, 7, "Estatus", 1, 'L', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(70, 7, "Fecha Requisición", 1, 'L', 0, 1, '', '', true, 0, false, true, '7', 'M');

if ( count($servicio->requisiciones) > 0 ) {
	$pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
	foreach($servicio->requisiciones as $key => $detalle) {
		$numero = $key + 1;
		$folio = mb_strtoupper(fString($detalle['folio']));
		$estatus = mb_strtoupper(fString($detalle['servicio_estatus.descripcion']));
		$fechaRequisicion = fFechaLarga($detalle['fechaCreacion']);

		$pdf->MultiCell(20, 7, "{$numero}", 1, 'L', 0, 0, 5, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(55, 7, "{$folio}", 1, 'L', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(55, 7, "{$estatus}", 1, 'L', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(70, 7, "{$fechaRequisicion}", 1, 'L', 0, 1, '', '', true, 0, false, true, '7', 'M');
	}
} else {
	$pdf->SetFont('helvetica', 'B', 12); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(200, 20, "Orden de Trabajo sin Requisiciones", 1, 'C', 0, 1, 5, '', true,0, false, true, '20', 'M');
}

$pdf->Ln(3); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(88, 5, "Fecha de finalización estimada:", 0, 'L', 0, 0, 15, '', true);
$pdf->MultiCell(4, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(88, 5, "Horas Hombre Proyectadas:", 0, 'L', 0, 1, '', '', true);

$fechaProgramacion = 'N/A';
if ( !is_null($servicio->fechaProgramacion) ) $fechaProgramacion = fFechaLarga($servicio->fechaProgramacion);
$horasProyectadas = number_format($servicio->horasProyectadas, 2, '.', ',');

$pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(88, 5, "{$fechaProgramacion}", 1, 'L', 1, 0, 15, '', true);
$pdf->MultiCell(4, 5, "", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(88, 5, "{$horasProyectadas}", 1, 'R', 1, 1, '', '', true);

$pdf->Ln(3); // Salto de Línea

// Imprimir imágenes
$y = $pdf->getY();
foreach($imagenes as $key => $detalle) {
	$extension = mb_strtoupper(substr($detalle['ruta'], -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	if ( $key % 2 == 0 ) {
		$x = 15;
		if ( $key > 0 ) {
			$y += 88;
			$pdf->SetY($y);
		}
		if ( $y > 195 ) {
			$pdf->AddPage();
			$pdf->SetY(60);
		}
	} else $x = 107;

	// $pdf->Image($detalle['ruta'], $x, '', 88, 0, $extension, '', '', false, 300, '', false, false, 1, 'CT', false, false);
	$pdf->Image($detalle['ruta'], $x, '', 88, 88, $extension, '', '', false, 300, '', false, false, 1, 'CT', false, false);
	$y = $pdf->getY();
}

if ( !is_null($responsableFirma) ) {
	$extension = mb_strtoupper(substr($responsableFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($responsableFirma, 20, 262, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

if ( !is_null($revisionFirma) ) {
	$extension = mb_strtoupper(substr($revisionFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($revisionFirma, 120, 262, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

$pdf->Line(15, 278, 95, 278, false);
$pdf->Line(115, 278, 195, 278, false);

// $pdf->setY(280);
$pdf->setY(278);

$responsable = mb_strtoupper(fString($responsable));
$revision = mb_strtoupper(fString($revision));

$pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(100, 5, $responsable, 0, 'C', 0, 0, 5, '', true);
$pdf->MultiCell(100, 5, $revision, 0, 'C', 0, 1, '', '', true);
// $pdf->Ln(1); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(100, 5, "RESPONSABLE", 0, 'C', 0, 0, 5, 282, true);
$pdf->MultiCell(100, 5, "REVISIÓN", 0, 'C', 0, 1, '', '', true);

// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output("Orden de Trabajo {$pdf->folio}.pdf", 'I');

//============================================================+
// END OF FILE
//============================================================+
