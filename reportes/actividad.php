<?php
//============================================================+
// File name   : actividad.php
// Description : Formato de Actividad Semanal
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

	//Page header
	public function Header() {
		$this->Rect(5, 5, 200, 25, 'D', array(), array(222,222,222));
		// Logo
		// $this->Rect(5, 5, 65, 22, 'DF', array(), array(222,222,222));
		// $this->Rect(6, 6, 66, 23, 'D', array(), array(222,222,222));
		$extension = mb_strtoupper(substr($this->logo, -3, 3));
		if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen
		$this->Image($this->logo, 6, 6, 66, 23, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
	
		$this->setCellPaddings(1, 1, 1, 1); // set cell padding

		// Title
		// $this->Rect(70, 5, 95, 11, 'D', array(), array(222,222,222));
		$this->SetFont('helvetica', 'B', 14); // Fuente, Tipo y Tamaño
		$this->SetTextColor(0, 0, 0); // Color del texto
		$this->SetFillColor(165, 164, 157); // Color de fondo
		$this->MultiCell(128, 11, "REPORTE DE ACTIVIDADES SEMANAL", 0, 'C', 0, 1, 75, 6, true, 0, false, true, '11', 'M');

		$fecha = strtotime($this->fechaInicial);
		// $diaSemana = fNombreDia(date("w", $fecha));
		$dia = date("d", $fecha);
		$mes = mb_strtoupper(fNombreMes(date("n", $fecha)));
		$year = date("Y", $fecha);
		// $fechaInicial = "{$diaSemana}, {$dia} de {$mes} de {$year}";
		$fechaInicial = "{$dia} DE {$mes} DEL {$year}";

		$fecha = strtotime($this->fechaFinal);
		$dia = date("d", $fecha);
		$mes = mb_strtoupper(fNombreMes(date("n", $fecha)));
		$year = date("Y", $fecha);
		$fechaFinal = "{$dia} DE {$mes} DEL {$year}";

		// $this->Rect(70, 16, 95, 11, 'D', array(), array(222,222,222));
		$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
		$this->SetTextColor(0, 0, 0); // Color del texto
		$this->SetFillColor(222, 222, 222); // Color de fondo
		$this->MultiCell(94, 11, "SEMANA DEL {$fechaInicial} \n AL {$fechaFinal}", 1, 'C', 0, 0, 75, 17, true);

		// $this->Rect(165, 16, 40, 11, 'D', array(), array(222,222,222));
		// $this->MultiCell(40, 11, "PÁGINA {$this->getPage()} DE {$this->getNumPages()}", 1, 'C', 1, 1, '', '', true, 0, false, true, '11', 'M');
		$this->MultiCell(30, 11, "FOLIO. {$this->folio}", 1, 'C', 0, 1, 173, '', true, 0, false, true, '11', 'M');

		$this->Ln(3); // Salto de Línea
		$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
		$this->MultiCell(200, 5, "BITACORA DE AVANCE DE REPARACIÓN", 0, 'C', 0, 1, 5, '', true);	
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
$pdf->folio = str_pad($actividad->folio, 3, '0', STR_PAD_LEFT);
$pdf->fechaInicial = $actividad->fechaInicial;
$pdf->fechaFinal = $actividad->fechaFinal;

// set document information
$pdf->setTitle("Actividad Semanal {$pdf->folio}");
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

$pdf->SetXY(5, 38);
$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(12, 7, "FECHA", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "FOLIO OT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(128, 7, "AVANCE DE REPARACIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "HORAS", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(234, 234, 234); // Color de fondo
foreach($detalleFechas as $key => $actividades) {

	// $fecha = $actividades['fecha'];
	$fecha = strtotime($actividades['fecha']);
	$diaSemana = date("w", $fecha);
	if ( $diaSemana == 0 ) $letraDiaSemana = 'D';
	elseif ( $diaSemana == 1 ) $letraDiaSemana = 'L';
	elseif ( $diaSemana == 2 ) $letraDiaSemana = 'M';
	elseif ( $diaSemana == 3 ) $letraDiaSemana = 'Mi';
	elseif ( $diaSemana == 4 ) $letraDiaSemana = 'J';
	elseif ( $diaSemana == 5 ) $letraDiaSemana = 'V';
	elseif ( $diaSemana == 6 ) $letraDiaSemana = 'S';
	$dia = date("d", $fecha);
	$alto = ( count($actividades['detalles']) < 2 ) ? 14 : count($actividades['detalles']) * 7;
	$pdf->SetFont('helvetica', '', 12); // Fuente, Tipo y Tamaño
	// $pdf->MultiCell(20, 7, "{$fecha}", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(12, $alto, "{$letraDiaSemana}\n{$dia}", 1, 'C', 0, 0, 5, '', true, 0, false, true, $alto, 'M');

	$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	if ( count($actividades['detalles']) == 0 ) {
		$pdf->MultiCell(188, 14, "", 1, 'C', 0, 1, 17, '', true, 0, false, true, '14', 'M');
		// $pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, 17, '', true, 0, false, true, '7', 'M');
		// $pdf->MultiCell(128, 7, "", 1, 'L', 0, 0, '', '', true, 0, false, true, '7', 'M');
		// $pdf->MultiCell(30, 7, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

		// $pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, 17, '', true, 0, false, true, '7', 'M');
		// $pdf->MultiCell(128, 7, "", 1, 'L', 0, 0, '', '', true, 0, false, true, '7', 'M');
		// $pdf->MultiCell(30, 7, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
	} else {
		foreach($actividades['detalles'] as $key => $actividad) {
			$ot = mb_strtoupper(fString($actividad['servicios.folio']));;
			$descripcion = mb_strtoupper(fString($actividad['descripcion']));
			$horas = number_format($actividad['horas'], 2);
			$pdf->MultiCell(30, 7, "{$ot}", 1, 'C', 0, 0, 17, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(128, 7, "{$descripcion}", 1, 'L', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(30, 7, "{$horas}", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
		}
	}

	if ( count($actividades['detalles']) == 1 ) {
		$pdf->MultiCell(188, 7, "", 1, 'C', 0, 1, 17, '', true, 0, false, true, '7', 'M');
		// $pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, 17, '', true, 0, false, true, '7', 'M');
		// $pdf->MultiCell(128, 7, "", 1, 'L', 0, 0, '', '', true, 0, false, true, '7', 'M');
		// $pdf->MultiCell(30, 7, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
	}

}

// $pdf->Ln(2); // Salto de Línea
$pdf->setY(250);

$empleadoNombre = mb_strtoupper(fString($empleadoNombre));

$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(200, 5, "NOMBRE Y FIRMA", 1, 'C', 0, 1, 5, '', true);
$pdf->MultiCell(200, 15, "{$empleadoNombre}", 1, 'C', 0, 1, 5, '', true, 0, false, true, '15', 'B');

$pdf->MultiCell(200, 20, "AUTORIZACIÓN / COORDINADOR", 1, 'C', 0, 1, 5, '', true);

// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output("Actividad Semanal {$pdf->folio}.pdf", 'I');

//============================================================+
// END OF FILE
//============================================================+
