<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Puesto.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Puesto;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class PuestoAjax
{

	/*=============================================
	TABLA DE PUESTOS
	=============================================*/
	public function mostrarTabla()
	{

		try {
			
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "puesto", "ver") ) throw new \Exception("No está autorizado a agregar puestos");

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

		$puesto = New Puesto;
        $puestos = $puesto->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "nombre" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();

        
        foreach ($puestos as $key => $value) {
            

            $id_puesto = $value['id'];
        	$rutaEdit = Route::names('puestos.edit', $value['id']);
        	$rutaDestroy = Route::names('puestos.destroy', $value['id']);

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "nombre" => fString($value["nombre"]),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
                                                            <button type='button' class='btn btn-xs btn-danger eliminar' puesto='{$id_puesto}'>
									                            <i class='far fa-times-circle'></i>
									                        </button>
								                     </form>" ] );
        }

        
        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;


		} catch (\Exception $e) {
            $respuesta = [
                'codigo' => 500, // Código de error para problemas del servidor
                'error' => true,
                'errorMessage' => $e->getMessage(), // El mensaje del error
                'errorCode' => $e->getCode() // Código específico de la excepción, si existe
            ];
        }
        echo json_encode($respuesta);
		
	}

	/*=============================================
	TABLA DE PUESTOS DEL USUARIO
	=============================================*/

	public $id_usuario;

	public function mostrarTablaPuestoUsuario()
	{
		$puesto = New Puesto;
        $puestos = $puesto->consultarPuesto("id_usuario",$this->id_usuario);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "puesto_usuario" ]);
        array_push($columnas, [ "data" => "nombre_zona" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();

        foreach ($puestos as $key => $value) {
            

            $id_puesto_usuario = $value['id_puesto_usuario'];

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "puesto_usuario" => fString($value["puesto_usuario"]),
        							  "nombre_zona" => fString($value["nombre_zona"]),
        							  "acciones" => "
			        							     <form method='POST'  style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
                                                            <button  type='button' class='btn btn-xs btn-danger eliminar' puesto='{$id_puesto_usuario}'>
									                            <i class='far fa-times-circle'></i>
									                        </button>
								                     </form>" 
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
	AGREGAR PUESTO
	=============================================*/	
	public $token;
	public $id_puesto;
	public $id_zona;


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
			'id_usuario'=>$this->id_usuario,
			'id_puesto'=>$this->id_puesto,
			'id_zona'=>$this->id_zona
		];

        try{
			$puesto = New Puesto;

			// VERIFICAR SI YA EXISTE EL PUESTO ASIGNADO
			if(!empty($puesto->verificarPuestoUsuario($datos))){
				$respuesta = array();
				$respuesta['codigo'] = 300;
				$respuesta['error'] = false;
				$respuesta['respuestaMessage'] = "Puesto asignado ya existe";

				 echo json_encode($respuesta);
				 return;
			}

			// CREAR PUESTO
			$respuesta = $puesto->crearPuestoUsuario($datos);

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

	public $id_puesto_usuario;

	public function eliminarPuesto() {
		try {
			$puesto = new Puesto;
			$puesto->id = $this->id_puesto_usuario; // ✅ Usa la propiedad de la clase
	
			if ($puesto->eliminar(true)) { // ✅ Asegúrate de que eliminar() retorne true/false
				$respuesta = [
					'codigo' => 200,
					'error' => false,
					'id_puesto_usuario' => $this->id_puesto_usuario,
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

$puestoAjax = new PuestoAjax();

if ( isset($_POST["accion"]) ) {

	if($_POST["accion"] == "asignarPuesto" ){

		/*=============================================
		ASIGNAR PUESTO
		=============================================*/ 
		$puestoAjax->token = $_POST["_token"];
		$puestoAjax->id_puesto = $_POST["id_puesto"];
		$puestoAjax->id_usuario = $_POST["id_usuario"];
		$puestoAjax->id_zona = $_POST["id_zona"];

		$puestoAjax->agregar();
	}else if($_POST["accion"] == "eliminarPuestoAsignado" ){

		/*=============================================
		DESIGNAR PUESTO
		=============================================*/ 
		$puestoAjax->token = $_POST["_token"];
		$puestoAjax->id_puesto_usuario = $_POST["id_puesto_usuario"];

		$puestoAjax->eliminarPuesto();
	}

} 
else if ( isset($_GET["accion"]) ) {

    /*=============================================
    MOSTRAR TABLA DE PUESTOS DEL USUARIO
    =============================================*/ 
    $puestoAjax->id_usuario = $_GET["id_usuario"];
    $puestoAjax->mostrarTablaPuestoUsuario();

} 
else {

    /*=============================================
    TABLA DE PUESTOS
    =============================================*/
    $puestoAjax->mostrarTabla();

}