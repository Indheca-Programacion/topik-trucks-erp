<?php

namespace App\Ajax;

session_start();
header("Access-Control-Allow-Origin: https://cc.indheca.net");

// Configuración de Errores
ini_set('display_errors', 1);
ini_set('log_errors', 1);

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Proveedor.php";
require_once "../Models/DatosBancarios.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveDatosBancariosRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Proveedor;
use App\Models\DatosBancarios;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveDatosBancariosRequest;

class ProveedorAjax
{

    /*=============================================
    TABLA DE PROVEEDORES
    =============================================*/
    public function mostrarTabla()
    {
        $proveedor = New Proveedor;
        $proveedores = $proveedor->consultar();

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "activo" ]);
        array_push($columnas, [ "data" => "personaFisica" ]);
        array_push($columnas, [ "data" => "proveedor" ]);
        array_push($columnas, [ "data" => "nombreComercial" ]);
        array_push($columnas, [ "data" => "rfc" ]);
        array_push($columnas, [ "data" => "correo" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($proveedores as $key => $value) {
            $rutaEdit = Route::names('proveedores.edit', $value['id']);
            $rutaDestroy = Route::names('proveedores.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['proveedor']));

            array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "activo" => ( $value["activo"] ) ? 'Si' : 'No',
                "personaFisica" => ( $value["personaFisica"] ) ? 'Si' : 'No',
                "proveedor" => mb_strtoupper(fString($value["proveedor"])),
                "nombreComercial" => mb_strtoupper(fString($value["nombreComercial"])),
                "rfc" => mb_strtoupper(fString($value["rfc"])),
                // "correo" => fString($value["correo"]),
                "correo" => mb_strtolower(fString($value["correo"])),
                "acciones" =>  "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </form>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

    public function agregarDatosBancarios()
    {
        try{
            $request = SaveDatosBancariosRequest::validated();

            if( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            if ( errors() ) {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];

                unset($_SESSION[CONST_SESSION_APP]["errors"]);

                echo json_encode($respuesta);
                return;

            }

            $datosBancarios = New DatosBancarios;
            $datosBancarios->crear($request);
            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "Datos bancarios agregados correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
    }

    public function editarDatoBancario()
    {
        try{
            $request = SaveDatosBancariosRequest::validated();

            $datosBancarios = New DatosBancarios;
            $datosBancarios->id = $request["datoBancarioId"];
            $datosBancarios->actualizar($request);
            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "Dato bancario editado correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
    }

    public function eliminarDatoBancario()
    {
        try{

            $datosBancarios = New DatosBancarios;
            $datosBancarios->id = $this->datoBancarioId;
            $datosBancarios->eliminar();
            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "Dato bancario eliminado correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
    }

    public function obtenerDatoBancarioPorId()
    {
        try{
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $datosBancarios = New DatosBancarios;
            $datoBancario = $datosBancarios->consultar(null,$this->datoBancarioId);
            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos'] = $datoBancario;
            $respuesta['mensaje'] = "Dato bancario obtenido correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
        exit;
    }

    /*=============================================
    LISTADO DE DATOS BANCARIOS POR ID PROVEEDOR
    =============================================*/
    public $proveedorId;

    public function mostrarListadoDatosBancarios()
    {

        try{

            $datoBancario = new DatosBancarios;
            $datosBancarios = $datoBancario->consultarDatosBancariosProveedor($this->proveedorId);

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos'] = $datosBancarios;
            $respuesta['mensaje'] = "Mostrando los datos con exito";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
        exit;
    }
    
    // ACTUALIZAR DATOS DEL PROVEEDOR
    public function actualizarDatosProveedor()
    {
        try{
            if( !usuarioAutenticadoProveedor() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            if ( errors() ) {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];

                unset($_SESSION[CONST_SESSION_APP]["errors"]);

                echo json_encode($respuesta);
                return;

            }   

            $proveedor = new Proveedor;
            $respuesta = $proveedor->actualizarDatosIncialesProveedor($this->datos);

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "Datos actualizados correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
        echo json_encode($respuesta);
    }

    // LISTADO DE VENDEDORES POR ID PROVEEDOR
    public function mostrarListadoVendedores()
    {

        try{

            $proveedor = new Proveedor;
            $vendedores = $proveedor->consultarVendedoresPorProveedor($this->proveedorId);

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['vendedores'] = $vendedores;
            $respuesta['mensaje'] = "Mostrando los datos con exito";

        } catch (Exception $e) {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
        }

        echo json_encode($respuesta);
        exit;
    }
}

$proveedorAjax = new ProveedorAjax;
try {
    if (isset($_GET['accion'])) {

        if($_GET['accion'] == 'obtenerDatoBancarioPorId')
        {
            $proveedorAjax->datoBancarioId = $_GET["datoBancarioId"];
            $proveedorAjax->obtenerDatoBancarioPorId();
        }else if($_GET['accion'] == 'selectDatosBancarios')
        {
            $proveedorAjax->proveedorId = $_GET["proveedorId"];
            $proveedorAjax->mostrarListadoDatosBancarios();
        }else if($_GET['accion'] == 'obtenerVendedores')
        {
            $proveedorAjax->proveedorId = $_GET["proveedorId"];
            $proveedorAjax->mostrarListadoVendedores();
        }
    }   
    if ( isset($_POST['accion'])) {

        /*=============================================
        AGREGAR DATO BANCARIO
        =============================================*/
        if($_POST["accion"] == "agregarDatosBancarios" ){
            $proveedorAjax->agregarDatosBancarios();
	    } elseif( $_POST['accion'] == 'editarDatoBancario'){
            $proveedorAjax->editarDatoBancario();
        }else if( $_POST['accion'] == 'actualizarDatosProveedor'){
            foreach (json_decode($_POST['datos'], true) as $item) {
                $proveedorAjax->datos[$item['key']] = $item['value'];
            }   
            $proveedorAjax->actualizarDatosProveedor();
        } elseif( $_POST['accion'] == 'eliminarDatoBancario'){
            $proveedorAjax->datoBancarioId = $_POST["datoBancarioId"];
            $proveedorAjax->eliminarDatoBancario();
        }else {
            $respuesta = array();
            $respuesta['codigo'] = 500;
            $respuesta['error'] = true;
            $respuesta['mensaje'] = "No existe la acción";
            echo json_encode($respuesta);
    exit;
        }
    } else {
        /*=============================================
        TABLA DE PROVEEDORES
        =============================================*/
        $proveedorAjax->mostrarTabla();
    }
} catch (\Exception $e) {
    $respuesta = array();
    $respuesta['codigo'] = 500;
    $respuesta['error'] = true;
    $respuesta['mensaje'] = $e->getMessage();
    echo json_encode($respuesta);
    exit;
}
