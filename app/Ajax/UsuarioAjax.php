<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
// require_once "../Models/Empresa.php";
require_once "../Controllers/Autorizacion.php";
// require_once "../Controllers/Validacion.php";

use App\Route;
use App\Models\Usuario;
// use App\Models\Empresa;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class UsuarioAjax
{

	/*=============================================
	TABLA DE USUARIOS
	=============================================*/
	public function mostrarTabla()
	{
		$usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario -> id = usuarioAutenticado()["id"];

            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "usuarios", "ver") ) {

                $id = $usuario -> id;

                $usuarios = array();
                array_push( $usuarios, $usuario->consultar(null , $id) );

            } else {

                $usuarios = $usuario->consultar();

            }

        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

            echo json_encode($respuesta);
            return;

        }

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "usuario" ]);
        array_push($columnas, [ "data" => "nombre" ]);
        array_push($columnas, [ "data" => "apellidoPaterno" ]);
        array_push($columnas, [ "data" => "apellidoMaterno" ]);
        array_push($columnas, [ "data" => "correo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($usuarios as $key => $value) {
        	$rutaEdit = Route::names('usuarios.edit', $value['id']);
        	$rutaDestroy = Route::names('usuarios.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['usuario']));

        	if ( mb_strtolower($value["usuario"]) != mb_strtolower(CONST_ADMIN) ) {

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "usuario" => fString($value["usuario"]),
        							  "nombre" => fString($value["nombre"]),
        							  "apellidoPaterno" => fString($value["apellidoPaterno"]),
        							  "apellidoMaterno" => fString($value["apellidoMaterno"]),
        							  "correo" => fString($value["correo"]),
        							  "empresa" => fString($value["empresas.nombreCorto"]),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>" ] );

        	} else {

			array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "usuario" => fString($value["usuario"]),
        							  "nombre" => fString($value["nombre"]),
        							  "apellidoPaterno" => fString($value["apellidoPaterno"]),
        							  "apellidoMaterno" => fString($value["apellidoMaterno"]),
        							  "correo" => fString($value["correo"]),
        							  "empresa" => fString($value["empresas.nombreCorto"]),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>" ] );

        	}
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

	public function subirDocumentoUsuario()
	{
		$respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "servicios", "actualizar") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a modificar Servicios.";

            }
        
        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }
		
        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

		$usuario -> id = $this->usuarioId;

		if(!$usuario -> subirDocumentoUsuario($_FILES['documentos'])){

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Error al subir el(los) documento(s).";
		}else{
			$respuesta["error"] = false;
			$respuesta["mensaje"] = "Documento(s) subido(s) correctamente.";

		}

		echo json_encode($respuesta);
	}

	public function eliminarDocumentoUsuario()
	{
		$respuesta["error"] = false;

		// Validar Autorizacion
		$usuario = New Usuario;
		if ( usuarioAutenticado() ) {

			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "servicios", "actualizar") ) {

				$respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a modificar Servicios.";

			}
		
		} else {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

		}
		
		if ( $respuesta["error"] ) {

			echo json_encode($respuesta);
			return;

		}

		$usuario -> documentoId = $this->documentoId;

		if(!$usuario -> eliminarDocumentoUsuario()){

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Error al eliminar el documento.";
		}else{
			$respuesta["error"] = false;
			$respuesta["mensaje"] = "Documento eliminado correctamente.";

		}

		echo json_encode($respuesta);
	}
}

$activar = new UsuarioAjax();

if (isset($_POST["accion"])) {
	if($_POST["accion"] == "subirDocumentoUsuario"){
		$activar->usuarioId = $_POST["usuarioId"];
		$activar -> subirDocumentoUsuario();
	}elseif($_POST["accion"] == "eliminarDocumentoUsuario"){
		$activar->documentoId = $_POST["documentoId"];
		$activar -> eliminarDocumentoUsuario();
	}else{
		echo "Acción no definida";
	}
}else{
	/*=============================================
	TABLA DE USUARIOS
	=============================================*/
	$activar -> mostrarTabla();
}
