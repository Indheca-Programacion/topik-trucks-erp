<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Puesto.php";
require_once "../Models/ConfiguracionPuestoMantenimiento.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Puesto;
use App\Models\ConfiguracionPuestoMantenimiento;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ConfiguracionPuestoMantenimientoAjax
{

	/*=============================================
	TABLA DE PUESTO TIPO DE MANTENIMIENTO
	=============================================*/
	public function mostrarTabla()
	{
		$puestoMantenimiento = New ConfiguracionPuestoMantenimiento;
        $puestoMantenimiento = $puestoMantenimiento->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "puesto" ]);
        array_push($columnas, [ "data" => "tipoMantenimiento" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($puestoMantenimiento as $key => $value) {

			$id_puesto_mantenimiento = $value["id_puesto_tipo_mantenimiento"];


        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "puesto" => fString($value["nombre_puesto"]),
        							  "tipoMantenimiento" => fString($value["nombre_tipo_mantenimiento"]),
        							  "acciones" => "
			        							     <form method='POST'  style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar'	  puestoMan='{$id_puesto_mantenimiento}'>
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
	AGREGAR PUESTO MANTENIMIENTO
	=============================================*/	
	public $token;
	public $id_puesto;
	public $id_MantenimientoTipo;


	public function agregar(){

		if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

		$usuario = New Usuario;
		$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
		if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "puesto", "crear") ) throw new \Exception("No está autorizado a agregar puestos");

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

		$datos = [ 
			'id_puesto'=>$this->id_puesto,
			'id_tipo_mantenimiento'=>$this->id_MantenimientoTipo
		];

        try{
			$puesto = New Puesto;

			// VERIFICAR SI YA EXISTE EL PUESTO ASIGNADO
			if(!empty($puesto->verificarPuestoMantenimiento($datos))){
				$respuesta = array();
				$respuesta['codigo'] = 300;
				$respuesta['error'] = false;
				$respuesta['respuestaMessage'] = "La asignacion ya existe";

				 echo json_encode($respuesta);
				 return;
			}

			// CREAR PUESTO
			$respuesta = $puesto->crearPuestoMantenimiento($datos);

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['respuestaMessage'] = "Puesto asignado con exito";

		}catch (\Exception $e) {
            $respuesta = [
                'codigo' => 500, // Código de error para problemas del servidor
                'error' => true,
                'errorMessage' => $e->getMessage(), // El mensaje del error
                'errorCode' => $e->getCode() // Código específico de la excepción, si existe
            ];
        }

        echo json_encode($respuesta);


	}

	public $id_puesto_mantenimiento;

	public function eliminarPuestoMantenimiento() {
		try {
			$puesto = new Puesto;
			$puesto->id = $this->id_puesto_mantenimiento; // ✅ Usa la propiedad de la clase
	
			if ($puesto->eliminarPuestoMantenimiento()) { // ✅ Asegúrate de que eliminar() retorne true/false
				$respuesta = [
					'codigo' => 200,
					'error' => false,
					'id_puesto_mantenimiento' => $this->id_puesto_mantenimiento,
					'respuestaMessage' => "Puesto asignado eliminado con éxito"
				];
			} else {
				$respuesta = [
					'codigo' => 400,
					'error' => true,
					'respuestaMessage' => "No se pudo eliminar el puesto"
				];
			}
	
		} catch (\Exception $e) {
			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage(),
				'errorCode' => $e->getCode()
			];
		}
	
		echo json_encode($respuesta);
	}
}

$puestoMantenimientoAjax = new ConfiguracionPuestoMantenimientoAjax();

if ( isset($_POST["accion"]) ) {

	if($_POST["accion"] == "asignarPuestoMantenimiento" ){

		/*=============================================
		ASIGNAR PUESTO MANTENIMIENTO
		=============================================*/ 
		$puestoMantenimientoAjax->token = $_POST["_token"];
		$puestoMantenimientoAjax->id_puesto = $_POST["id_puesto"];
		$puestoMantenimientoAjax->id_MantenimientoTipo = $_POST["id_MantenimientoTipo"];

		$puestoMantenimientoAjax->agregar();
	}else if($_POST["accion"] == "eliminarPuestoMantenimiento" ){

		/*=============================================
		DESIGNAR PUESTO
		=============================================*/ 
		$puestoMantenimientoAjax->token = $_POST["_token"];
		$puestoMantenimientoAjax->id_puesto_mantenimiento = $_POST["id_puesto_mantenimiento"];

		$puestoMantenimientoAjax->eliminarPuestoMantenimiento();
	}
} else {

    /*=============================================
    TABLA DE COLORES
    =============================================*/
    $puestoMantenimientoAjax->mostrarTabla();

}