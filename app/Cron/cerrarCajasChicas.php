<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/control-mantenimiento/app/Cron/php_error_log');

chdir('/var/www/html/control-mantenimiento/app/Cron/');

define('CONST_SESSION_APP', "appControlCostos");
$_SESSION[CONST_SESSION_APP]["modoProduccion"] = true;
require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Gastos.php";
require_once "../Models/GastoDetalles.php";
require_once "../Models/RequisicionGasto.php";


use App\Conexion;
use App\Models\Gastos;

$query="SELECT g.* 
FROM gastos g
INNER JOIN gasto_detalles gd ON g.id = gd.gastoId
WHERE g.cerrada = 0 AND g.requisicionId IS null
GROUP BY g.id
HAVING COUNT(gd.id) > 0;";

$respuesta = Conexion::queryAll(CONST_BD_APP, $query, $error);

if (count($respuesta)>0) {
    
    $gasto = new Gastos;
    foreach ($respuesta as $key => $value) {
        $gasto->id = $value["id"];

        $gasto->cerrarGasto();
        
        $gasto->consultar(null,$gasto->id);

        // SE OBTIENEN LOS DATOS DE EMPRESSA
        require_once "../Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null,$gasto->empresa);

        // SE OBTIENEN LOS DATOS DE LOS DETALLES DEL GASTO
        $gastoDetalle = New \App\Models\GastoDetalles;
        $gastoDetalles = $gastoDetalle->consultarPorGasto($gasto->id);
        
        //Se crean los datos para ingreas a las partidas
        $partidas = [];
        foreach ($gastoDetalles as $key => $value) {
            $partidas["costo"][]=$value["costo"];
            $partidas["cantidad"][]=$value["cantidad"];
            $partidas["unidad"][]=$value["unidad"];
            $partidas["numeroParte"][]=$value["numeroParte"];
            $partidas["concepto"][]=$value["observaciones"]." ".$value["numeroEconomico"];
        }
        
        $requisicionGasto =  New \App\Models\RequisicionGasto;

        // Generacion de folio
        $folio = "GACC-".strtoupper($empresa->nomenclaturaOT);

        $datosReq = [
            "empresa" => $empresa->id,
            "gasto" => $gasto->id,
            "folio" => $folio,
            "estatus" => 18,
            "partidas" => $partidas
        ];

        if( !is_null($gasto->usuarioIdRevision)){
            $datosReq["estatus"] = 23; // Si el gasto ya fue revisado, se crea la requisicion como revisada
            $datosReq["usuarioIdAlmacen"] = $gasto->usuarioIdRevision;
        }

        if ( !is_null($gasto->usuarioIdAutorizacion)){
            $datosReq["estatus"] = 19; // Si el gasto ya fue autorizado, se crea la requisicion como pagada
            $datosReq["usuarioIdResponsable"] = $gasto->usuarioIdAutorizacion;
        }

        if ( !$requisicionGasto->crear($datosReq) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

        $datosGasto["requisicionId"] = $requisicionGasto->id;

        $gasto->actualizarRequisicionId($datosGasto);
    }
}

?>