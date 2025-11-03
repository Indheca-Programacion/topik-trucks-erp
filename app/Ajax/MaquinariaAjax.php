<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Maquinaria.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Maquinaria;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class MaquinariaAjax
{

	/*=============================================
	TABLA DE MAQUINARIAS
	=============================================*/
	public function mostrarTabla()
	{
    	$maquinaria = New Maquinaria;
        $maquinarias = $maquinaria->consultar();

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "tipoMaquinaria" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "numeroFactura" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "marca" ]);
        array_push($columnas, [ "data" => "modelo" ]);
        array_push($columnas, [ "data" => "year" ]);
        array_push($columnas, [ "data" => "serie" ]);
        array_push($columnas, [ "data" => "color" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "almacen" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($maquinarias as $key => $value) {
        	$rutaEdit = Route::names('maquinarias.edit', $value['id']);
        	$rutaDestroy = Route::names('maquinarias.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "empresa" => fString($value["empresas.nombreCorto"]),
                "tipoMaquinaria" => fString($value["maquinaria_tipos.descripcion"]),
                "numeroEconomico" => fString($value["numeroEconomico"]),
                "numeroFactura" => fString($value["numeroFactura"]),
                "descripcion" => fString($value["descripcion"]),
                "marca" => fString($value["marcas.descripcion"]),
                "modelo" => fString($value["modelos.descripcion"]),
                "year" => $value["year"],
                "serie" => fString($value["serie"]),
                "color" => fString($value["colores.descripcion"]),
                "estatus" => fString($value["estatus.descripcion"]),
                "ubicacion" => fString($value["ubicaciones.descripcion"]),
                "almacen" => fString($value["almacenes.descripcion"]),
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

	/*=============================================
	CONSULTAR MAQUINARIA
	=============================================*/	
	public function consultar()
	{
        $maquinaria = New Maquinaria;

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['maquinaria'] = $maquinaria->consultar(null , $this->maquinariaId);

        echo json_encode($respuesta);
	}

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    public $empresaId;
    public $maquinariaTipoId;
    public $ubicacionId;

    public function consultarFiltros()
    {
        $arrayFiltros = array();

        if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "M.empresaId", "valor" => $this->empresaId ]);
        if ( $this->maquinariaTipoId > 0 ) array_push($arrayFiltros, [ "campo" => "M.maquinariaTipoId", "valor" => $this->maquinariaTipoId ]);
        if ( $this->ubicacionId > 0 ) array_push($arrayFiltros, [ "campo" => "M.ubicacionId", "valor" => $this->ubicacionId ]);
        if ( $this->obraId > 0 ) array_push($arrayFiltros, [ "campo" => "M.obraId", "valor" => $this->obraId ]);

        $maquinaria = New Maquinaria;
        $maquinarias = $maquinaria->consultarFiltros($arrayFiltros);

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "tipoMaquinaria" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "numeroFactura" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "marca" ]);
        array_push($columnas, [ "data" => "modelo" ]);
        array_push($columnas, [ "data" => "year" ]);
        array_push($columnas, [ "data" => "serie" ]);
        array_push($columnas, [ "data" => "color" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "almacen" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($maquinarias as $key => $value) {
            $rutaEdit = Route::names('maquinarias.edit', $value['id']);
            $rutaDestroy = Route::names('maquinarias.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['descripcion']));

            array_push( $registros, [
                "id" => $value["id"],
                "consecutivo" => ($key + 1),
                "empresa" => fString($value["empresas.nombreCorto"]),
                "tipoMaquinaria" => fString($value["maquinaria_tipos.descripcion"]),
                "numeroEconomico" => fString($value["numeroEconomico"]),
                "numeroFactura" => fString($value["numeroFactura"]),
                "descripcion" => fString($value["descripcion"]),
                "marca" => fString($value["marcas.descripcion"]),
                "modelo" => fString($value["modelos.descripcion"]),
                "year" => $value["year"],
                "serie" => fString($value["serie"]),
                "color" => fString($value["colores.descripcion"]),
                "estatus" => fString($value["estatus.descripcion"]),
                "ubicacion" => fString($value["ubicaciones.descripcion"]),
                "almacen" => fString($value["almacenes.descripcion"]),
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

    /*=============================================
    GUARDAR IMAGENES
    =============================================*/
    public function guardarImagenes()
    {
        try {
            
            $maquinaria = New Maquinaria;
            $maquinaria->id = $this->maquinaria;
            $response = $maquinaria->guardarImagenes($this->detalle, $this->fecha);

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Imagenes guardadas correctamente."
            ];
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
    ELIMINAR IMAGENES
    =============================================*/
    public function eliminarImagen()
    {
        try {
            
            $maquinaria = New Maquinaria;
            $response = $maquinaria->eliminarImagen($this->archivoId);

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Imagen eliminada correctamente."
            ];
        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

        echo json_encode($respuesta);
    }
}

$maquinariaAjax = new MaquinariaAjax();

if ( isset($_POST["accion"])) {
    if ( $_POST["accion"] == 'guardarImagenes'){
        /*=============================================
        CANCELAR PARTIDA
        =============================================*/
        $maquinariaAjax->token = $_POST["_token"];
        $maquinariaAjax->maquinaria = $_POST["maquinariaId"];
        $maquinariaAjax->fecha = $_POST["fecha"];
        $maquinariaAjax->detalle = $_POST["detalle"];
        // $maquinariaAjax->imagenes = $_POST["images"];
        $maquinariaAjax->guardarImagenes();
    } elseif ( $_POST["accion"] == 'eliminarFoto' ) {
        /*=============================================
        ELIMINAR IMAGEN
        =============================================*/
        $maquinariaAjax->token = $_POST["_token"];
        $maquinariaAjax->archivoId = $_POST["archivoId"];
        $maquinariaAjax->eliminarImagen();
    } else {

        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => "Realizó una petición desconocida."
        ];

        echo json_encode($respuesta);

    }
}elseif ( isset($_GET["maquinariaId"]) ) {

	/*=============================================
	CONSULTAR MAQUINARIA
	=============================================*/	
	$maquinariaAjax->maquinariaId = $_GET["maquinariaId"];
	$maquinariaAjax->consultar();

} elseif ( isset($_GET["empresaId"]) && isset($_GET["maquinariaTipoId"]) && isset($_GET["ubicacionId"]) ) {

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    $maquinariaAjax->empresaId = $_GET["empresaId"];
    $maquinariaAjax->maquinariaTipoId = $_GET["maquinariaTipoId"];
    $maquinariaAjax->ubicacionId = $_GET["ubicacionId"];
    $maquinariaAjax->obraId = $_GET["obraId"]??0;
    $maquinariaAjax->consultarFiltros();

} else {

	/*=============================================
	TABLA DE MAQUINARIAS
	=============================================*/
	$maquinariaAjax->mostrarTabla();

}
