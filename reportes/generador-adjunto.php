<?php
require_once "../../vendor/autoload.php";

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
		// $this->Image($this->logo, 6, 6, 66, 23, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
	
		$this->setCellPaddings(1, 1, 1, 1); // set cell padding

		// Title
		$this->SetFont('helvetica', 'B', 14); // Fuente, Tipo y Tamaño
		$this->SetTextColor(0, 0, 0); // Color del texto
		$this->SetFillColor(165, 164, 157); // Color de fondo
		$this->MultiCell('', '', "GENERADOR SOPORTE PARA COBRO DE ESTIMACION", 0, 'C', 0, 0, 80, 10, true, 0, false, true, '11', 'M');

		$this->Ln(3); // Salto de Línea
		$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
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

if ( is_null($empresa->imagen) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
else $pdf->logo = Route::rutaServidor().$empresa->imagen;
$extension = mb_strtoupper(substr($pdf->logo, -3, 3));
		if ( $extension == 'JPG') $pdf->setJPEGQuality(75); // Calidad de imágen

foreach ($maquinariasPorEmpresa as $key => $maquinaria) {
	// echo '<pre>';
	// print_r($maquinaria);
	// echo '</pre>';
	$pdf->AddPage(); // Agregar nueva página
	$fechaMes= obtenerMes($generador->mes);

	$extension = mb_strtoupper(substr($maquinaria["empresa"], -3, 3));
	if ( $extension == 'JPG') $pdf->setJPEGQuality(75); // Calidad de imágen
	$pdf->Image('../../'.$maquinaria["empresa"], 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
	
	$pdf->SetXY(4, 38);
	$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	$pdf->SetFillColor(221, 244, 245);
	$pdf->MultiCell(18, 7, "PROYECTO:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(90, 7, "{$generador->obra}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell($dias*4, 7, "{$fechaMes}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(18, 7, "UBICACION:", 1, 'C', 1, 0, 5, 45, true, 0, false, true, '7', 'M');
	$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
	
	$pdf->MultiCell(90, 7, "{$generador->ubicacion}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->SetX(113);
	for ($i=1; $i < $dias+1; $i++) {
		$dia = strlen($i) < 2 ? "0".$i : $i;
		$diaSemana = obtenerDia($generador->mes."-".$dia);
		$pdf->MultiCell(4, 7, "{$diaSemana}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	}
	
	$pdf->SetXY(($dias*4)+113,38);// Cambiar
	$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	
	$pdf->MultiCell(8, 21, "TOTAL DIAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->SetFillColor(0, 143, 57);
	$pdf->MultiCell(8, 21, "DIAS EFECTIVOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->SetFillColor(230, 25, 25);
	$pdf->MultiCell(8, 21, "FALLAS MECANICAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->SetFillColor(237, 255, 33);
	$pdf->MultiCell(8, 21, "PAROS OPERATIVOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->SetFillColor(102, 51, 153);
	$pdf->SetTextColor(255, 255, 255);
	$pdf->MultiCell(8, 21, "CLIMA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFillColor(221, 244, 245);
	$pdf->MultiCell(9, 21, "% D.M.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	
	$pdf->SetXY(4, 52);
	
	$pdf->MultiCell(18, 7, "NUMERO ECONOMICO", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "EQUIPOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "MARCA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "MODELO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "SERIE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(18, 7, "FECHA INICIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	for ($i=1; $i < $dias+1; $i++) { 
		$pdf->MultiCell(4, 7, "{$i}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	}
	//Datos vacios para el final
	$pdf->SetXY(4, 59);
	$Total = 0;
	$TotalEfectivos = 0;
	$TotalFallas = 0;
	$TotalParos = 0;
	$TotalEntrega = 0;
	$TotlaDescanso = 0;
	$TotalClima = 0;
	$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	
	//Crea las maquinarias
	foreach ($maquinaria["maquinarias"] as $key => $value) {
		$equipo = mb_strtoupper(fString($value["equipo"]));
		$marca = mb_strtoupper(fString($value["marca"]));
		$modelo = mb_strtoupper(fString($value["modelo"]));
		$serie = mb_strtoupper(fString($value["serie"]));
		$numero = mb_strtoupper(fString($value["numeroEconomico"]));
		
		$partida = $key + 1;
	
		$fecha = fFechaLarga($value["fecha"]);
		$y_start = $pdf->GetY();
		if ( $y_start > 149 && $partida == count($maquinarias) ) {
			$pdf->AddPage();
	
			$pdf->SetXY(4, 38);
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(221, 244, 245);
			$pdf->MultiCell(18, 7, "PROYECTO:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
			$pdf->MultiCell(90, 7, "{$generador->obra}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell($dias*4, 7, "{$fechaMes}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->MultiCell(18, 7, "UBICACION:", 1, 'C', 1, 0, 5, 45, true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
	
			$pdf->MultiCell(90, 7, "{$generador->ubicacion}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetX(113);
			for ($i=1; $i < $dias+1; $i++) {
				$dia = strlen($i) < 2 ? "0".$i : $i;
				$diaSemana = obtenerDia($generador->mes."-".$dia);
				$pdf->MultiCell(4, 7, "{$diaSemana}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			}
	
			$pdf->SetXY(($dias*4)+113,38);// Cambiar
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	
			$pdf->MultiCell(8, 21, "TOTAL DIAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(0, 143, 57);
			$pdf->MultiCell(8, 21, "DIAS EFECTIVOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(230, 25, 25);
			$pdf->MultiCell(8, 21, "FALLAS MECANICAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(237, 255, 33);
			$pdf->MultiCell(8, 21, "PAROS OPERATIVOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(102, 51, 153);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->MultiCell(8, 21, "CLIMA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFillColor(221, 244, 245);
			$pdf->MultiCell(9, 21, "% D.M.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	
			$pdf->SetXY(4, 52);
	
			$pdf->MultiCell(18, 7, "NUMERO ECONOMICO", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "EQUIPOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "MARCA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "MODELO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "SERIE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "FECHA INICIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			for ($i=1; $i < $dias+1; $i++) { 
				$pdf->MultiCell(4, 7, "{$i}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			}
			$pdf->SetXY(4, 59);
	
			$y_start = $pdf->GetY();
		}
		$y_end = $pdf->GetY();
		$pdf->SetFillColor(255, 255, 255);
		$altoFila = $y_end - $y_start;
		$pdf->MultiCell(18, 7, "{$numero}", 1, 'C', 0, 0, 5, $y_start, true, true, false, false, '7', 'M');
		$pdf->MultiCell(18, 7, "{$equipo}", 1, 'C', 0, 0, '', '', true, true, false, false, '7', 'M');
		$pdf->MultiCell(18, 7, "{$marca}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(18, 7, "{$modelo}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(18, 7, "{$serie}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(18, 7, "{$fecha}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		for ($i=1; $i < $dias+1; $i++) { 
			$laborados = json_decode($value["laborados"]);
			$fallas = json_decode($value["fallas"]);
			$paros = json_decode($value["paros"]);
			$clima = json_decode($value["clima"]);
			if (in_array($i,$laborados)){
				$fill = 1;
				$pdf->SetFillColor(0, 143, 57);
			}
			if (in_array($i,$fallas)){
				$fill = 1;
				$pdf->SetFillColor(230, 25, 25);
			}
			if (in_array($i,$paros)){
				$fill = 1;
				$pdf->SetFillColor(237, 255, 33);
			}
			if (in_array($i,$clima)){
				$fill = 1;
				$pdf->SetFillColor(102, 51, 153);
			}
			$last = 0;
			if ($i == $dias) $last = 1;
			$pdf->MultiCell(4, 7, "", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(256, 256, 256);
		}
		$efectivos = count($laborados);
		$fallasMecanicas = count($fallas);
		$parosOper = count($paros);
		$climaCount = count($clima);
		$totalDias = $efectivos+$fallasMecanicas+$parosOper+$climaCount;
		$dm = 0;
		if ($totalDias !== 0) {
			$dm = (($totalDias-$fallasMecanicas)/$totalDias)*100;
			if (floor($dm) != $dm) {
			// Formatear el número con dos decimales
				$dm = number_format($dm, 2);
			}
		}
		$pdf->MultiCell(8, 7, "{$totalDias}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(8, 7, "{$efectivos}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(8, 7, "{$fallasMecanicas}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(8, 7, "{$parosOper}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(8, 7, "{$climaCount}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(9, 7, "{$dm} %", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');	
	
		if ( $y_end > 160 ) {
			$pdf->AddPage();
	
			$pdf->SetXY(4, 38);
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(221, 244, 245);
			$pdf->MultiCell(18, 7, "PROYECTO:", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
			$pdf->MultiCell(90, 7, "{$generador->obra}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell($dias*4, 7, "{$fechaMes}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
			$pdf->MultiCell(18, 7, "UBICACION:", 1, 'C', 1, 0, 5, 45, true, 0, false, true, '7', 'M');
			$pdf->SetFont('helvetica', 'B', 6); // Fuente, Tipo y Tamaño
	
			$pdf->MultiCell(90, 7, "{$generador->ubicacion}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetX(113);
			for ($i=1; $i < $dias+1; $i++) {
				$dia = strlen($i) < 2 ? "0".$i : $i;
				$diaSemana = obtenerDia($generador->mes."-".$dia);
				$pdf->MultiCell(4, 7, "{$diaSemana}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			}
	
			$pdf->SetXY(($dias*4)+113,38);// Cambiar
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
	
			$pdf->MultiCell(8, 21, "TOTAL DIAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(0, 143, 57);
			$pdf->MultiCell(8, 21, "DIAS EFECTIVOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(230, 25, 25);
			$pdf->MultiCell(8, 21, "FALLAS MECANICAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(237, 255, 33);
			$pdf->MultiCell(8, 21, "PAROS OPERATIVOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetFillColor(102, 51, 153);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->MultiCell(8, 21, "CLIMA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFillColor(221, 244, 245);
			$pdf->MultiCell(9, 21, "% D.M.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	
			$pdf->SetXY(4, 52);
	
			$pdf->MultiCell(18, 7, "NUMERO ECONOMICO", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "EQUIPOS", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "MARCA", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "MODELO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "SERIE", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(18, 7, "FECHA INICIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			for ($i=1; $i < $dias+1; $i++) { 
				$pdf->MultiCell(4, 7, "{$i}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			}
			$pdf->SetXY(4, 59);
	
			$pdf->SetFont('helvetica', 'B', 5); // Fuente, Tipo y Tamaño
		}
	
		$Total += $totalDias;
		$TotalEfectivos += $efectivos;
		$TotalFallas += $fallasMecanicas;
		$TotalParos += $parosOper;
		$TotalClima += $climaCount;
	}
	
	//Crea los totales
	$pdf->SetX(($dias*4)+113);//Cambiar
	$TotalDM = 0;
	if ($Total > 0) {
		$TotalDM = number_format((($Total-$TotalFallas)/$Total)*100,2);
	} 
	$pdf->MultiCell(8, 7, "{$Total}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(8, 7, "{$TotalEfectivos}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(8, 7, "{$TotalFallas}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(8, 7, "{$TotalParos}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(8, 7, "{$TotalClima}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(9, 7, "{$TotalDM} %", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');	
	
	$pdf->SetXY(30,180);
	$pdf->SetFont('helvetica', 'B', 7); // Fuente, Tipo y Tamaño
	
	if ( !is_null($elaboroFirma) ) {
		$extension = mb_strtoupper(substr($elaboroFirma, -3, 3));
		if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen
	
		$pdf->Image('../../'.$elaboroFirma, 25, 160, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
	}
	
	if ( !is_null($supervisoFirma) ) {
		$extension = mb_strtoupper(substr($supervisoFirma, -3, 3));
		if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen
	
		$pdf->Image('../../'.$supervisoFirma, 205, 160, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
	}
	
	if ( !is_null($autorizoFirma) ) {
		$extension = mb_strtoupper(substr($autorizoFirma, -3, 3));
		if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen
	
		$pdf->Image('../../'.$autorizoFirma, 120, 160, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
	}
	
	$pdf->MultiCell(60, 6, "{$elaboro}", 'T', 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(60, 6, "{$superintendente}", 'T', 'C', 1, 0, 120, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(60, 6, "{$superviso}", 'T', 'C', 1, 1, 210, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "ELABORO", 0, 'C', 1, 0, 50, '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(20, 7, "SUPERVISO", 0, 'C', 1, 0, 140, '', true, 0, false, true, '7', 'M');	
	$pdf->MultiCell(28, 7, "SUPERINTENDENTE", 0, 'C', 1, 0, 225, '', true, 0, false, true, '7', 'M');	
	
	$pdf->AddPage();
	
	// Configurar la fuente y tamaño del texto
	$pdf->SetFont('helvetica', '', 8);

	$extension = mb_strtoupper(substr($maquinaria["empresa"], -3, 3));
	if ( $extension == 'JPG') $pdf->setJPEGQuality(75); // Calidad de imágen
	$pdf->Image('../../'.$maquinaria["empresa"], 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
	
	$pdf->setXY(5,40);
	// Escribir en la nueva página
	$pdf->MultiCell(25, 7, "MAQUINARIA", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 7, "FECHA DE INICIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(40, 7, "FECHA DE FINALIZACION", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(140, 7, "OBSERVACION", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');
	
	foreach ($maquinaria["observaciones"] as $key => $value) {
		$fecha_inicio = fFechaLarga($value["fecha_inicio"]);
		$fecha_fin = fFechaLarga($value["fecha_fin"]);
	
		$y = $pdf->GetY();
	
		if ($y > 190) {
			$pdf->AddPage();
			$pdf->setXY(5,40);
			$pdf->MultiCell(25, 7, "MAQUINARIA", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(30, 7, "FECHA DE INICIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(40, 7, "FECHA DE FINALIZACION", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(140, 7, "OBSERVACION", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');
		}
	
		$pdf->MultiCell(25, 9, "{$value["numeroEconomico"]}", 1, 'C', 1, 0, 5, '', true, true, false, false, '8', 'M');
		$pdf->MultiCell(30, 9, "{$fecha_inicio}", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(40, 9, "{$fecha_fin}", 1, 'C', 1, 0, '', '', true, 0, false, true, '8', 'M');
		$pdf->MultiCell(140, 9, "{$value["observaciones"]}", 1, 'C', 1, 1, '', '', true, 0, false, true, '8', 'M');
	}
	
}

$ruta = __DIR__ . "/Generador ".$obra->descripcion." ".$fechaMes.".pdf";
$titulo = "Generador ".$obra->descripcion." ".$fechaMes.".pdf";
$pdf->Output($ruta, 'F');
$archivoGenerador = [
	'ruta' => $ruta,
	'titulo' => $titulo
];

function obtenerDiasEnMes($fecha) {
    // Separar el año y el mes de la fecha
    list($year, $month) = explode('-', $fecha);

    // Obtener el número de días en el mes
    $numDias = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    return $numDias;
}

function obtenerDia($fecha) {
	//echo $fecha;
    $diasSemana = array('D', 'L', 'M', 'X', 'J', 'V', 'S','D');
    //echo date('N', strtotime($fecha));
    $numeroDia = date('N', strtotime($fecha));
    return $diasSemana[$numeroDia];
}

function obtenerMes($fecha) {
    $meses = array(
        1 => "ENERO", 2 => "FEBRERO", 3 => "MARZO", 4 => "ABRIL", 5 => "MAYO", 6 => "JUNIO",
        7 => "JULIO", 8 => "AGOSTO", 9 => "SEPTIEMBRE", 10 => "OCTUBRE", 11 => "NOVIEMBRE", 12 => "DICIEMBRE"
    );

    $fecha_array = explode('-', $fecha);

    if (count($fecha_array) == 2) {
        $mes = $meses[intval($fecha_array[1])];
        $año = $fecha_array[0];
        return $mes . ' ' . $año;
    } else {
        // La fecha proporcionada no está en el formato esperado
        return "Formato de fecha inválido";
    }
}