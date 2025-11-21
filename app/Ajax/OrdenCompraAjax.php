<?php

namespace App\Ajax;

session_start();
header("Access-Control-Allow-Origin: https://cc.indheca.net");

// Configuraci��n de Errores
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/home/josue/Escritorio/control-mantenimiento/php_error_log');

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/OrdenCompra.php";
require_once "../Models/Requisicion.php";
require_once "../Models/Inventario.php";
require_once "../Models/InventarioSalida.php";
require_once "../Models/InventarioPartida.php";

require_once "../Requests/SaveObrasRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\OrdenCompra;
use App\Models\Requisicion;
use App\Models\Inventario;
use App\Models\InventarioSalida;
use App\Models\InventarioPartida;


use App\Requests\SaveObrasRequest;

class OrdenCompraAjax
{
	/*=============================================
	TABLA DE ordenes
	=============================================*/
	public function mostrarTabla()
	{   

        try {
            $ordenCompra = New OrdenCompra;
            $ordenCompras = $ordenCompra->consultar();

            $columnas = array();
            array_push($columnas, [ "data" => "consecutivo" ]);
            array_push($columnas, [ "data" => "folio" ]);
            array_push($columnas, [ "data" => "servicio" ]);
            array_push($columnas, [ "data" => "estatus" ]);
            array_push($columnas, [ "data" => "fechaCreacion" ]);
            array_push($columnas, [ "data" => "requisicion" ]);
            array_push($columnas, [ "data" => "creo" ]);
            array_push($columnas, [ "data" => "condicion_pago" ]);
            array_push($columnas, [ "data" => "proveedor" ]);
            array_push($columnas, [ "data" => "polizas" ]);
            array_push($columnas, [ "data" => "montoTotal" ]);
            array_push($columnas, [ "data" => "moneda" ]);
            array_push($columnas, [ "data" => "banco" ]);
            array_push($columnas, [ "data" => "clabe" ]);
            array_push($columnas, [ "data" => "acciones" ]);
            
            $token = createToken();
            
            $registros = array();
            foreach ($ordenCompras as $key => $value) {
                $rutaEdit = Route::names('orden-compra.edit', $value['id']);
                $rutaDestroy = Route::names('orden-compra.destroy', $value['id']);
                $rutaPrint = Route::names('orden-compra.print', $value['id']);
                $folio = mb_strtoupper(fString($value['id']));

                if ($value['polizasContablesValor'] == 0){
                    $polizasContableValor = 'SIN PÓLIZA';
                } elseif( $value["polizasContablesValor"] == $value["total"] ){
                    $polizasContableValor = 'COMPLETAS';
                } else {
                    $polizasContableValor = 'INCOMPLETA';
                }

                array_push( $registros, [
                    "consecutivo" => ($key + 1),
                    "folio" => $value["folio"],
                    "servicio" => mb_strtoupper(fString($value["servicio.folio"])),
                    "estatus" => mb_strtoupper(fString($value["estatus.descripcion"])),
                    "colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                    "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
                    "fechaCreacion" => ( is_null($value["fechaCreacion"]) ? '' : fFechaLarga($value["fechaCreacion"]) ),
                    "requisicion" => $value["requisicion.folio"],
                    "creo" => mb_strtoupper(fString($value["creo"])),
                    "condicion_pago" => mb_strtoupper($condicionPago = ( $value["condicionPagoId"]== 1) ? 'CONTADO' : 'CRÉDITO'),
                    "proveedor" => mb_strtoupper(fString($value["proveedor"])),
                    "montoTotal" => number_format($value["total"], 2),
                    "polizas" => mb_strtoupper($polizasContableValor),
                    "moneda"  => $value["monedaNombre"],
                    "banco" => $value["nombreBanco"],
                    "clabe" => $value["cuentaClave"],
                    "acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                    <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                        <input type='hidden' name='_method' value='DELETE'>
                                        <input type='hidden' name='_token' value='{$token}'>
                                        <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                            <i class='far fa-times-circle'></i>
                                        </button>
                                    </form> 
                                    <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>"   
                ] );
            }

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos']['columnas'] = $columnas;
            $respuesta['datos']['registros'] = $registros;


		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

        echo json_encode($respuesta);
	}
    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    public $obraId;
    public $estatusId;
    public $fechaInicial;
    public $fechaFinal;

    public function consultarFiltros()
    {
        $arrayFiltros = array();

        if ( $this->estatusId > 0 ) array_push($arrayFiltros, [ "campo" => "OC.estatusId", "operador" => "=", "valor" => $this->estatusId ]);
        if ( $this->obraId > 0 ) array_push($arrayFiltros, [ "campo" => "O.id", "operador" => "=", "valor" => $this->obraId ]);
        if ( $this->fechaInicial > 0 ) array_push($arrayFiltros, [ "campo" => "OC.fechaCreacion", "operador" => ">=", "valor" => "'".fFechaSQL($this->fechaInicial)." 00:00:00'" ]);
        if ( $this->fechaFinal > 0 ) array_push($arrayFiltros, [ "campo" => "OC.fechaCreacion", "operador" => "<=", "valor" => "'".fFechaSQL($this->fechaFinal)." 23:59:59'" ]);

        $ordenCompra = New OrdenCompra;
        $ordenesCompra = $ordenCompra->consultarFiltros($arrayFiltros);

        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "servicio" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fechaCreacion" ]);
        array_push($columnas, [ "data" => "requisicion" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "condicion_pago" ]);
        array_push($columnas, [ "data" => "proveedor" ]);
        array_push($columnas, [ "data" => "polizas" ]);
        array_push($columnas, [ "data" => "montoTotal" ]);
        array_push($columnas, [ "data" => "moneda" ]);
        array_push($columnas, [ "data" => "banco" ]);
        array_push($columnas, [ "data" => "clabe" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        
        $token = createToken();
        
        $registros = array();
        foreach ($ordenesCompra as $key => $value) {
                $rutaEdit = Route::names('orden-compra.edit', $value['id']);
                $rutaDestroy = Route::names('orden-compra.destroy', $value['id']);
                $rutaPrint = Route::names('orden-compra.print', $value['id']);
                $folio = mb_strtoupper(fString($value['id']));

                if ($value['polizasContablesValor'] == 0){
                    $polizasContableValor = 'SIN PÓLIZA';
                } elseif( $value["polizasContablesValor"] == $value["total"] ){
                    $polizasContableValor = 'COMPLETAS';
                } else {
                    $polizasContableValor = 'INCOMPLETA';
                }

                array_push( $registros, [
                    "consecutivo" => ($key + 1),
                    "folio" => $value["folio"],
                    "servicio" => mb_strtoupper(fString($value["servicio.folio"])),
                    "estatus" => mb_strtoupper(fString($value["estatus.descripcion"])),
                    "colorTexto" => mb_strtoupper(fString($value["estatus.colorTexto"])),
                    "colorFondo" => mb_strtoupper(fString($value["estatus.colorFondo"])),
                    "fechaCreacion" => ( is_null($value["fechaCreacion"]) ? '' : fFechaLarga($value["fechaCreacion"]) ),
                    "requisicion" => $value["requisicion.folio"],
                    "creo" => mb_strtoupper(fString($value["creo"])),
                    "condicion_pago" => mb_strtoupper($condicionPago = ( $value["condicionPagoId"]== 1) ? 'CONTADO' : 'CRÉDITO'),
                    "proveedor" => mb_strtoupper(fString($value["proveedor"])),
                    "montoTotal" => number_format($value["total"], 2),
                    "polizas" => mb_strtoupper($polizasContableValor),
                    "moneda"  => $value["monedaNombre"],
                    "banco" => $value["nombreBanco"],
                    "clabe" => $value["cuentaClave"],
                    "acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                    <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                        <input type='hidden' name='_method' value='DELETE'>
                                        <input type='hidden' name='_token' value='{$token}'>
                                        <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                            <i class='far fa-times-circle'></i>
                                        </button>
                                    </form> 
                                    <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>"   
                ] );
            }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

    /*=============================================
	FUNCION PARA VER TODOS LOS ARCHIVOS
	=============================================*/
    public $requisicionId;

    public function verArchivos()
    {
        try {
            $requisicion = new Requisicion;
            $ordenCompra = new OrdenCompra;
            $inventario = new Inventario;
            $inventarioSalida = new InventarioSalida;
            $inventarioPartida = new InventarioPartida;

            $respuesta = [];

            if ($requisicion->consultar(null, $_GET["requisicionId"])) {

                $requisicion->consultarPolizas();
                $requisicion->consultarComprobantes();
                $requisicion->consultarFacturas();
                $requisicion->consultarCotizaciones();
                $requisicion->consultarVales();
                $requisicion->consultarDetalles();
                $requisicion->consultarSoportes();

                $ordenCompra->ordenCompraId = $_GET["ordenCompraId"];

                $ordenDeCompraDatos = $ordenCompra->consultarOrdenDeCompra();

                $rutaOrdenDeCompra = $this->crearPDFOrdenDeCompra($ordenDeCompraDatos);

                $rutaRequisicion = $this->crearPDFRequisicion($requisicion);

                // //OBTENER DATOS DE LAS ENTRADAS DE ALMACEN
                // $ordenCompraFolio = $ordenDeCompraDatos[0]["folio"];

                // $entradasInventario = $inventario->obtenerEntradasPorOrdenCompra($ordenCompraFolio);

                // // OBTENER LAS SALIDAS DE LAS ENTRADAS
                // $salidasInventario = [];

                // foreach ($entradasInventario as $key => $value) {

                //     $salidasDeEntrada = $inventarioSalida->consultarInventarioPorId($value["id"]);
                    
                //     if (!empty($salidasDeEntrada)) {
                //         // Si hay salidas, las agregamos al arreglo principal
                //         $salidasInventario = array_merge($salidasInventario, $salidasDeEntrada);
                //     }
                // }

                //OBTENER DATOS DE LAS SALIDAS DE ALMACEN
                $archivos = [];

                foreach( $requisicion->polizas as $file ) {
                    if ( $file["formato"] === 'application/pdf' ) {
                        $ruta = $_SERVER['DOCUMENT_ROOT'] . ltrim($file["ruta"], '/');
                        $archivos[] = escapeshellarg($ruta);
                    }
                }

                // Agregar la ruta de orden de compra a $archivos
                // 1. Comprobantes de pago (PDFs)
                if (!empty($requisicion->comprobantesPago)) {
                    foreach ($requisicion->comprobantesPago as $file) {
                        if ($file["formato"] === 'application/pdf') {
                            $ruta = $_SERVER['DOCUMENT_ROOT'] . ltrim($file["ruta"], '/');
                            $archivos[] = escapeshellarg($ruta);
                        }
                    }
                }

                // 2. Orden de compra
                $archivos[] = escapeshellarg($rutaOrdenDeCompra);

                // 3. Requisición
                $archivos[] = escapeshellarg(realpath('../../reportes/tmp/requisicion.pdf'));

                // 4. Cotizaciones, facturas, vales de almacén (PDFs)
                $categorias = [
                    $requisicion->cotizaciones,
                    $requisicion->facturas,
                    $requisicion->valesAlmacen,
                    $requisicion->soportes
                ];

                foreach ($categorias as $documentos) {
                    foreach ($documentos as $file) {
                        if ($file["formato"] === 'application/pdf') {
                            $ruta = $_SERVER['DOCUMENT_ROOT'] . ltrim($file["ruta"], '/');
                            $archivos[] = escapeshellarg($ruta);
                        }
                    }
                }

  
                $nombreArchivo = "requisicion_" . $requisicion->folio . ".pdf";
                $rutaSalida = "/tmp/" . $nombreArchivo;

                if (file_exists($rutaSalida)) {
                    unlink($rutaSalida);
                }

                $comando = "qpdf --empty --pages " . implode(" ", $archivos) . " -- " . $rutaSalida;

                shell_exec($comando);

                $rutaDestino = __DIR__ . "/../../reportes/requisiciones/" . $nombreArchivo;

                if (file_exists($rutaSalida)) {
                    if (!is_dir(dirname($rutaDestino))) {
                        mkdir(dirname($rutaDestino), 0777, true);
                    }

                    if (copy($rutaSalida, $rutaDestino)) {
                        unlink($rutaSalida);
                    } else {
                        throw new \Exception("No se pudo copiar el PDF generado al destino.");
                    }
                }

                if (file_exists($rutaDestino)) {

                    // Eliminar archivo temporal si está definido y existe
                    if (!empty($rutaRequisicion) && file_exists($rutaRequisicion)) {
                        unlink($rutaRequisicion);
                    }

                    // Eliminar archivos de órdenes de compra si existen y es un arreglo
                    if (!empty($rutasOrdenDeCompra) && is_array($rutasOrdenDeCompra)) {
                        foreach ($rutasOrdenDeCompra as $ruta) {
                            $rutaLimpia = str_replace("'", "", $ruta);
                            if (file_exists($rutaLimpia)) {
                                unlink($rutaLimpia);
                            }
                        }
                    }

                    $respuesta = [
                        'error' => false,
                        'ruta' => '/reportes/requisiciones/' . $nombreArchivo,
                        'ordenDeCompraDatos' => $ordenDeCompraDatos,
                    ];
                } else {
                    throw new \Exception("Error al fusionar los archivos PDF.");
                }

                echo json_encode($respuesta);

            }

        } catch (\Exception $e) {
            echo json_encode([
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ]);
        }
    }

    function crearPDFOrdenDeCompra($datos)
    {
        include "../../reportes/OrdenCompraConjuntoPDF.php";

        $ruta = generarPDFSimple($datos);  // obtener la ruta retornada

        return $ruta; // si quieres usarla fuera también
    }

    function crearPDFRequisicion($requisicion)
    {

        require_once "../../app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null, $requisicion->servicio['empresaId']);

        require_once "../../app/Models/MantenimientoTipo.php";
        $mantenimientoTipo = New \App\Models\MantenimientoTipo;
        $mantenimientoTipo->consultar(null, $requisicion->servicio['mantenimientoTipoId']);

        require_once "../../app/Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinaria->consultar(null, $requisicion->servicio['maquinariaId']);

        require_once "../../app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obra->consultar(null, $requisicion->servicio['obraId'] ?? $maquinaria->obraId);

        require_once "../../app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $requisicion->usuarioIdCreacion);

        $usuarioNombre = $usuario->nombre;
        $solicito = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
        if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . $usuario->apellidoMaterno;
        $solicitoFirma = $usuario->firma;
        unset($usuario);

        $responsableFirma = null;
        $revisoFirma = null;

        $usuario = New \App\Models\Usuario;
        $almacen = 'VB ALMACEN';
        $reviso = '';
        if ( !is_null($requisicion->usuarioIdAlmacen) ){
            $usuario->consultar(null, $requisicion->usuarioIdAlmacen);
            $usuario->consultarPerfiles();

            if (in_arrayi('comprobaciones', $usuario->perfiles)) {
                $almacen = 'VB COMPROBACIONES';
            }

            $reviso = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . $usuario->apellidoMaterno;
            $revisoFirma = $usuario->firma;
            unset($usuario);
        }


        $responsable = '';
        if ( !is_null($requisicion->usuarioIdResponsable) ) {
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $requisicion->usuarioIdResponsable);

            $usuarioNombre = $usuario->nombre;
            $responsable = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $responsable .= ' ' . $usuario->apellidoMaterno;
            $responsableFirma = $usuario->firma;
            unset($usuario);
        }

        if ( $requisicion->servicio['empresaId'] == 2 ){
            include "../../reportes/requisicionConjuntoIndheca.php";
        }else{
            include "../../reportes/requisicionConjunto.php";
        }
    }

    function crearPDFValeEntrada($entradasInventario){

        include "../../reportes/ValeEntradaConjuntoPDF.php";

        $ruta = generarPDFValeEntrada($entradasInventario);  // obtener la ruta retornada

        return $ruta; // si quieres usarla fuera también
    }

    function crearPDFValeSalida($salidasInventario){

        include "../../reportes/ValeSalidaConjuntoPDF.php";

        $ruta = generarPDFValeSalida($salidasInventario);  // obtener la ruta retornada

        return $ruta; // si quieres usarla fuera también
    }

    public function asignarDocumentos()
    {
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $ordenCompra = New OrdenCompra;
            $ordenCompra->id = $_POST["ordenCompraId"];
            $ordenCompra->documentos = $_POST["documentos"];

            if ( !$ordenCompra->asignarDocumentos() ) throw new \Exception("Hubo un error al intentar asignar los documentos a la Orden de Compra, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => true,
                'respuestaMessage' => "Los documentos fueron asignados correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

        public function autorizarAdicional()
    {
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $ordenCompra = New OrdenCompra;
            $ordenCompra->id = $_POST["ordenCompraId"];

            if ( !$ordenCompra->autorizarAdicional() ) throw new \Exception("Hubo un error al intentar autorizar la Orden de Compra, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => true,
                'respuestaMessage' => "La Orden de Compra fue autorizada correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

}

/*=============================================
TABLA DE OBRAS
=============================================*/

try {

    $ordenAjax = New OrdenCompraAjax();

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "agregar" ) {

            /*=============================================
            CREAR DETALLE DE OBRA
            =============================================*/
            $ordenAjax->crear();

        } else if ( $_POST["accion"] == "agregarSemana" ) {
			/*=============================================
            AGREGAR SEMANA
            =============================================*/
            $ordenAjax->addSemana();
		} else if ( $_POST["accion"] == "autorizarAdicional" ) {
            /*=============================================
            AUTORIZAR ADICIONAL
            =============================================*/
            $ordenAjax->autorizarAdicional();
        } elseif ( $_POST["accion"] == "asignarDocumentos" ) {  
            /*=============================================
            ASIGNAR DOCUMENTOS
            =============================================*/
            $ordenAjax->asignarDocumentos();
        }
        else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }
    } elseif (isset($_GET["obraId"])) {
        /*=============================================
        CONSULTAR FILTROS
        =============================================*/
        $ordenAjax->estatusId = $_GET["estatusId"];
        $ordenAjax->fechaInicial = $_GET["fechaInicial"];
        $ordenAjax->fechaFinal = $_GET["fechaFinal"];
        $ordenAjax->obraId = $_GET["obraId"];
        $ordenAjax->consultarFiltros();
    } elseif ( isset($_GET["accion"]) ){
        /*=============================================
        VER ARCHIVOS
        =============================================*/
        $ordenAjax->requisicionId = $_GET["requisicionId"];
        $ordenAjax->verArchivos();
    }
    else{

        /*=============================================
        TABLA DE ORDENES DE COMPRA
        =============================================*/
		$ordenAjax->mostrarTabla();

    }


} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}


?>
