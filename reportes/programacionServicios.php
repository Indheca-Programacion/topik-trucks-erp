<?php
require_once "../../vendor/autoload.php";

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF2 extends TCPDF {
	public $logo;
	public $empresa;
	public $folio;

	//Page header
	public function Header() {
		// Logo
		$extension = mb_strtoupper(substr($this->logo, -3, 3));
		if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen
		$this->SetLineStyle(array('width' => 0, 'color' => array(255, 255, 255)));
		$this->SetFillColor(242, 242, 242); // Color de fondo
		$this->RoundedRect(6, 5, 70, 22, 3.5, '1111', 'DF');
		$this->Image($this->logo, 6, 6, 66, 23, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
	
		$this->setCellPaddings(1, 1, 1, 1); // set cell padding

        $this->setCellPaddings(1, 1, 1, 1); // set cell padding

        // Title
        $this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
        $this->SetTextColor(0, 0, 0); // Color del texto
        $this->SetFillColor(170, 171, 175); // Color de fondo
        $this->RoundedRect(75, 5, 170, 11, 3.5, '0001', 'DF');
        $this->SetFillColor(0, 0, 0); // Color de fondo
        $this->MultiCell(170, 11, "PROGRAMACIÓN DE MANTENIMIENTOS", 0, 'C', 0, 0, 75, 7, true);

        // $this->Rect(165, 5, 40, 11, 'D', array(), array(222,222,222));
        $this->SetTextColor(255, 255, 255); // Color del texto
        $this->SetFillColor(139, 143, 146); // Color de fondo
        $this->RoundedRect(240, 5, 50, 11, 3.5, '1000', 'DF');
        $this->SetFillColor(0, 0, 0); // Color de fondo

        $this->MultiCell(40, 11, "PGR-IGC-10-05 \n REV 00", 0, 'C', 0, 1, 245,  5, true);

        // $this->Rect(70, 16, 95, 11, 'D', array(), array(222,222,222));
        $this->SetTextColor(0, 0, 0); // Color del texto
        $this->SetFillColor(242, 242, 242); // Color de fondo
        $this->MultiCell(165, 11, "SISTEMA DE GESTIÓN INTEGRAL \n ISO 9001:2015, ISO 14001:2015, ISO 45001:2018", 1, 'C', 1, 0, 75, 16, true);

        $this->MultiCell(50, 11, "PÁGINA {$this->getPage()} DE {$this->getNumPages()}", 1, 'C', 1, 1, '', '', true, 0, false, true, '11', 'M');

		$this->Ln(2); // Salto de Línea
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

$pdf = new MYPDF2('L', 'mm', 'A4', true, 'UTF-8', false);
if ( is_null($empresa->imagen) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
else $pdf->logo = Route::rutaServidor().$empresa->imagen;
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

$pdf->AddPage(); // Agregar nueva página

$pdf->SetXY(15,30);
$pdf->SetFont('helvetica', 'N', 9); // Fuente, Tipo y Tamaño
$numeroContrato = '';
$ubicacion = $ubicaciones->nombreCorto ?? '';
$nombreContrato = $ubicaciones->nombreCorto ?? '';
$periodo = 'SEMANA '.date('W');
$pdf->MultiCell(40, 7, "N° CONTRATO/OBRA:", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(60, 6, "{$numeroContrato}", 'B', 'C', 0, 0, '', '', true);
$pdf->MultiCell(25, 7, "UBICACIÓN:", 0, 'L', 0, 0, '160', '', true);
$pdf->MultiCell(60, 6, "{$ubicacion}", 'B', 'C', 0, 1, '', '', true);
$pdf->MultiCell(60, 7, "NOMBRE DEL CONTRATO/OBRA:", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(60, 6, "{$nombreContrato}", 'B', 'C', 0, 0, '', '', true);
$pdf->MultiCell(25, 7, "PERIODO:", 0, 'L', 0, 0, '160', '', true);
$pdf->MultiCell(60, 6, "{$periodo}", 'B', 'C', 0, 1, '', '', true);

$pdf->SetXY(15,50);
$pdf->SetFont('helvetica', 'B', 7); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(221, 235, 247); // Color de fondo
$pdf->MultiCell(15, 8, "PART.", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 8, "NO. ECONOMICO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 8, "FOLIO SERVICIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(60, 8, "UBICACION", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 8, "ESTATUS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(40, 8, "TIPO DE MANTENIMIENTO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(40, 8, "SOPORTE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(40, 8, "QR/LINK", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');
$pdf->SetFillColor(255, 255, 255); // Color de fondo

foreach ($servicios as $key => $value) {
	$partida = $key + 1;
	$numeroEconomico = mb_strtoupper(fString($value['numeroEconomico']));
	$folio = mb_strtoupper(fString($value['servicioAbierto']));
	$ubicacion = mb_strtoupper(fString($value['ubicacion']));
	$estatus = mb_strtoupper(fString($value['estatusRequisicion']));
	$tipoMantenimiento = mb_strtoupper(fString($value['tipoMantenimiento']));
	$soporte = mb_strtoupper(fString($value['soporte']));

	$y_end = $pdf->GetY();

	// Preparar estilo del QR
	$style = array(
		'border' => 2,
		'padding' => 1,
		'fgcolor' => array(0,0,0),
		'bgcolor' => false
	);

	// Escribir las columnas previas
	$pdf->MultiCell(15, 20, "{$partida}", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 20, "{$numeroEconomico}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 20, "{$folio}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(60, 20, "{$ubicacion}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 20, "{$estatus}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(40, 20, "{$tipoMantenimiento}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(40, 20, "{$soporte}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');

	// Capturar posición de la celda QR antes de dibujarla
	$x_qr = $pdf->GetX();
	$y_qr = $pdf->GetY();

	// Dibujar la celda (vacía por defecto)
	$pdf->MultiCell(40, 20, "", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');

	// Si existe servicioAbiertoId, generar y dibujar el QR centrado dentro de la celda
	if (!is_null($value['servicioAbiertoId']) && $value['servicioAbiertoId'] !== '') {
		$contenido_qr = Route::names('servicios.edit', $value['servicioAbiertoId']);

		// Tamaño del QR (ajustado para caber en la celda 40x8 mm)
		$barcode_size = 18; // mm
		// Centrar el QR dentro de la celda de 40x8: X + (40 - size)/2, Y + (8 - size)/2
		$px = $x_qr + (40 - $barcode_size) / 2;
		$py = $y_qr + max(0, (8 - $barcode_size) / 2); // evitar valores negativos

		$pdf->write2DBarcode(
			$contenido_qr,
			'QRCODE,L',
			$px,
			$py,
			$barcode_size,
			$barcode_size,
			$style,
			'N'
		);
	}

	// Última columna: SOPORTE

	if ( $y_end > 160 ) {
		$pdf->AddPage();

		$pdf->SetXY(15,50);
		$pdf->SetFont('helvetica', 'B', 7); // Fuente, Tipo y Tamaño
		$pdf->SetFillColor(221, 235, 247); // Color de fondo
		$pdf->MultiCell(15, 8, "PART.", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 8, "NO. ECONOMICO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 8, "FOLIO SERVICIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(60, 8, "UBICACION", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 8, "ESTATUS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(40, 8, "TIPO DE MANTENIMIENTO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(40, 8, "QR/LINK", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(40, 8, "SOPORTE", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');
		$pdf->SetFillColor(255, 255, 255); // Color de fondo

	}
}
$pdf->Ln(12); // Salto de Línea

$pdf->MultiCell(100, 7, "ELABORÓ", 1, 'C', 0, 0, 20, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(100, 7, "REVISÓ", 1, 'C', 0, 1, 150, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(100, 10, "RICARDO ANTONIO SERENA DE LA CRUZ", 1, 'C', 0, 0, 20, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(100, 10, "", 1, 'C', 0, 1, 150, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(100, 7, "NOMBRE Y FIRMA", 1, 'C', 0, 0, 20, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(100, 7, "NOMBRE Y FIRMA", 1, 'C', 0, 0, 150, '', true, 0, false, true, '7', 'M');

$ruta_directoro = getcwd();
// Construir la ruta completa del PDF
$ruta_pdf = $ruta_directoro.'/tmp/ProgramacionServicios.pdf';

// Guardar el PDF en la ruta actual
$pdf->Output($ruta_pdf, 'F');