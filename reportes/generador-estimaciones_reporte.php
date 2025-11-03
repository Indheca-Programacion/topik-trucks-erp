<?php
require_once "vendor/autoload.php";

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
if (!class_exists('MYPDF')) {
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
			$this->MultiCell('', '', "ESTIMACION", 0, 'C', 0, 1, 80, 10, true, 0, false, true);
			$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
			$this->MultiCell('', '', "CLIENTES:", 0, 'C', 0, 0, '', '', true, 0, false, true);

			$this->Ln(3); // Salto de Línea
		}

		// Page footer
		public function Footer() {
			// $this->setY(-25); // Position at 25 mm from bottom
		}
	}
}


$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$dias = obtenerDiasEnMes($generador->mes);
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

// if ( is_null($empresa->imagen) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
// else $pdf->logo = Route::rutaServidor().$empresa->imagen;
$extension = mb_strtoupper(substr($pdf->logo, -3, 3));
		if ( $extension == 'JPG') $pdf->setJPEGQuality(75); // Calidad de imágen
$pdf->Image($pdf->logo, 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);

$fechaMes= obtenerMes($generador->mes);

//Crea las maquinarias
foreach ($datos as $key => $empresa) {

	$pdf->AddPage(); // Agregar nueva página
	$pdf->SetXY(4, 38);
	$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	$pdf->SetFillColor(12, 34, 63);
	$pdf->SetTextColor(255, 255, 255);
	$pdf->MultiCell(18, 7, "PROYECTO:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(90, 7, "{$generador->obra}", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "UBICACION:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(90, 7, "{$generador->ubicacion}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(175, 7, "{$fechaMes}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	$pdf->SetX(113);

	$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño

	$pdf->SetXY(4, 52);

	$pdf->MultiCell(18, 7, "NUMERO ECONOMICO", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "EQUIPOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "MARCA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "MODELO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "FECHA INICIAL", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "DIAS GENERADOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 7, "P.U. POR 30 DÍAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 7, "COSTO DEL MES", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 7, "$ OPERACIÓN", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 7, "$ COMB.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 7, "$ MANTTO.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 7, "$ FLETE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 7, "IMPORTE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');

	//Datos vacios para el final
	$pdf->SetXY(4, 59);
	$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetTextColor(0, 0, 0);

	$extension = mb_strtoupper(substr($empresa["ruta"], -3, 3));
	if ( $extension == 'JPG') $pdf->setJPEGQuality(75); // Calidad de imágen
	$pdf->Image($empresa["ruta"], 6, 6, 66, 23, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
	$importe_total = 0;
	foreach ($empresa["registros"] as $key => $value) {
		$laborados = count(json_decode($value["laborados"]));
		$fallas = json_decode($value["fallas"]);
		$paros = json_decode($value["paros"]);
		$clima = json_decode($value["clima"]);
		$totalDias = $laborados + count($paros);
		//
		$fechaIngresada = $generador->mes;
		$partesFecha = explode('-', $fechaIngresada);
		$año = $partesFecha[0];
		$mes = $partesFecha[1];
		$totalDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $año);
		$division = $totalDiasMes != 0 ? $laborados / $totalDiasMes : 0;
		$pu = (floatval($value["costo"])/30) * $totalDias ;
	
		$importe_total += $pu + $value["operacion"] + $value["comb"] + $value["mantto"] + $value["flete"] + $value["ajuste"];
		$importe = number_format($pu+$value["operacion"]+$value["comb"]+$value["mantto"]+$value["flete"]+$value["ajuste"],2);
		$equipo = mb_strtoupper(fString($value["equipo"]));
		$marca = mb_strtoupper(fString($value["marca"]));
		$modelo = mb_strtoupper(fString($value["modelo"]));
		$numero = mb_strtoupper(fString($value["numeroEconomico"]));
		$pu = number_format($pu,2);
		$partida = $key + 1;
	
		$fecha = fFechaLarga($value["fecha"]);
		$y_start = $pdf->GetY();
		if ( $y_start > 149 && $partida == count($estimaciones) ) {
			$pdf->AddPage();
	
			$pdf->SetXY(4, 38);
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(12, 34, 63);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->MultiCell(18, 7, "PROYECTO:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
			$pdf->MultiCell(90, 7, "{$generador->obra}", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "UBICACION:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(90, 7, "{$generador->ubicacion}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(175, 7, "{$fechaMes}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->SetX(113);
	
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	
			$pdf->SetXY(4, 52);
	
			$pdf->MultiCell(18, 7, "NUMERO ECONOMICO", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "EQUIPOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "MARCA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "MODELO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "FECHA INICIAL", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "DIAS GENERADOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "P.U. POR 30 DÍAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "COSTO DEL MES", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "$ OPERACIÓN", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "$ COMB.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "$ MANTTO.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "$ FLETE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "IMPORTE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetXY(4, 59);
	
			$pdf->SetXY(4, 59);
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(0, 0, 0);
	
			$y_start = $pdf->GetY();
		}
	
		$y_end = $pdf->GetY();
		$pdf->SetFillColor(255, 255, 255);
		$altoFila = $y_end - $y_start;
		$pdf->MultiCell(18, 7, "{$numero}", 1, 'C', 0, 0, 5, $y_start, true, true, false, false, '7', 'M');
		$pdf->MultiCell(18, 7, "{$equipo}", 1, 'C', 0, 0, '', '', true, true, false, false, '7', 'M');
		$pdf->MultiCell(18, 7, "{$marca}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(18, 7, "{$modelo}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(18, 7, "{$fecha}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(18, 7, "{$totalDias}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(25, 7, "$ {$value["costo"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(25, 7, "$ {$pu}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(25, 7, "$ {$value["operacion"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(25, 7, "$ {$value["comb"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(25, 7, "$ {$value["mantto"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(25, 7, "$ {$value["flete"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(25, 7, "$ {$importe}", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
	
		if ( $y_end > 160 ) {
			$pdf->AddPage();
	
			$pdf->SetXY(4, 38);
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(12, 34, 63);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->MultiCell(18, 7, "PROYECTO:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
			$pdf->MultiCell(90, 7, "{$generador->obra}", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "UBICACION:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(90, 7, "{$generador->ubicacion}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(175, 7, "{$fechaMes}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->SetX(113);
	
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	
			$pdf->SetXY(4, 52);
	
			$pdf->MultiCell(18, 7, "NUMERO ECONOMICO", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "EQUIPOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "MARCA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "MODELO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "FECHA INICIAL", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "DIAS GENERADOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "P.U. POR 30 DÍAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "COSTO DEL MES", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "$ OPERACIÓN", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "$ COMB.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "$ MANTTO.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "$ FLETE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(25, 7, "IMPORTE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	
			$pdf->SetXY(4, 59);
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetTextColor(0, 0, 0);
		}
	
	}
	$importe_total = number_format($importe_total,2);
	$pdf->MultiCell(25, 7, "$ {$importe_total}", 1, 'C', 0, 1, 263, '', true, 0, false, true, '7', 'M');

	$pdf->SetXY(30,180);
	$pdf->SetFont('helvetica', 'B', 7); // Fuente, Tipo y Tamaño
	
	if ( !is_null($elaboroFirma) ) {
		$extension = mb_strtoupper(substr($elaboroFirma, -3, 3));
		if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen
	
		$pdf->Image($elaboroFirma, 25, 160, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
	}

	if ( !is_null($estimacionFirma) ) {
		$extension = mb_strtoupper(substr($estimacionFirma, -3, 3));
		if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen
	
		$pdf->Image($estimacionFirma, 115, 160, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
	}

	if ( !is_null($supervisoFirma) ) {
		$extension = mb_strtoupper(substr($supervisoFirma, -3, 3));
		if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen
	
		$pdf->Image($supervisoFirma, 205, 160, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
	}
	
	$pdf->MultiCell(60, 6, "{$elaboro}", 'T', 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(60, 6, "{$autorizo}", 'T', 'C', 1, 0, 120, '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(60, 6, "{$superviso}", 'T', 'C', 1, 1, 205, '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(20, 7, "ELABORO", 0, 'C', 1, 0, 50, '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(20, 7, "GERENTE", 0, 'C', 1, 0, 140, '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(28, 7, "SUPERVISO", 0, 'C', 1, 0, 220, '', true, 0, false, true, '7', 'M');	
	
	
	
}


// Crear el directorio /generadores si no existe
$mesCarpeta = date('Y-m', strtotime($generador->mes . '-01'));
$outputDir = __DIR__ . '/generadores/' . $mesCarpeta . '/';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Guardar el PDF en /generadores
$pdf->Output($outputDir . "/Estimacion".$id.".pdf", 'F');

$rutaGenerador[$mesCarpeta][] = $outputDir . "/Estimacion".$id.".pdf";
// Guardar la ruta del generador en la base de datos