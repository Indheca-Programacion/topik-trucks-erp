<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Controllers/Autorizacion.php";
require_once "../Controllers/MailController.php";

require_once "../Models/ConfiguracionCorreoElectronico.php";
require_once "../Models/MensajeRequisicion.php";
require_once "../Models/Requisicion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Mensaje.php";

require_once "../Requests/SaveMensajeOrdenCompraRequest.php";

use App\Route;
use App\Controllers\Validacion;
use App\Controllers\Autorizacion;
use App\Controllers\MailController;

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\MensajeOrdenCompra;
use App\Models\Mensaje;
use App\Models\Requisicion;
use App\Models\Usuario;

use App\Requests\SaveMensajeOrdenCompraRequest;

class MensajeOrdenCompraAjax
{

     /*=============================================
    MOSTRAR CHAT

    FUNCIÓN PARA MOSTRAR LOS MENSAJE CREADOS EN FORMA
    DE LISTA.
    =============================================*/
    public function mostrarMensajes()
    {
        try {

            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $purchaseOrderMessage = new MensajeOrdenCompra;
            $purchaseOrderMessages = $purchaseOrderMessage->consultar(null,$this->ordenCompraId);
            
            $respuesta = array();
            $respuesta['codigo'] = 200;  
            $respuesta['error'] = false; 
            $respuesta['messageResponse'] = "Mensajes obtenidos correctamente"; 
            $respuesta['mensajes'] = $purchaseOrderMessages; 
    
        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Error al obtener los mensajes, llama a un administrador"
            ];
        }
        echo json_encode($respuesta);
    }
    
    /*=============================================
    CREAR MENSAJE

    FUNCION PARA LA CREACIÓN DE MENSJAES CON RELACIÓN
    A LAS REQUISICIONES Y ENVIO DE CORREO ELECTRONICO
    AUTOMATICO AL CREAR EL MENSAJE .
    =============================================*/
    public function crearMensaje()
    {    
        try{
           
			$request = SaveMensajeOrdenCompraRequest::validated();

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $mensaje = New MensajeOrdenCompra;
            $mensaje->crear($request);

            // MANDAR CORREO POR MANDAR MENSAJE
            $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

            if ( $configuracionCorreoElectronico->consultar(null , 1)) 
            {

                $ordenCompraId = $request["ordenCompraId"];
                $arrayRecipients = array();
                
                // Obtener el usuario autenticado
                $user = new Usuario;
                $userSendMessage = $user->consultar(null,usuarioAutenticado()["id"]);

                // OBTENER USUARIOS PAGOS
                // $usersShopping = $user->consultarCompradores();
                
                $userSendMessageArray = [
                    "usuarioId" => $userSendMessage["id"],
                    "correo" => $userSendMessage["correo"]
                ];

                array_push($arrayRecipients, $userSendMessageArray);    


                // ACTIVAR PARA USUARIOS COMPRAS
                // foreach ($usersShopping as $value) {

                //     if (in_array($value["id"], array_column($arrayRecipients, "id"))) continue;
                //     $resipientsShopping = [
                //         "usuarioId" => $value["id"],
                //         "correo" => $value["correo"]
                //     ];
                //     array_push($arrayRecipients, $resipientsShopping);
                // }
                
                $userSendMessageArray = [
                    "usuarioId" => 43,
                    "correo" => "josuepe03@hotmail.com"
                ];

                $liga = Route::names('orden-compra.edit',$ordenCompraId);
                $datosCorreo = [
                            'mensajeHTML' => "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>
                                                <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>
                        
                                                    <center>
                        
                                                        <h3 style='font-weight: 100; color: #999'>Nuevo mensaje Orden Compra {$ordenCompraId} </h3>
                                                        <hr style='border: 1px solid #ccc; width: 80%'>
                                                        
                                                        <br>
                                                        <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                                            <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Ha sido creado un nuevo mensaje</div>
                                                        </a>
                        
                                                        <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>
                        
                                                        <hr style='border: 1px solid #ccc; width: 80%'>
                        
                                                        <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado de la creación de un nuevo mensaje, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>
                        
                                                    </center>
                        
                                                </div>
                                                    
                                            </div>
                                            ",
                            'asunto' => "Nuevo mensaje de " ,
                            'mensaje' => "Se ha cerrado el gasto , entre a la aplicación para ver el detalle de la misma.",
                        ];

                // ENVIO DE CORREO
                $respuestaEnvioCorreo = enviarCorreo($userSendMessageArray,$datosCorreo);
            }

            $respuesta = [
                'error' => false,
                'mensaje' => "Mensaje enviado correctamente"
            ];
            
        }catch (\Exception $e) {
            $respuesta = [
                'error' => true,
                'errorMessage' => $e->getMessage(), // El mensaje del error
            ];
        }
        echo json_encode($respuesta);

    }
}

$mensajeOrdenCompraAyax = New MensajeOrdenCompraAjax();

if (isset($_POST["accion"])) {
    if ( $_POST["accion"] == "crearMensaje" ) {
        /*=============================================
        CREAR MENSAJES
        =============================================*/	
        $mensajeOrdenCompraAyax->crearMensaje();
    } else {
        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => 'Acción no encontrada'
        ];
    }
} else {
    /*=============================================
	TABLA DE INVENTARIOS
	=============================================*/
    $mensajeOrdenCompraAyax->ordenCompraId = $_GET["ordenCompraId"];
    $mensajeOrdenCompraAyax->mostrarMensajes();
}