<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Requisicion.php";
require_once "../Models/OrdenCompra.php";
require_once "../Models/MensajeRequisicion.php";
require_once "../Models/RequisicionArchivo.php";
require_once "../Models/Mensaje.php";
require_once "../Controllers/MailController.php";
require_once "../Models/ConfiguracionCorreoElectronico.php";

require_once "../Controllers/Autorizacion.php";
require_once "../../vendor/autoload.php";

use App\Route;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use iio\libmergepdf\Driver\TcpdiDriver;
use App\Models\Usuario;
use App\Models\Mensaje;

use App\Models\Requisicion;
use App\Models\MensajeRequisicion;
use App\Models\OrdenCompra;
use App\Models\RequisicionArchivo;
use App\Models\ConfiguracionCorreoElectronico;
use App\Controllers\MailController;

use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class RequisicionAjax
{
	/*=============================================
	TABLA DE REQUISICIONES
	=============================================*/
	public function mostrarTabla()
	{

        if ( !usuarioAutenticado() ) {
            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";
            echo json_encode($respuesta);
            die;
        } 

		$requisicion = New Requisicion;
		$ordenCompra = New OrdenCompra;

        $requisiciones = $requisicion->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fechaRequisicion" ]);
        array_push($columnas, [ "data" => "ubicacion",  "title" => "Ubicacion Maquinaria" ]);
        array_push($columnas, [ "data" => "solicito" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "ordenCompra" ]);
        array_push($columnas, [ "data" => "estatusOrdenCompra" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($requisiciones as $key => $value) {
        	$rutaEdit = Route::names('requisiciones.edit', $value['id']);
            $rutaPrint = Route::names('requisiciones.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));
            $solicito = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            if ( !is_null($value['usuarios.apellidoMaterno']) ) $solicito .= ' ' . $value['usuarios.apellidoMaterno'];

            // OBTENER ORDENES DE COMPRA DE LA REQUISICION
            $ordenCompra->requisicionId = $value["id"];
            $listaDeOrdenes = $ordenCompra->listaOrdenesDeCompraPorRequisicion();

            $folios = [];
            $descripciones = [];

            foreach ($listaDeOrdenes as $item) {
                $folios[] = $item['folio'];
                $descripciones[] = $item['descripcion'];
            }

            // Concatenar como desees
            $foliosConcatenados = empty($folios) ? 'SIN ORDEN DE COMPRA' : implode(', ', $folios);
            $descripcionesConcatenadas = empty($descripciones) ? 'SIN ORDEN DE COMPRA' : implode(', ', $descripciones);


        	array_push( $registros, [
                "consecutivo" => ($key + 1),
        		"folio" => fString($value["id"]),
        		"estatus" => fString($value["servicio_estatus.descripcion"]),
                "colorTexto" => mb_strtoupper(fString($value["servicio_estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["servicio_estatus.colorFondo"])),
        		"fechaRequisicion" => fFechaLarga($value["fechaCreacion"]),
        		"ubicacion" => fString($value["ubicaciones.descripcion"]),
                "solicito" => fString($solicito),
                "ordenCompra" => fString($foliosConcatenados),
                "estatusOrdenCompra" => fString($descripcionesConcatenadas),
        		"numeroEconomico" => fString($value["maquinarias.numeroEconomico"]),
        		"acciones" =>  "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}


    /*=============================================
    MOSTRAR CHAT
    =============================================*/
    public function mostrarChat()
    {
        try {
            $msjReq = new MensajeRequisicion;
            
            // Suponiendo que consultar devuelve un arreglo, no un objeto Eloquent
            $msjReq = $msjReq->consultar(null, $_GET["id_requisicion"]);
    
            // Si consultar devuelve un arreglo asociativo con claves duplicadas, debemos limpiarlo
            $mensajesLimpiados = array_map(function ($mensaje) {
                // Filtrar solo las claves necesarias, por ejemplo:
                return [
                    'id' => $mensaje['id'],
                    'mensaje' => $mensaje['mensaje'],
                    'id_requisicion' => $mensaje['id_requisicion'],
                    'usuario_id' => $mensaje['usuario_id'],
                    'fecha_enviado' => $mensaje['fecha_enviado'],
                    'nombreCompleto' => $mensaje['nombreCompleto'],
                    'session_id' => usuarioAutenticado()["id"],
                ];
            }, $msjReq);
    
            $respuesta = array();
            $respuesta['codigo'] = 200;  // Código de éxito
            $respuesta['error'] = false; // Indica que no hay error
            $respuesta['mensajes'] = $mensajesLimpiados;  // Los mensajes filtrados
    
        } catch (\Exception $e) {
            // En caso de error, devolver un código de error
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()  // El mensaje de error del servidor
            ];
        }
    
        // Enviar la respuesta como JSON
        echo json_encode($respuesta);
    }
    

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    public $empresaId;
    public $ubicacionId;
    public $maquinariaId;
    public $servicioEstatusId;
    public $fechaInicial;
    public $fechaFinal;
    public $concepto;
    public $ordenCompra;

    public function consultarFiltros()
    {
        $arrayFiltros = array();

        if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "S.empresaId", "operador" => "=", "valor" => $this->empresaId ]);
        if ( $this->ubicacionId > 0 ) array_push($arrayFiltros, [ "campo" => "S.ubicacionId", "operador" => "in", "valor" => "($this->ubicacionId)" ]);
        if ( $this->centroServicioId > 0 ) array_push($arrayFiltros, [ "campo" => "S.servicioCentroId", "operador" => "in", "valor" => "($this->centroServicioId)" ]);
        if ( $this->maquinariaId > 0 ) array_push($arrayFiltros, [ "campo" => "S.maquinariaId", "operador" => "=", "valor" => $this->maquinariaId ]);
        if ( $this->servicioEstatusId > 0 ) array_push($arrayFiltros, [ "campo" => "R.servicioEstatusId", "operador" => "=", "valor" => $this->servicioEstatusId ]);
        if ( $this->fechaInicial > 0 ) array_push($arrayFiltros, [ "campo" => "R.fechaCreacion", "operador" => ">=", "valor" => "'".fFechaSQL($this->fechaInicial)." 00:00:00'" ]);
        if ( $this->fechaFinal > 0 ) array_push($arrayFiltros, [ "campo" => "R.fechaCreacion", "operador" => "<=", "valor" => "'".fFechaSQL($this->fechaFinal)." 23:59:59'" ]);
        if ( $this->concepto !== '' ) array_push($arrayFiltros, [ "campo" => "lower(rd.concepto)", "operador" => "like", "valor" => "'%".$this->concepto."%'" ]);
        if ( $this->ordenCompra !== '' ) array_push($arrayFiltros, [ "campo" => "lower(RA.titulo)", "operador" => "like", "valor" => "'%".$this->ordenCompra."%'" ]);

        $requisicion = New Requisicion;
		$ordenCompra = New OrdenCompra;
        $requisiciones = $requisicion->consultarFiltros($arrayFiltros);

        // var_dump( $requisiciones);
        // die();
        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fechaRequisicion" ]);
        array_push($columnas, [ "data" => "obra",  "title" => "Obra" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "solicito" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "ordenCompra" ]);
        array_push($columnas, [ "data" => "estatusOrdenCompra" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        

        $registros = array();
        foreach ($requisiciones as $key => $value) {
            $rutaEdit = Route::names('requisiciones.edit', $value[0]);
            $rutaPrint = Route::names('requisiciones.print', $value[0]);
            $solicito = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            if ( !is_null($value['usuarios.apellidoMaterno']) ) $solicito .= ' ' . $value['usuarios.apellidoMaterno'];

            // OBTENER ORDENES DE COMPRA DE LA REQUISICION
            $ordenCompra->requisicionId = $value[0];
            $listaDeOrdenes = $ordenCompra->listaOrdenesDeCompraPorRequisicion();

            $folios = [];
            $descripciones = [];

            foreach ($listaDeOrdenes as $item) {
                $folios[] = $item['folio'];
                $descripciones[] = $item['descripcion'];
            }

            // Concatenar como desees
            $foliosConcatenados = empty($folios) ? 'SIN ORDEN DE COMPRA' : implode(', ', $folios);
            $descripcionesConcatenadas = empty($descripciones) ? 'SIN ORDEN DE COMPRA' : implode(', ', $descripciones);

            array_push( $registros, [
                "consecutivo" => ($key + 1),
                "empresa" => fString($value["empresas.nombreCorto"]),
                "folio" => fString($value["folio"]),
                "estatus" => fString($value["servicio_estatus.descripcion"]),
                "colorTexto" => mb_strtoupper(fString($value["servicio_estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["servicio_estatus.colorFondo"])),
                "fechaRequisicion" => fFechaLarga($value["fechaCreacion"]),
        		"obra" => fString($value["obras.descripcion"]),
                "ubicacion" => fString($value["ubicaciones.descripcion"]),
                "solicito" => fString($solicito),
                "ordenCompra" => fString($foliosConcatenados),
                "estatusOrdenCompra" => fString($descripcionesConcatenadas),
                "numeroEconomico" => fString($value["maquinarias.numeroEconomico"]),
                "acciones" =>  "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

    /*=============================================
    VER IMÁGENES
    =============================================*/
    public $token;
    public $detalleId;

    public function verImagenes()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisiciones", "ver") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a ver Requisiciones.";

            }
        
        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

        // Validar Token
        if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $requisicion = New Requisicion;

        $respuesta["imagenes"] = array();

        // Consultar las imágenes
        $respuesta["imagenes"] = $requisicion->consultarImagenes($this->detalleId);

        echo json_encode($respuesta);
    }

    /*=============================================
    ELIMINAR ARCHIVO
    =============================================*/
    public $archivoId;
    public $requisicionId;

    public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisiciones-subir", "eliminar") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a eliminar Archivos.";

            }
        
        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

        // Validar Token
        if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        // Validar existencia del campo requisicionId
        if ( !Validacion::validar("requisicionId", $this->requisicionId, ['exists', CONST_BD_APP.'.requisiciones', 'id']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "La requisición no existe.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $requisicionArchivo = New RequisicionArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $requisicionArchivo->id = $this->archivoId;
        $requisicionArchivo->requisicionId = $this->requisicionId;
        if ( !$requisicionArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $requisicionArchivo->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);

    }

    public function descargarTodo()
    {
        Autorizacion::authorize('view', New Requisicion);

        $requisicion = New Requisicion;

        $respuesta = array();

        if ( $requisicion->consultar(null , $_GET["requisicionId"]) ) {

            $requisicion->consultarComprobantes();
            $requisicion->consultarOrdenes();
            $requisicion->consultarFacturas();
            $requisicion->consultarCotizaciones();
            $requisicion->consultarVales();
            $requisicion->consultarDetalles();

            $this->crearRequisicion($requisicion);

            $archivos = [];

            foreach ($requisicion->comprobantesPago as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }
            
            foreach ($requisicion->ordenesCompra as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            $archivos[] = escapeshellarg(realpath('../../reportes/tmp/requisicion.pdf'));

            foreach ($requisicion->cotizaciones as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            

            foreach ($requisicion->facturas as $file) {
                if ($file["formato"] == 'application/pdf') {
                    $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                }
            }

            $nombreArchivo = "requisicion_" . $requisicion->folio . ".pdf";
            $rutaSalida = escapeshellarg(sys_get_temp_dir() . '/' . $nombreArchivo); // Usar un directorio temporal

            $comando = "pdfunite " . implode(" ", $archivos) . " " . $rutaSalida;

            $salida = shell_exec($comando);

            if (file_exists(str_replace("'", "", $rutaSalida))) { //Verificar que el archivo se creo.
                header("Content-type:application/pdf");
                header("Content-Disposition:attachment;filename=$nombreArchivo");
                readfile(str_replace("'", "", $rutaSalida)); //Leer el archivo y enviarlo.
                unlink(str_replace("'", "", $rutaSalida)); //Borrar el archivo temporal.
                exit();
            } else {
                echo "Error al fusionar los archivos PDF.";
            }

            $respuesta = array( 
                                'error' => false,
                                'rutas' => 'reportes/tmp/merged.pdf' ,
                            );

        } else {
            $respuesta = array( 'codigo' => 500,
                                'error' => true,
                                'errorMessage' => 'No se logró consultar la Requisición' );
        }

        echo json_encode($respuesta);
    }

    function crearRequisicion($requisicion)
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

    /*=============================================
    CREAR MENSAJE
    =============================================*/

    public $id_requisicion;

    public function crearMensaje()
    {    
        try{
            require_once "../Requests/SaveMensajeRequest.php";
            $request = \App\Requests\SaveMensajeRequest::validated();
            $request['usuario_id'] = usuarioAutenticado()["id"];

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

            require_once "../../app/Models/MensajeRequisicion.php";
            $mensaje = New \App\Models\MensajeRequisicion;
            $mensaje->crear($request);

            $usuario = New Usuario;
            $usuario -> id = usuarioAutenticado()["id"];
            Autorizacion::authorize('view', $usuario);


            // MANDAR CORREO POR MANDAR MENSAJE
            $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;

            if ( $configuracionCorreoElectronico->consultar(null , 1) ) 
            {
                $arrayDestinatarios = array();

                // Obtener el usuario autenticado
                $usuarioEnvio = $usuario->consultar(null,usuarioAutenticado()["id"]);
                $perfilCompras = $usuario->consultarPerfil(null,5);

                $usuarioEnvioArray = [
                    "usuarioId" => $usuarioEnvio["id"],
                    "correo" => $usuarioEnvio["correo"]
                ];
                array_push($arrayDestinatarios, $usuarioEnvioArray);
                                
                
                // ACTIVAR PARA USUARIOS COMPRAS
                
                // Agregar los usuarios del perfil de compras al arreglo
                // foreach ($perfilCompras as $value) {
                //     if (in_array($value["id"], array_column($arrayDestinatarios, "id"))) continue;
                //     $destinatario = [
                //         "usuarioId" => $value["id"],
                //         "correo" => $value["correo"]
                //     ];
                //     array_push($arrayDestinatarios, $destinatario);
                // }
                
                $mensajeCorreo = New Mensaje;

                $liga = Route::names('requisiciones.edit',$this->id_requisicion);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>
    
                            <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>
    
                                <center>
    
                                    <h3 style='font-weight: 100; color: #999'>Nuevo mensaje REQUISICION {$this->id_requisicion} </h3>
    
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
                                
                        </div>";
    
                $datos = [ "mensajeTipoId" => 3,
                            "mensajeEstatusId" => 1,
                            "asunto" => "Nuevo mensaje de " .$usuarioEnvio["nombre"] ." Requisicion ".$this->id_requisicion,
                            "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                            "mensaje" => "Se ha cerrado el gasto , entre a la aplicación para ver el detalle de la misma.",
                            "liga" => $liga,
                            "destinatarios" => $arrayDestinatarios                
                    ];
    
                    if ( $mensajeCorreo->crear($datos) ) {
                        $mensajeCorreo->consultar(null , $mensajeCorreo->id);
                        $mensajeCorreo->mensajeHTML = $mensajeHTML;
    
                        $enviar = MailController::send($mensajeCorreo);
                        if ( $enviar["error"] ) $mensajeCorreo->noEnviado([ "error" => $enviar["errorMessage"] ]);
                        else $mensajeCorreo->enviado();
                    }

            }


            $respuesta = [
                'error' => false,
                'id_requisicion' => $this->id_requisicion,
                'respuestaMessage' => "Mensaje enviado correctamente",
                "destinatarios" => $arrayDestinatarios,
                "usuarioEnvio" => $usuarioEnvio,
                "datos_Mensaje" => $datos
            ];
            
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

    public function verArchivos()
    {
        try {

            $requisicion = new Requisicion;

            $respuesta = array();

            if ( $requisicion->consultar(null , $_GET["requisicionId"]) ) {

                $requisicion->consultarComprobantes();
                $requisicion->consultarOrdenes();
                $requisicion->consultarFacturas();
                $requisicion->consultarCotizaciones();
                $requisicion->consultarVales();
                $requisicion->consultarDetalles();

                $rutaRequisicion = $this->crearRequisicion($requisicion);

                // AGREGAR CONSULTA DE ORDENES DE COMPRAS SOLO SON DATOS Y PARTIDAS
                $ordenesDeCompraDatos = $requisicion->consultarOrdenesDeCompra();

                $rutasOrdenDeCompra = $this->crearPDFOrdenesDeCompra($ordenesDeCompraDatos);

                $comprobantesPago = $requisicion->comprobantesPago;
                $ordenesCompra = $requisicion->ordenesCompra;
                $facturas = $requisicion->facturas;
                $cotizaciones = $requisicion->cotizaciones;
                $vales = $requisicion->valesAlmacen;

                $archivos = [];
                $temp_dir = '/tmp/processed_pdfs/';

                // Asegúrate de que el directorio temporal exista y sea escribible
                if (!is_dir($temp_dir)) {
                    mkdir($temp_dir, 0777, true);
                }

                foreach ($requisicion->comprobantesPago as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                $archivos = array_merge($archivos, $rutasOrdenDeCompra);

                foreach ($requisicion->ordenesCompra as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                $archivos[] = $rutaRequisicion;

                foreach ($requisicion->cotizaciones as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                foreach ($requisicion->facturas as $file) {
                    if ($file["formato"] == 'application/pdf') {
                        $archivos[] = escapeshellarg(realpath('../../' . $file["ruta"]));
                    }
                }

                $nombreArchivo = "requisicion_". $requisicion->folio . ".pdf";

                $rutaSalida = "/tmp/" . $nombreArchivo;

                // Verificar si ya existe el archivo y eliminarlo
                if (file_exists($rutaSalida)) {
                    unlink($rutaSalida);
                }

                $comando = "pdfunite " . implode(" ", $archivos) . " " . $rutaSalida;
                // // COMANDO DEV LINUX
                
                $salida = shell_exec($comando);
                // // Mover el archivo a la carpeta deseada después de crearlo
                $rutaDestino = __DIR__ . "/../../reportes/requisiciones/" . $nombreArchivo;
                if (file_exists(str_replace("'", "", $rutaSalida))) {
                    // Crear el directorio si no existe
                    if (!is_dir(dirname($rutaDestino))) {
                        mkdir(dirname($rutaDestino), 0777, true);
                    }
                    // Mover el archivo generado al destino
                    rename(str_replace("'", "", $rutaSalida), $rutaDestino);
                }

                if (file_exists(str_replace("'", "", $rutaDestino))) { //Verificar que el archivo se creo.

                    unlink(str_replace("'", "", $rutaRequisicion)); //Borrar el archivo temporal REQUISICION

                    
                } else {
                    echo "Error al fusionar los archivos PDF.";
                }


                $respuesta = array( 
                                    'error' => false,
                                    'ruta' => '/reportes/requisiciones/'.$nombreArchivo ,
                                );

                echo json_encode($respuesta);


            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

    }

    function crearPDFOrdenesDeCompra($datos)
    {
        include "../../reportes/pdfPrueba.php";
        
        // Llamar a la función
        $rutasArchivos = generarPDFOrdenes($datos);

        return $rutasArchivos;
    }

    function processPdfWithGhostscript($input_path, $output_dir) {
        $input_filename = basename($input_path);
        $output_filename = 'gs_processed_' . uniqid() . '_' . $input_filename; // Nombre único para evitar colisiones
        $output_path = $output_dir . $output_filename;

        // Asegurarse de que las rutas estén correctamente escapadas para el shell
        $escaped_input_path = escapeshellarg($input_path);
        $escaped_output_path = escapeshellarg($output_path);

        // Comando de Ghostscript para "aplanar" el PDF y eliminar restricciones
        $command = "gs -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile={$escaped_output_path} {$escaped_input_path} 2>&1";
        
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        if ($return_var === 0) {
            // Retorna la ruta del archivo procesado si fue exitoso
            return $output_path;
        } else {
            // Loguea el error o maneja la excepción
            error_log("Error al procesar PDF con Ghostscript: " . implode("\n", $output));
            // Si Ghostscript falla, intenta usar el archivo original, aunque puede fallar la unión
            return $input_path; 
        }
    }

}

$requisicionAjax = new RequisicionAjax;

// if ( isset($_POST["accion"]) && isset($_POST["detalleId"]) ) {
if ( isset($_POST["accion"]) ) {

    // if ( $_POST["accion"] == "verImagenes" ) {
    if ( $_POST["accion"] == "verImagenes" && isset($_POST["detalleId"]) ) {

        /*=============================================
        VER IMÁGENES
        =============================================*/
        $requisicionAjax->token = $_POST["_token"];
        $requisicionAjax->detalleId = $_POST["detalleId"];
        $requisicionAjax->verImagenes();

    } 
    elseif ( $_POST["accion"] == "crearMensaje" ) {

        /*=============================================
        CREAR MENSAJE
        =============================================*/
        $requisicionAjax->token = $_POST["_token"];
        $requisicionAjax->mensaje = $_POST["mensaje"];
        $requisicionAjax->id_requisicion = $_POST["id_requisicion"];
        $requisicionAjax->crearMensaje();

    }
    elseif ( $_POST["accion"] == "eliminarArchivo" && isset($_POST["archivoId"]) ) {

        /*=============================================
        ELIMINAR ARCHIVO
        =============================================*/
        $requisicionAjax->token = $_POST["_token"];
        $requisicionAjax->archivoId = $_POST["archivoId"];
        $requisicionAjax->requisicionId = $_POST["requisicionId"];
        $requisicionAjax->eliminarArchivo();

    } else {

        $respuesta["error"] = true;
        $respuesta["errorMessage"] = "Realizó una petición desconocida.";

        echo json_encode($respuesta);

    }

} else {

    if ( isset($_GET["maquinariaId"]) ) {

        /*=============================================
        CONSULTAR FILTROS
        =============================================*/ 
        $requisicionAjax->empresaId = $_GET["empresaId"];
        $requisicionAjax->ubicacionId = $_GET["ubicacionId"];
        $requisicionAjax->centroServicioId = $_GET["centroServicioId"];
        $requisicionAjax->maquinariaId = $_GET["maquinariaId"];
        $requisicionAjax->servicioEstatusId = $_GET["servicioEstatusId"];
        $requisicionAjax->fechaInicial = $_GET["fechaInicial"];
        $requisicionAjax->fechaFinal = $_GET["fechaFinal"];
        $requisicionAjax->concepto =$_GET["concepto"];
        $requisicionAjax->ordenCompra =$_GET["ordenCompra"];

        $requisicionAjax->consultarFiltros();

    }
    elseif ( isset($_GET["id_requisicion"]) ){
        /*=============================================
        MOSTRAR CHATS
        =============================================*/
        $requisicionAjax->mostrarChat();

    }

    elseif ( isset($_GET["accion"]) ){
        /*=============================================
        VER ARCHIVOS
        =============================================*/
        $requisicionAjax->requisicionId = $_GET["requisicionId"];
        $requisicionAjax->verArchivos();
    }

    else if ( isset($_GET["requisicionId"]) ){
        /*=============================================
        DESCARGA TODOS LOS ARCHIVOS EN CONJUNTO
        =============================================*/
        $requisicionAjax->descargarTodo();
    }else {

        /*=============================================
        TABLA DE REQUISICIONES
        =============================================*/
        $requisicionAjax->mostrarTabla();

    }

}
