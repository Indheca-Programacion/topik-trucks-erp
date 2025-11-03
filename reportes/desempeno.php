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
		//TODO: Cambiar esto a mas abajo
		// $extension = mb_strtoupper(substr($this->logo, -3, 3));
		// if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen
		// $this->Image($this->logo, 6, 6, 66, 23, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
	
		$this->setCellPaddings(1, 1, 1, 1); // set cell padding

		// Title
		$this->SetFont('helvetica', 'B', 14); // Fuente, Tipo y Tamaño
		$this->SetTextColor(0, 0, 0); // Color del texto
		$this->SetFillColor(165, 164, 157); // Color de fondo
		$this->MultiCell('', '', "DESEMPEÑO", 0, 'C', 0, 1, 5, 10, true, 0, false, true);
		$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño

		$this->Ln(3); // Salto de Línea
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

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

$pdf->setCellPaddings(1, 1, 1, 1); // set cell padding

//Crea las maquinarias

$pdf->AddPage(); // Agregar nueva página

// $pdf->SetXY(4, 52);
$pdf->SetFillColor(220, 220, 220); 
$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(30, 8, "NUMERO ECONOMICO", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(30, 8, "TOTAL DIAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 8, "HORAS OPERATIVAS DISPONIBLES", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(30, 8, "HORAS MOTOR REGISTRADAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(30, 8, "RALENTI REGISTRADAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(30, 8, "LTS COMB CONSUMIDOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(30, 8, "RENDIMIENTO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 8, "% APROVECHAMIENTO", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M',1);
$pdf->MultiCell(48, 8, "OBSERVACIONES", 1, 'C', 1,1, '', '', true, 0, false, true, '7', 'M');
$pdf->SetFillColor(255, 255, 255); 

$pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño

foreach ($arrayDesempeño as $key => $value) {
	$partida = $key + 1;
	$numeroEconomico = mb_strtoupper(fString($value['numeroEconomico']));
	$totalDias = number_format($value['totalDias'],2);
    $hod = number_format($value['hod'],2);
    $hmr = number_format($value['hmr'],2);
    $rr = number_format($value['rr'],2);
    $lcc = number_format($value['lcc'],2);
    $rendimiento = number_format($value['rendimiento'],2);
    $aprovechamiento = number_format($value['aprovechamiento'],2);
    $observaciones = mb_strtoupper($value['observaciones']);

	$y_end = $pdf->GetY();

	$pdf->MultiCell(30, 8, "{$numeroEconomico}", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 8, "{$totalDias}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 8, "{$hod}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 8, "{$hmr}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 8, "{$rr}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 8, "{$lcc}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 8, "{$rendimiento} %", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 8, "{$aprovechamiento} %", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(48, 8, "{$observaciones}", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M',1);

	if ( $y_end > 160 ) {
		$pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño

		$pdf->SetXY(15,50);
		$pdf->SetFillColor(220, 220, 220); 
		$pdf->MultiCell(30, 8, "NUMERO ECONOMICO", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(30, 8, "TOTAL DIAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(30, 8, "HORAS OPERATIVAS DISPONIBLES", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(30, 8, "HORAS MOTOR REGISTRADAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(30, 8, "RALENTI REGISTRADAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(30, 8, "LTS COMB CONSUMIDOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(30, 8, "RENDIMIENTO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(30, 8, "% APROVECHAMIENTO", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M',1);
        $pdf->MultiCell(48, 8, "OBSERVACIONES", 1, 'C', 1,1, '', '', true, 0, false, true, '7', 'M');
        $pdf->SetFillColor(255, 255, 255); // Color de fondo
        $pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño

	}
}

//Datos vacios para el final
$pdf->SetXY(4, 59);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetXY(30,180);
$pdf->SetFont('helvetica', 'B', 7); // Fuente, Tipo y Tamaño
	
	// if ( !is_null($elaboroFirma) ) {
	// 	$extension = mb_strtoupper(substr($elaboroFirma, -3, 3));
	// 	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen
	
	// 	$pdf->Image($elaboroFirma, 25, 160, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
	// }
	
	// $pdf->MultiCell(60, 6, "{$elaboro}", 'T', 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');	
	// $pdf->MultiCell(20, 7, "ELABORO", 0, 'C', 1, 0, 50, '', true, 0, false, true, '7', 'M');	
	// $pdf->MultiCell(20, 7, "GERENTE", 0, 'C', 1, 0, 140, '', true, 0, false, true, '7', 'M');	
	// $pdf->MultiCell(28, 7, "SUPERVISO", 0, 'C', 1, 0, 220, '', true, 0, false, true, '7', 'M');	
	

$pdf->Output("Desempeno.pdf", 'I');

// function obtenerDiasEnMes($fecha) {
//     // Separar el año y el mes de la fecha
//     list($year, $month) = explode('-', $fecha);

//     // Obtener el número de días en el mes
//     $numDias = cal_days_in_month(CAL_GREGORIAN, $month, $year);

//     return $numDias;
// }

// function obtenerDia($fecha) {
// 	//echo $fecha;
//     $diasSemana = array('D', 'L', 'M', 'X', 'J', 'V', 'S','D');
//     //echo date('N', strtotime($fecha));
//     $numeroDia = date('N', strtotime($fecha));
//     return $diasSemana[$numeroDia];
// }

// function obtenerMes($fecha) {
//     $meses = array(
//         1 => "ENERO", 2 => "FEBRERO", 3 => "MARZO", 4 => "ABRIL", 5 => "MAYO", 6 => "JUNIO",
//         7 => "JULIO", 8 => "AGOSTO", 9 => "SEPTIEMBRE", 10 => "OCTUBRE", 11 => "NOVIEMBRE", 12 => "DICIEMBRE"
//     );

//     $fecha_array = explode('-', $fecha);

//     if (count($fecha_array) == 2) {
//         $mes = $meses[intval($fecha_array[1])];
//         $año = $fecha_array[0];
//         return $mes . ' ' . $año;
//     } else {
//         // La fecha proporcionada no está en el formato esperado
//         return "Formato de fecha inválido";
//     }
// }