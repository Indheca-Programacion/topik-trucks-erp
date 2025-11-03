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
require_once "../../vendor/autoload.php";

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	// public $logo;
	public $empresa;
	public $servicioCentroDescripcion;
	public $numeroEconomicoSerie;
	public $servicioEstatusDescripcion;
	public $fechaInicial;
	public $fechaFinal;
	public $headerY;

	//Page header
	public function Header() {
		// $this->Rect(5, 5, 285, 25, 'D', array(), array(222,222,222));	
		$this->setCellPaddings(1, 1, 1, 1); // set cell padding

		// Title
		$this->SetFont('helvetica', 'B', 14); // Fuente, Tipo y Tamaño
		$this->SetTextColor(0, 0, 0); // Color del texto
		$this->SetFillColor(165, 164, 157); // Color de fondo
		$this->MultiCell(285, 8, "Control de Mantenimiento y Servicios", 0, 'C', 0, 1, 5, 5, true, 0, false, true, '8', 'M');
		$this->SetFont('helvetica', '', 12); // Fuente, Tipo y Tamaño
		$this->MultiCell(285, 8, "Listado de Servicios", 0, 'C', 0, 1, 5, '', true, 0, false, true, '8', 'M');

		$this->SetFont('helvetica', '', 10); // Fuente, Tipo y Tamaño
		$rowFechas = '';
		if ( !is_null($this->fechaInicial) || !is_null($this->fechaFinal) ) {
			if ( !is_null($this->fechaInicial) ) $rowFechas .= ' Fecha Inicial: ' . $this->fechaInicial;
			if ( !is_null($this->fechaFinal) ) $rowFechas .= ' Fecha Final: ' . $this->fechaFinal;
			// $this->MultiCell(285, 8, "Fecha Solicitud:$rowFechas", 0, 'C', 0, 1, 5, '', true, 0, false, true, '8', 'M');
		}
		if ( $this->servicioEstatusDescripcion != '' ) $rowFechas .= ' Estatus: ' . $this->servicioEstatusDescripcion;
		if ( $rowFechas != '' ) $this->MultiCell(285, 8, "$rowFechas", 0, 'C', 0, 1, 5, '', true, 0, false, true, '8', 'M');

		$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
		$this->SetTextColor(0, 0, 0); // Color del texto
		$this->SetFillColor(222, 222, 222); // Color de fondo

		$this->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
		$this->MultiCell(90, 5, "Empresa", 1, 'L', 0, 0, 5, '', true);
		$this->MultiCell(90, 5, "Centro de Servicio", 1, 'L', 0, 0, '', '', true);
		$this->MultiCell(65, 5, "Número Económico", 1, 'L', 0, 0, '', '', true);
		// $this->MultiCell(20, 5, "", 0, 'L', 0, 0, '', '', true);
		$this->MultiCell(40, 5, "Fecha del Reporte", 1, 'C', 0, 1, '', '', true);

		$this->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
		$this->SetFillColor(222, 222, 222); // Color de fondo
		$this->MultiCell(90, 5, "{$this->empresa}", 1, 'L', 1, 0, 5, '', true);
		$this->MultiCell(90, 5, "{$this->servicioCentroDescripcion}", 1, 'L', 1, 0, '', '', true);
		$this->MultiCell(65, 5, "{$this->numeroEconomicoSerie}", 1, 'L', 1, 0, '', '', true);
		// $this->MultiCell(20, 5, "", 0, 'L', 0, 0, '', '', true);

		$fecha = (new \DateTime('America/Mexico_City')) -> format('Y-m-d'); // Fecha del día
		$fecha = strtotime($fecha);
		$dia = date("d", $fecha);
		$mes = fNombreMes(date("n", $fecha));
		$year = date("Y", $fecha);
		$this->MultiCell(40, 5, "{$dia}/{$mes}/{$year}", 1, 'C', 0, 1, '', '', true);

		$this->Ln(2); // Salto de Línea

		$this->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$this->MultiCell(10, 10, "#", 1, 'C', 0, 0, 5, '', true, 0, false, true, '10', 'M');
		$this->MultiCell(35, 10, "Empresa\nCentro de Servicio", 1, 'L', 0, 0, '', '', true, 0, false, true, '10', 'M');
		$this->MultiCell(25, 10, "Folio", 1, 'L', 0, 0, '', '', true, 0, false, true, '10', 'M');
		$this->MultiCell(13, 10, "Estatus", 1, 'L', 0, 0, '', '', true, 0, false, true, '10', 'M');
		$this->MultiCell(30, 10, "Fecha Solicitud\nFecha Finalización", 1, 'L', 0, 0, '', '', true, 0, false, true, '10', 'M');
		$this->MultiCell(50, 10, "Tipo de Mantenimiento\nTipo de Servicio", 1, 'L', 0, 0, '', '', true, 0, false, true, '10', 'M');
		$this->MultiCell(25, 10, "Tipo de Maquinaria", 1, 'L', 0, 0, '', '', true, 0, false, true, '10', 'M');
		$this->MultiCell(20, 10, "Número\nEconómico", 1, 'L', 0, 0, '', '', true, 0, false, true, '10', 'M');
		$this->MultiCell(30, 10, "Marca - Modelo\nSerie", 1, 'L', 0, 0, '', '', true, 0, false, true, '10', 'M');
		$this->MultiCell(47, 10, "Descripcion", 1, 'L', 0, 1, '', '', true, 0, false, true, '10', 'M');

		$this->headerY = $this->GetY();
	}

	// Page footer
	public function Footer() {
		// $this->setXY(5, -10); // Position at 25 mm from bottom
		$this->setX(5);
		$this->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$this->MultiCell(285, 5, "Página {$this->getAliasNumPage()} de {$this->getAliasNbPages()}", 0, 'C', 0, 1, '', '', true, 0, false, true, '5', 'M');
	}
}

// create new PDF document
// $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// if ( is_null($empresa->imagen) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
// else $pdf->logo = Route::rutaServidor().$empresa->imagen;
$pdf->empresa = ( is_null($empresa->razonSocial) ) ? 'Todas' : mb_strtoupper(fString($empresa->razonSocial, 'UTF-8'));
$pdf->servicioCentroDescripcion = ( is_null($servicioCentro->descripcion) ) ? 'Todos' : mb_strtoupper(fString($servicioCentro->descripcion, 'UTF-8'));
$pdf->numeroEconomicoSerie = ( is_null($maquinaria->numeroEconomico) ) ? 'Todos' : mb_strtoupper(fString($maquinaria->numeroEconomico, 'UTF-8')) . ' [ ' . mb_strtoupper(fString($maquinaria->serie, 'UTF-8')) . ' ]' ;
$pdf->servicioEstatusDescripcion = ( is_null($servicioEstatus->descripcion) ) ? '' : mb_strtoupper(fString($servicioEstatus->descripcion, 'UTF-8'));;
$pdf->fechaInicial = $fechaSolicitudInicial;
$pdf->fechaFinal = $fechaSolicitudFinal;

// set document information
$pdf->setTitle("Listado de Servicios");
// remove default header/footer
// $pdf->setPrintHeader(false);
// $pdf->setPrintFooter(false);

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
// $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setMargins(PDF_MARGIN_LEFT, 45, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
// $pdf->setFooterMargin(0);

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

// $pdf->SetXY(5, 51);
$pdf->SetY($pdf->headerY);

$pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(222, 222, 222); // Color de fondo

if ( count($servicios) > 0 ) {
	$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	foreach($servicios as $key => $detalle) {
		$posicionY = $pdf->GetY();
		if ( $posicionY > 185 ) {
			$pdf->AddPage();

			$pdf->SetY($pdf->headerY);
		} 

		$numero = $key + 1;
		$empresaNombreCorto = mb_strtoupper(fString($detalle['empresas.nombreCorto']));
		$centroServicio = mb_strtoupper(fString($detalle['servicio_centros.descripcion']));
		$folio = mb_strtoupper(fString($detalle['folio']));
		$estatus = mb_strtoupper(fString($detalle['servicio_estatus.descripcion']));
		$fechaSolicitud = fFechaLarga($detalle['fechaSolicitud']);
		$fechaFinalizacion = ( is_null($detalle['fechaFinalizacion']) ) ? '' : fFechaLarga($detalle['fechaFinalizacion']);
		$mantenimientoTipo = mb_strtoupper(fString($detalle['mantenimiento_tipos.descripcion']));
		$servicioTipo = mb_strtoupper(fString($detalle['servicio_tipos.descripcion']));
		$maquinariaTipo = mb_strtoupper(fString($detalle['maquinaria_tipos.descripcion']));
		$numeroEconomico = mb_strtoupper(fString($detalle['maquinarias.numeroEconomico']));
		$marca = mb_strtoupper(fString($detalle['marcas.descripcion']));
		$modelo = mb_strtoupper(fString($detalle['modelos.descripcion']));
		$serie = mb_strtoupper(fString($detalle['maquinarias.serie']));
		$descripcion = mb_strtoupper(fString($detalle['descripcion']));

		$pdf->MultiCell(10, 12, "{$numero}", 1, 'C', 0, 0, 5, '', true, 0, false, true, '12', 'M');
		// $pdf->MultiCell(10, 10, "{$posicionY}", 1, 'C', 0, 0, 5, '', true, 0, false, true, '10', 'M');
		// $pdf->MultiCell(10, 10, "{$pdf->headerY}", 1, 'C', 0, 0, 5, '', true, 0, false, true, '10', 'M');
		$pdf->MultiCell(35, 12, "{$empresaNombreCorto}\n{$centroServicio}", 1, 'L', 0, 0, '', '', true, 0, false, true, '12', 'M');
		$pdf->MultiCell(25, 12, "{$folio}", 1, 'L', 0, 0, '', '', true, 0, false, true, '12', 'M');
		$pdf->MultiCell(13, 12, "{$estatus}", 1, 'L', 0, 0, '', '', true, 0, false, true, '12', 'M');
		$pdf->MultiCell(30, 12, "{$fechaSolicitud}\n{$fechaFinalizacion}", 1, 'L', 0, 0, '', '', true, 0, false, true, '12', 'M',true);
		$pdf->MultiCell(50, 12, "{$mantenimientoTipo}\n{$servicioTipo}", 1, 'L', 0, 0, '', '', true, 0, false, true, '12', 'M',true);
		$pdf->MultiCell(25, 12, "{$maquinariaTipo}", 1, 'L', 0, 0, '', '', true, 0, false, true, '12', 'M');
		$pdf->MultiCell(20, 12, "{$numeroEconomico}", 1, 'L', 0, 0, '', '', true, 0, false, true, '12', 'M',true);
		$pdf->MultiCell(30, 12, "{$marca} - {$modelo}\n{$serie}", 1, 'L', 0, 0, '', '', true, 0, false, true, '12', 'M',true);
		$pdf->MultiCell(47, 12, "{$descripcion}", 1, 'L', 0, 1, '', '', true, 0, false, false, '12', 'M',true);
	}
} else {
	$pdf->SetFont('helvetica', 'B', 12); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(285, 20, "No hay información por mostrar", 1, 'C', 0, 1, 5, '', true,0, false, true, '20', 'M');
}

// ---------------------------------------------------------
//Close and output PDF document
// $pdf->Output("Orden de Trabajo {$pdf->folio}.pdf", 'D');
$pdf->Output($ruta, 'F');

//============================================================+
// END OF FILE
//============================================================+
