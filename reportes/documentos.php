<?php

// Include the main TCPDF library (search for installation path).
require_once "../../vendor/autoload.php";
// use setasign\Fpdi\Tcpdf\Fpdi;
$pdf = new \Clegginabox\PDFMerger\PDFMerger;

foreach ($comprobantesPago as $key => $file) {
	$pdf->addPDF('../../'.$file["ruta"], 'all');
}
foreach ($ordenesCompra as $key => $file) {
	try {
        $pdf->addPDF('../../'.$file["ruta"], 'all');
    } catch (Exception $e) {
        // Captura cualquier excepci¨®n que pueda ocurrir al agregar el PDF
        error_log("Error al agregar el PDF " . $file["ruta"] . ": " . $e->getMessage() . "\n", 3, "log_errores.txt");
    }
}
foreach ($facturas as $key => $file) {
	$pdf->addPDF('../../'.$file["ruta"], 'all');
}
foreach ($cotizaciones as $key => $file) {
	$pdf->addPDF('../../'.$file["ruta"], 'all');
}

$pdf->merge('file', __DIR__.'/tmp/mi_pdf.pdf', 'P');