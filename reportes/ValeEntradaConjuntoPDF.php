<?php

use App\Route;
require_once __DIR__ . '/../librerias/tcpdf/tcpdf.php';

class MYPDFVale extends TCPDF {
    public $logo;
	public $empresaId;
	public $empresa;
	public $folio;
	public $fechaCreacion;

	public function Header() {
		
		$extension = mb_strtoupper(substr($this->logo, -3, 3));
		if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen

			// Logo
			$this->Rect(5, 5, 65, 22, 'DF', array(), array(222,222,222));
			$this->Image($this->logo, 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
		
			$this->setCellPaddings(1, 1, 1, 1); // set cell padding

			// Title
			// $this->Rect(70, 5, 95, 11, 'D', array(), array(222,222,222));
			$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(165, 164, 157); // Color de fondo
			$this->MultiCell(95, 11, "ENTRADA DE ALMACEN", 1, 'C', 1, 0, 70, 5, true);

			// $this->Rect(165, 5, 40, 11, 'D', array(), array(222,222,222));
			$this->SetTextColor(255, 255, 255); // Color del texto
			$this->SetFillColor(126, 126, 126); // Color de fondo
			$this->MultiCell(40, 11, "FO-IGC-AD-11-14 \n REV 05", 1, 'C', 1, 1, '', '', true);

			// $this->Rect(70, 16, 95, 11, 'D', array(), array(222,222,222));
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(222, 222, 222); // Color de fondo
			$this->MultiCell(95, 11, "SISTEMA DE GESTIÓN INTEGRAL \n ISO 9001:2015, ISO 14001:2015, ISO 45001:2018", 1, 'C', 1, 0, 70, 16, true);

			$this->MultiCell(20, 11, "PÁGINA {$this->getPage()} DE {$this->getNumPages()}", 1, 'C', 1, 0, '', '', true, 0, false, true, '11', 'M');
			$this->MultiCell(20, 11, "FOLIO N°: \n {$this->folio}", 1, 'C', 1, 1, '', '', true, 0, false, true, '11', 'M');

			// $this->Rect(165, 16, 40, 11, 'D', array(), array(222,222,222));

		$this->Ln(2); // Salto de Línea
		// $fechaCreacion = fFechaLarga($this->fechaCreacion);
		
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

function generarPDFValeEntrada($entradasInventario) {

    // create new PDF document
    $pdf = new MYPDFVale(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->logo = $_SERVER['DOCUMENT_ROOT'] . 'vistas/img/empresas/' . "67756895.png";

    $pdf->setPrintFooter(false);

    // set default header data
    $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

    // set default monospaced font
    $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->setMargins(30, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->setHeaderMargin(PDF_MARGIN_HEADER);

    // set auto page breaks
    // $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setAutoPageBreak(TRUE, 5);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // ---------------------------------------------------------

    $pdf->setFont('times', 'BI', 12); // Fuente, Tipo y Tamaño

    $pdf->setCellPaddings(1, 1, 1, 1); // set cell padding

    foreach ($entradasInventario as $inventarioEntrada) {
        
        if ( file_exists ( "app/Models/InventarioPartida.php" ) ) {
            require_once "app/Models/InventarioPartida.php";
            require_once "app/Models/Almacen.php";
            require_once "app/Models/Usuario.php";

        } else {
            require_once "../Models/InventarioPartida.php";
            require_once "../Models/Almacen.php";
            require_once "../Models/Usuario.php";
        }

        $modeloInventarioPartida = New \App\Models\InventarioPartida;
        $modeloAlmacen = New \App\Models\Almacen;
        $modeloUsuario = New \App\Models\Usuario;

        // FOLIO DEL VALE
        $pdf->folio = $inventarioEntrada["id"];

        // CONSTULTAR PARTIDAS DE LA ENTRADA
        $modeloInventarioPartida->id = $inventarioEntrada["id"];
        $detalles = $modeloInventarioPartida->consultarPartidaPorId();

        // CONSULTAR ALMACEN DE LA ENTRADA
        $modeloAlmacen->consultar(null, $inventarioEntrada["almacenId"]);

        // USUARIO QUE RECIBIO
        $modeloUsuario->consultar(null, $inventarioEntrada["usuarioRecibioId"]);
        $usuarioNombreRecibio = mb_strtoupper($modeloUsuario->nombre . ' ' . $modeloUsuario->apellidoPaterno);
        if ( !is_null($modeloUsuario->apellidoMaterno) ) $usuarioNombreRecibio .= ' ' . mb_strtoupper($modeloUsuario->apellidoMaterno);
        $usuarioFirmaRecibio = $_SERVER['DOCUMENT_ROOT'] . $modeloUsuario->firma;

        // USUARIO QUE ENTREGO
        $usuarioNombreEntrego = mb_strtoupper($inventarioEntrada["entrego"]);
        $usuarioFirmaEntrego =  $_SERVER['DOCUMENT_ROOT'] . $inventarioEntrada["firma"];
     
        $pdf->AddPage();

        $pdf->Ln(8); // Salto de Línea

        $ordenCompra = mb_strtoupper(fString($inventarioEntrada["ordenCompra"]));
        $nombreAlmacen = mb_strtoupper(fString($modeloAlmacen->descripcion));
        $fechaEntrega = fFechaLarga($inventarioEntrada["fechaEntrega"]);
    
        $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño

        $pdf->MultiCell(40, 5, "NOMBRE QUIEN ENTREGA:", 0, 'R', 0, 0, 5, 30, true);
        $pdf->MultiCell(80, 5, "{$usuarioNombreEntrego}", 'B', '', 0, 0, '', '', true);

        $pdf->MultiCell(10, 5, "OC:", 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, "{$ordenCompra}", 'B', '', 0, 0, '', '', true);

        $pdf->MultiCell(15, 5, "Fecha:", 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, "{$fechaEntrega}", 'B', '', 0, 1, '', '', true);

        $pdf->MultiCell(18, 5, "ALMACEN:", 0, 'C', 0, 0, 5, '', true);
        $pdf->MultiCell(60, 5, "{$nombreAlmacen}", 'B', '', 0, 1, '', '', true);

        $pdf->Ln(8); // Salto de Línea

        $pdf->MultiCell(25, 9, "CANTIDAD", 1, 'C', 0, 0, 12, '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(25, 9, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(25, 9, "N° DE PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(111, 9, "DESCRIPCION", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

        $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

        foreach($detalles as $key => $detalle) {

            $partida = $key + 1;

            $y_start = $pdf->GetY();
            if ( $y_start > 223 && $partida == count($detalles) ) {
                $pdf->AddPage();

                $pdf->SetXY(5, 51);
                $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
                $pdf->MultiCell(25, 9, "CANTIDAD", 1, 'C', 0, 0, 12, '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(25, 9, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(25, 9, "N° DE PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(111, 9, "DESCRIPCION", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

                $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

                $y_start = $pdf->GetY();
            }

            $descripcion = $detalle["concepto"];
            $unidad = mb_strtoupper($detalle["unidad"]);

            $pdf->MultiCell(111, 0, "{$descripcion}", 1, 'C', 0, 1, 87, '', true, 0);
            $y_end = $pdf->GetY();
            $altoFila = $y_end - $y_start;
            $pdf->MultiCell(25, $altoFila, "{$detalle["cantidad"]}", 1, 'C',0, 0, 12, $y_start, true, 0, false, true, $altoFila, 'M');
            $pdf->MultiCell(25, $altoFila, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
            $pdf->MultiCell(25, $altoFila, "{$detalle["numeroParte"]}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');

            if ( $y_end > 270 ) {
                $pdf->AddPage();

                $pdf->SetXY(5, 51);
                $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
                $pdf->MultiCell(25, 9, "CANTIDAD", 1, 'C', 0, 0, 12, '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(25, 9, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(25, 9, "N° DE PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(111, 9, "DESCRIPCION", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
                $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
            }
        }

        $y = $pdf->getY();
        if ( $y > 228 ) {
            $pdf->AddPage();

            $pdf->SetXY(5, 51);
            $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
            $pdf->MultiCell(25, 9, "CANTIDAD", 1, 'C', 0, 0, 12, '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(25, 9, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(25, 9, "N° DE PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(111, 9, "DESCRIPCION", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

            $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
            $y = $pdf->getY();
        }

        while ($y <= 220) {
            $pdf->MultiCell(25, 9, "", 1, 'C', 0, 0, 12, '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(25, 9, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(25, 9, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(111, 9, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

            $y = $pdf->getY();
        }

        $pdf->SetX(12);
        $observaciones = mb_strtoupper($inventarioEntrada["observaciones"]);

        $pdf->MultiCell(186, 20, "OBSERVACIONES:", 1, '', 0,0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(146, 7, "{$observaciones}", 'B', '', 0, 0, 40, '', true, 0, false, true, '7', 'M');

        $pdf->Ln(40); // Salto de Línea
        $extension = mb_strtoupper(substr($usuarioFirmaEntrego, -3, 3));
        if ( $extension == 'PNG')  $pdf->setJPEGQuality(75); // Calidad de imágen
        $pdf->Image($usuarioFirmaEntrego, 10, 250, 50, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);

        $extension = mb_strtoupper(substr($usuarioFirmaRecibio, -3, 3));
        if ( $extension == 'PNG')  $pdf->setJPEGQuality(75); // Calidad de imágen
        $pdf->Image($usuarioFirmaRecibio, 75, 250, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);

        $pdf->SetXY(10,270);
        $pdf->MultiCell(50, 7, "{$usuarioNombreEntrego}", 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(50, 7, "{$usuarioNombreRecibio}", 0, 'C', 0, 0, 80, '', true);
        $pdf->MultiCell(50, 7, "", 0, 'C', 0, 1, 145, '', true);

        $pdf->SetX(10);

        $pdf->MultiCell(50, 7, "ENTREGÓ", 'T', 'C', 0, 0, '', '', true);

        $pdf->MultiCell(50, 7, "RECIBIÓ", 'T', 'C', 0, 0, 80, '', true);

        $pdf->MultiCell(50, 7, "AUTORIZÓ", 'T', 'C', 0, 0, 145, '', true);
    }

    // Definir y guardar el archivo PDF
    $ruta = __DIR__ . '/vale-entrada/vale-entrada.pdf';  
    $pdf->Output($ruta, 'F'); 

    return $ruta;
}