<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Servicio.php";
require_once "../Models/ServicioImagen.php";
require_once "../Models/ServicioArchivo.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Servicio;
use App\Models\ServicioImagen;
use App\Models\ServicioArchivo;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ServicioAjax
{
	/*=============================================
	TABLA DE SERVICIOS
	=============================================*/
	public function mostrarTabla()
	{
		$servicio = New Servicio;
        $servicios = $servicio->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "centroServicio" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fechaSolicitud" ]);
        array_push($columnas, [ "data" => "tipoMantenimiento" ]);
        array_push($columnas, [ "data" => "tipoServicio" ]);
        array_push($columnas, [ "data" => "tipoMaquinaria" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "marca" ]);
        array_push($columnas, [ "data" => "modelo" ]);
        array_push($columnas, [ "data" => "serie" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "fechaProgramacion" ]);
        array_push($columnas, [ "data" => "fechaFinalizacion" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($servicios as $key => $value) {
        	$rutaEdit = Route::names('servicios.edit', $value['id']);
        	// $rutaDestroy = Route::names('servicios.destroy', $value['id']);
            $rutaPrint = Route::names('servicios.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['folio']));
            $creo = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            if ( !is_null($value['usuarios.apellidoMaterno']) ) $creo .= ' ' . $value['usuarios.apellidoMaterno'];

        	array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
                "centroServicio" => mb_strtoupper(fString($value["servicio_centros.descripcion"])),
                "folio" => mb_strtoupper(fString($value["folio"])),
                "estatus" => mb_strtoupper(fString($value["servicio_estatus.descripcion"])),
                "colorTexto" => mb_strtoupper(fString($value["servicio_estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["servicio_estatus.colorFondo"])),
                "tipoMantenimiento" => mb_strtoupper(fString($value["mantenimiento_tipos.descripcion"])),
                "tipoServicio" => mb_strtoupper(fString($value["servicio_tipos.descripcion"])),
                "tipoMaquinaria" => mb_strtoupper(fString($value["maquinaria_tipos.descripcion"])),
                "numeroEconomico" => mb_strtoupper(fString($value["maquinarias.numeroEconomico"])),
                "marca" => mb_strtoupper(fString($value["marcas.descripcion"])),
                "modelo" => mb_strtoupper(fString($value["modelos.descripcion"])),
                "serie" => mb_strtoupper(fString($value["maquinarias.serie"])),
                "ubicacion" => mb_strtoupper(fString($value["ubicaciones.descripcion"])),
                "fechaSolicitud" => fFechaLarga($value["fechaSolicitud"]),
                "fechaProgramacion" => ( is_null($value["fechaProgramacion"]) ? '' : fFechaLarga($value["fechaProgramacion"]) ),
                "fechaFinalizacion" => ( is_null($value["fechaFinalizacion"]) ? '' : fFechaLarga($value["fechaFinalizacion"]) ),
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "creo" => mb_strtoupper(fString($creo)),
                "acciones" =>   "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                 <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        // "acciones" =>   "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
        //                         <form method='POST' action='{$rutaDestroy}' style='display: inline'>
        //                             <input type='hidden' name='_method' value='DELETE'>
        //                             <input type='hidden' name='_token' value='{$token}'>
        //                             <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
        //                                 <i class='far fa-times-circle'></i>
        //                             </button>
        //                         </form>"

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    public $empresaId;
    public $servicioCentroId;
    public $maquinariaId;
    public $servicioEstatusId;
    public $servicioTipoId;
    public $mantenimientoTipoId;
    public $fechaInicial;
    public $fechaFinal;

    public function consultarFiltros()
    {
        $arrayFiltros = array();

        if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "S.empresaId", "operador" => "=", "valor" => $this->empresaId ]);
        if ( $this->servicioCentroId > 0 ) array_push($arrayFiltros, [ "campo" => "S.servicioCentroId", "operador" => "=", "valor" => $this->servicioCentroId ]);
        if ( $this->maquinariaId > 0 ) array_push($arrayFiltros, [ "campo" => "S.maquinariaId", "operador" => "=", "valor" => $this->maquinariaId ]);
        if ( $this->mantenimientoTipoId > 0 ) array_push($arrayFiltros, [ "campo" => "S.mantenimientoTipoId", "operador" => "=", "valor" => $this->mantenimientoTipoId ]);
        if ( $this->servicioTipoId > 0 ) array_push($arrayFiltros, [ "campo" => "S.servicioTipoId", "operador" => "=", "valor" => $this->servicioTipoId ]);
        if ( $this->servicioEstatusId > 0 ) array_push($arrayFiltros, [ "campo" => "S.servicioEstatusId", "operador" => "=", "valor" => $this->servicioEstatusId ]);
        if ( $this->fechaInicial > 0 ) array_push($arrayFiltros, [ "campo" => "S.fechaSolicitud", "operador" => ">=", "valor" => "'".fFechaSQL($this->fechaInicial)." 00:00:00'" ]);
        if ( $this->fechaFinal > 0 ) array_push($arrayFiltros, [ "campo" => "S.fechaSolicitud", "operador" => "<=", "valor" => "'".fFechaSQL($this->fechaFinal)." 23:59:59'" ]);

        $servicio = New Servicio;
        $servicios = $servicio->consultarFiltros($arrayFiltros);

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "centroServicio" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fechaSolicitud" ]);
        array_push($columnas, [ "data" => "tipoMantenimiento" ]);
        array_push($columnas, [ "data" => "tipoServicio" ]);
        array_push($columnas, [ "data" => "tipoMaquinaria" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "marca" ]);
        array_push($columnas, [ "data" => "modelo" ]);
        array_push($columnas, [ "data" => "serie" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "fechaProgramacion" ]);
        array_push($columnas, [ "data" => "fechaFinalizacion" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($servicios as $key => $value) {
            $rutaEdit = Route::names('servicios.edit', $value['id']);
            $rutaPrint = Route::names('servicios.print', $value['id']);
            $folio = mb_strtoupper(fString($value['folio']));
            $creo = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            if ( !is_null($value['usuarios.apellidoMaterno']) ) $creo .= ' ' . $value['usuarios.apellidoMaterno'];

            array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
                "centroServicio" => mb_strtoupper(fString($value["servicio_centros.descripcion"])),
                "folio" => mb_strtoupper(fString($value["folio"])),
                "estatus" => mb_strtoupper(fString($value["servicio_estatus.descripcion"])),
                "colorTexto" => mb_strtoupper(fString($value["servicio_estatus.colorTexto"])),
                "colorFondo" => mb_strtoupper(fString($value["servicio_estatus.colorFondo"])),
                "tipoMantenimiento" => mb_strtoupper(fString($value["mantenimiento_tipos.descripcion"])),
                "tipoServicio" => mb_strtoupper(fString($value["servicio_tipos.descripcion"])),
                "tipoMaquinaria" => mb_strtoupper(fString($value["maquinaria_tipos.descripcion"])),
                "numeroEconomico" => mb_strtoupper(fString($value["maquinarias.numeroEconomico"])),
                "marca" => mb_strtoupper(fString($value["marcas.descripcion"])),
                "modelo" => mb_strtoupper(fString($value["modelos.descripcion"])),
                "serie" => mb_strtoupper(fString($value["maquinarias.serie"])),
                "ubicacion" => mb_strtoupper(fString($value["ubicaciones.descripcion"])),
                "fechaSolicitud" => fFechaLarga($value["fechaSolicitud"]),
                "fechaProgramacion" => ( is_null($value["fechaProgramacion"]) ? '' : fFechaLarga($value["fechaProgramacion"]) ),
                "fechaFinalizacion" => ( is_null($value["fechaFinalizacion"]) ? '' : fFechaLarga($value["fechaFinalizacion"]) ),
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "creo" => mb_strtoupper(fString($creo)),
                "acciones" =>   "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
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
    GENERAR REPORTE PDF
    =============================================*/
    public function generarPDF()
    {
        try {

            $arrayFiltros = array();

            if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "S.empresaId", "operador" => "=", "valor" => $this->empresaId ]);
            if ( $this->servicioCentroId > 0 ) array_push($arrayFiltros, [ "campo" => "S.servicioCentroId", "operador" => "=", "valor" => $this->servicioCentroId ]);
            if ( $this->maquinariaId > 0 ) array_push($arrayFiltros, [ "campo" => "S.maquinariaId", "operador" => "=", "valor" => $this->maquinariaId ]);
            if ( $this->servicioEstatusId > 0 ) array_push($arrayFiltros, [ "campo" => "S.servicioEstatusId", "operador" => "=", "valor" => $this->servicioEstatusId ]);
            if ( $this->fechaInicial > 0 ) array_push($arrayFiltros, [ "campo" => "S.fechaSolicitud", "operador" => ">=", "valor" => "'".fFechaSQL($this->fechaInicial)." 00:00:00'" ]);
            if ( $this->fechaFinal > 0 ) array_push($arrayFiltros, [ "campo" => "S.fechaSolicitud", "operador" => "<=", "valor" => "'".fFechaSQL($this->fechaFinal)." 23:59:59'" ]);

            $servicio = New Servicio;
            $servicios = $servicio->consultarFiltros($arrayFiltros);

            require_once "../Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            if ( $this->empresaId > 0 ) $empresa->consultar(null, $this->empresaId);

            require_once "../Models/ServicioCentro.php";
            $servicioCentro = New \App\Models\ServicioCentro;
            if ( $this->servicioCentroId > 0 ) $servicioCentro->consultar(null, $this->servicioCentroId);

            require_once "../Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            if ( $this->maquinariaId > 0 ) $maquinarias = $maquinaria->consultar(null, $this->maquinariaId);

            require_once "../Models/ServicioEstatus.php";
            $servicioEstatus = New \App\Models\ServicioEstatus;
            if ( $this->servicioEstatusId > 0 ) $servicioStatus = $servicioEstatus->consultar(null, $this->servicioEstatusId);

            $fechaSolicitudInicial = ( $this->fechaInicial > 0 ) ? $this->fechaInicial : null;
            $fechaSolicitudFinal = ( $this->fechaFinal > 0 ) ? $this->fechaFinal : null;

            // $fecha = (new \DateTime('America/Mexico_City')) -> format('Y-m-d h:i:s A'); // Fecha del día
            // $fecha = strtotime($fecha);
            // $dia = date("d", $fecha);
            // $mes = fNombreMes(date("n", $fecha));
            // $year = date("Y", $fecha);
            // $hora = date("H", $fecha);
            // $minuto = date("i", $fecha);
            // $segundo = date("s", $fecha);
            // $am_pm = date("A", $fecha);

            // $archivo = "tmp/Listado de Servicios {$dia}-{$mes}-{$year} {$hora}-{$minuto}-{$segundo}.pdf";

            $archivo = 'tmp/Listado de Servicios.pdf';
            $ruta = $_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$archivo;

            include "../../reportes/listado-servicios.php";

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['archivo'] = $archivo;

        } catch (Exception $e) {
            $respuesta = array( 'codigo' => 500,
                                'error' => true,
                                // 'errorMessage' => 'Error al generar el reporte' );
                                'errorMessage' => $e->getMessage() );
        }

        echo json_encode($respuesta);
        return;
    }

    /*=============================================
    VER IMÁGENES
    =============================================*/
    public $token;
    public $servicioId;

    public function verImagenes()
    {

        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "servicios", "ver") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a ver Servicios.";

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

        $servicio = New Servicio;

        $respuesta["imagenes"] = array();

        // Consultar las imágenes
        $respuesta["imagenes"] = $servicio->consultarImagenes($this->servicioId);

        echo json_encode($respuesta);

    }

    /*=============================================
    ELIMINAR IMAGEN
    =============================================*/
    public $imagenId;

    public function eliminarImagen()
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

        // Validar existencia del campo servicioId
        if ( !Validacion::validar("servicioId", $this->servicioId, ['exists', CONST_BD_APP.'.servicios', 'id']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El servicio no existe.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $servicioImagen = New ServicioImagen;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $servicioImagen->id = $this->imagenId;
        $servicioImagen->servicioId = $this->servicioId;
        if ( !$servicioImagen->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "La imágen no existe.";

        } else {

            // Eliminar el archivo
            if ( $servicioImagen->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);
    }

    /*=============================================
    SUBIR ARCHIVOS
    =============================================*/
    public $folio;
    public $archivos;

    public function subirArchivos()
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

        // Validar existencia del campo servicioId
        if ( !Validacion::validar("servicioId", $this->servicioId, ['exists', CONST_BD_APP.'.servicios', 'id']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El servicio no existe.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $servicio = New Servicio;

        // $datos["archivos"] = $this->archivos;

        $respuesta["respuesta"] = false;

        // Validar campo folio (que coincida con el servicioId)
        $servicio->id = $this->servicioId;
        if ( $servicio->consultar(null , $this->servicioId) ) {

            if ( $servicio->folio == $this->folio ) {

                // Insertar los archivos en el servicio
                if ( $servicio->insertarArchivos($this->archivos) ) {

                    $respuesta["respuestaMessage"] = "Los documentos fueron grabados correctamente.";

                    $respuesta["archivos"] = $servicio->consultarArchivos($this->servicioId);

                } else {

                    $respuesta["error"] = true;
                    $respuesta["errorMessage"] = "Hubo un error al intentar grabar los documentos, intente de nuevo.";

                }

            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "El folio no coincide con el servicio.";

            }

        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El servicio no existe.";

        }

        echo json_encode($respuesta);
    }

    /*=============================================
    VER ARCHIVOS
    =============================================*/
    public function verArchivos()
    {

        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "servicios", "ver") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a ver Servicios.";

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

        $servicio = New Servicio;

        $respuesta["archivos"] = array();

        // Consultar los archivos
        $respuesta["archivos"] = $servicio->consultarArchivos($this->servicioId);

        echo json_encode($respuesta);

    }

    /*=============================================
    ELIMINAR ARCHIVO
    =============================================*/
    public $archivoId;

    public function eliminarArchivo()
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

        // Validar existencia del campo servicioId
        if ( !Validacion::validar("servicioId", $this->servicioId, ['exists', CONST_BD_APP.'.servicios', 'id']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El servicio no existe.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $servicioArchivo = New ServicioArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $servicioArchivo->id = $this->archivoId;
        $servicioArchivo->servicioId = $this->servicioId;
        if ( !$servicioArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $servicioArchivo->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);
    }
}

$servicioAjax = new ServicioAjax;

if ( isset($_GET["accion"]) && $_GET["accion"] == 'reporte' ) {

    /*=============================================
    GENERAR REPORTE PDF
    =============================================*/ 
    $servicioAjax->empresaId = $_GET["empresaId"];
    $servicioAjax->servicioCentroId = $_GET["servicioCentroId"];
    $servicioAjax->maquinariaId = $_GET["maquinariaId"];
    $servicioAjax->servicioEstatusId = $_GET["servicioEstatusId"];
    $servicioAjax->fechaInicial = $_GET["fechaInicial"];
    $servicioAjax->fechaFinal = $_GET["fechaFinal"];
    $servicioAjax->generarPDF();

} elseif ( isset($_POST["accion"]) && $_POST["accion"] == 'verImagenes' ) {

    /*=============================================
    VER IMÁGENES
    =============================================*/
    $servicioAjax->token = $_POST["_token"];
    $servicioAjax->servicioId = $_POST["servicioId"];
    $servicioAjax->verImagenes();

} elseif ( isset($_POST["accion"]) && $_POST["accion"] == 'eliminarImagen' && isset($_POST["imagenId"]) ) {

    /*=============================================
    ELIMINAR IMAGEN
    =============================================*/
    $servicioAjax->token = $_POST["_token"];
    $servicioAjax->imagenId = $_POST["imagenId"];
    $servicioAjax->servicioId = $_POST["servicioId"];
    $servicioAjax->eliminarImagen();

} elseif ( isset($_POST["accion"]) && $_POST["accion"] == 'subirArchivos' ) {

    /*=============================================
    SUBIR ARCHIVOS
    =============================================*/
    $servicioAjax->token = $_POST["_token"];
    $servicioAjax->servicioId = $_POST["servicioId"];
    $servicioAjax->folio = $_POST["folio"];
    $servicioAjax->archivos = $_FILES["archivos"];
    $servicioAjax->subirArchivos();

} elseif ( isset($_POST["accion"]) && $_POST["accion"] == 'verArchivos' ) {

    /*=============================================
    VER ARCHIVOS
    =============================================*/
    $servicioAjax->token = $_POST["_token"];
    $servicioAjax->servicioId = $_POST["servicioId"];
    $servicioAjax->verArchivos();

} elseif ( isset($_POST["accion"]) && $_POST["accion"] == 'eliminarArchivo' && isset($_POST["archivoId"]) ) {

    /*=============================================
    ELIMINAR ARCHIVO
    =============================================*/
    $servicioAjax->token = $_POST["_token"];
    $servicioAjax->archivoId = $_POST["archivoId"];
    $servicioAjax->servicioId = $_POST["servicioId"];
    $servicioAjax->eliminarArchivo();

} elseif ( isset($_GET["maquinariaId"]) ) {

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    $servicioAjax->empresaId = $_GET["empresaId"];
    $servicioAjax->servicioCentroId = $_GET["servicioCentroId"];
    $servicioAjax->maquinariaId = $_GET["maquinariaId"];
    $servicioAjax->servicioEstatusId = $_GET["servicioEstatusId"];
    $servicioAjax->servicioTipoId = $_GET["servicioTipoId"];
    $servicioAjax->mantenimientoTipoId = $_GET["mantenimientoTipoId"];
    $servicioAjax->fechaInicial = $_GET["fechaInicial"];
    $servicioAjax->fechaFinal = $_GET["fechaFinal"];
    $servicioAjax->consultarFiltros();

} else {

    /*=============================================
    TABLA DE SERVICIOS
    =============================================*/
    $servicioAjax->mostrarTabla();

}
