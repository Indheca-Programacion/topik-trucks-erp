<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Cliente.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Cliente;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ClienteAjax
{
        /*=============================================
        TABLA DE CLIENTES
        =============================================*/
        public function mostrarTabla()
	{
                try{
                        $cliente = New Cliente;
                        $clientes = $cliente->consultar();
        
                        $columnas = array();
                        array_push($columnas, [ "data" => "consecutivo" ]);
                        array_push($columnas, [ "data" => "nombreCompleto" ]);
                        array_push($columnas, [ "data" => "correoElectronico" ]);
                        array_push($columnas, [ "data" => "telefono" ]);
                        array_push($columnas, [ "data" => "acciones" ]);
                        
                        $token = createToken();
                        
                        $registros = array();
                        foreach ($clientes as $key => $value) {
                                $rutaEdit = Route::names('clientes.edit', $value['id']);
                                $rutaDestroy = Route::names('clientes.destroy', $value['id']);
                                $folio = mb_strtoupper(fString($value['nombreCompleto']));
        
                                array_push( $registros, [ "consecutivo" => ($key + 1),
                                                          "nombreCompleto" => fString($value["nombreCompleto"]),
                                                          "correoElectronico" => fString($value["correo"]),
                                                          "telefono" => fString($value["telefono"]),
                                                          "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
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
                }catch (\Exception $e) {
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
TABLA DE CLIENTES
=============================================*/
$cliente = new ClienteAjax();
$cliente->mostrarTabla();