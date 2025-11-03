<?php

namespace App\Controllers;

require_once "app/Models/Maquinaria.php";
require_once "app/Policies/MaquinariaPolicy.php";
require_once "app/Requests/SaveMaquinariasRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "vendor/autoload.php";

use App\Models\Maquinaria;
use App\Policies\MaquinariaPolicy;
use App\Requests\SaveMaquinariasRequest;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
// use App\Requests\Request;
use App\Route;

class MaquinariasController
{
    public function index()
    {
        Autorizacion::authorize('view', new Maquinaria);

        // $maquinaria = New Maquinaria;
        // $maquinarias = $maquinaria->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/MaquinariaTipo.php";
        $maquinariaTipo = New \App\Models\MaquinariaTipo;
        $maquinariaTipos = $maquinariaTipo->consultar();

        require_once "app/Models/Ubicacion.php";
        $ubicacion = New \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        $contenido = array('modulo' => 'vistas/modulos/maquinarias/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $maquinaria = new Maquinaria;
        Autorizacion::authorize('create', $maquinaria);

        // $empresas = \App\Conexion::queryAll(CONST_BD_SECURITY, "SELECT * FROM empresas ORDER BY razonSocial", $error);
        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        // $maquinariaTipos = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xmaquinaria_tipos ORDER BY descripcion", $error);
        require_once "app/Models/MaquinariaTipo.php";
        $maquinariaTipo = New \App\Models\MaquinariaTipo;
        $maquinariaTipos = $maquinariaTipo->consultar();

        // $marcas = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xmarcas ORDER BY descripcion", $error);
        require_once "app/Models/Marca.php";
        $marca = New \App\Models\Marca;
        $marcas = $marca->consultar();

        // $modelos = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xmodelos ORDER BY descripcion", $error);
        require_once "app/Models/Modelo.php";
        $modelo = New \App\Models\Modelo;
        $modelos = $modelo->consultar();

        // $colores = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xcolores ORDER BY descripcion", $error);
        require_once "app/Models/Color.php";
        $color = New \App\Models\Color;
        $colores = $color->consultar();

        // $estatus = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xestatus ORDER BY descripcion", $error);
        require_once "app/Models/Estatus.php";
        $status = New \App\Models\Estatus;
        $estatus = $status->consultar();

        // $ubicaciones = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xubicaciones ORDER BY descripcion", $error);
        require_once "app/Models/Ubicacion.php";
        $ubicacion = New \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        // $almacenes = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xalmacenes ORDER BY descripcion", $error);
        require_once "app/Models/Almacen.php";
        $almacen = New \App\Models\Almacen;
        $almacenes = $almacen->consultar();

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        // include "vistas/modulos/maquinarias/crear.php";
        $contenido = array('modulo' => 'vistas/modulos/maquinarias/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {    
        Autorizacion::authorize('create', New Maquinaria);

        $request = SaveMaquinariasRequest::validated();

        $maquinaria = New Maquinaria;
        $respuesta = $maquinaria->crear($request);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La maquinaria fue creada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Maquinaria',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La maquinaria fue creada correctamente' );
            header("Location:" . Route::names('maquinarias.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('maquinarias.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Maquinaria);

        $maquinaria = New Maquinaria;

        if ( $maquinaria->consultar(null , $id) ) {

            $imagenes = $maquinaria->consultarFotos();

            $arrayEvidencias = array();
            foreach ($imagenes as $key => $value) {
                $value["ruta"] = Route::rutaServidor().$value["ruta"];
                switch ($value["detalle"]) {
                    case 1:
                        $arrayEvidencias['FUGAS'][] = $value;
                        break;
                    case 2:
                        $arrayEvidencias['TRANSMISION'][] = $value;
                        break;
                    case 3:
                        $arrayEvidencias['SISTEMAS'][] = $value;
                        break;
                    case 4:
                        $arrayEvidencias['MOTOR'][] = $value;
                        break;
                    case 5:
                        $arrayEvidencias['PINTURA'][] = $value;
                        break;
                    case 6:
                        $arrayEvidencias['SEGURIDAD'][] = $value;
                        break;
                    case 7:
                        $arrayEvidencias['GENERAL'][] = $value;
                        break;
                }
            }

            $text =  Route::names('escaner.edit', $id);

            $qr_code = QrCode::create($text)
            ->setSize(600)
            ->setMargin(40)
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh);
            
            $writer = new PngWriter;
            
            $result = $writer->write($qr_code);
            $result->saveToFile("vistas/img/qr/qr-code.png");

            $result->getString();

            $maquinaria->consultarHorometros();
            $maquinaria->consultarConsumibles();
            $maquinaria->consultarServicios();
            $maquinaria->consultarChecklist();
            $maquinaria->consultarKits();

            require_once "app/Models/KitMantenimiento.php";
            $kitMantenimiento = New \App\Models\KitMantenimiento;
            $kitMantenimiento->id = $maquinaria->id;
            $kitMantenimiento->tipoMaquinaria = $maquinaria->maquinariaTipoId;
            $kitMantenimiento->modelo = $maquinaria->modeloId;
            $kits = $kitMantenimiento->consultarKitsParaMaquinaria();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/MaquinariaTipo.php";
            $maquinariaTipo = New \App\Models\MaquinariaTipo;
            $maquinariaTipos = $maquinariaTipo->consultar();

            require_once "app/Models/Marca.php";
            $marca = New \App\Models\Marca;
            $marcas = $marca->consultar();

            require_once "app/Models/Modelo.php";
            $modelo = New \App\Models\Modelo;
            $modelos = $modelo->consultar();

            require_once "app/Models/Color.php";
            $color = New \App\Models\Color;
            $colores = $color->consultar();

            require_once "app/Models/Estatus.php";
            $status = New \App\Models\Estatus;
            $estatus = $status->consultar();

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            require_once "app/Models/Almacen.php";
            $almacen = New \App\Models\Almacen;
            $almacenes = $almacen->consultar();

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/MantenimientoTipo.php";
            $mantenimientoTipo = New \App\Models\MantenimientoTipo;
            $tiposMantenimiento = $mantenimientoTipo->consultar();

            require_once "app/Models/SolicitudTipo.php";
            $solicitudTipo = New \App\Models\SolicitudTipo;
            $tiposSolicitud = $solicitudTipo->consultar();

            require_once "app/Models/ServicioCentro.php";
            $servicioCentro = New \App\Models\ServicioCentro;
            $servicioCentros = $servicioCentro->consultar();

            require_once "app/Models/ServicioTipo.php";
            $servicioTipo = New \App\Models\ServicioTipo;
            $servicioTipos = $servicioTipo->consultar();

            $maquinaria->consultarKits();
            $kitsMantenimiento = $maquinaria->kits;

            // include "vistas/modulos/maquinarias/editar.php";
            $contenido = array('modulo' => 'vistas/modulos/maquinarias/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Maquinaria);

        $request = SaveMaquinariasRequest::validated($id);

        $maquinaria = New Maquinaria;
        $maquinaria->id = $id;
        $respuesta = $maquinaria->actualizar($request);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La maquinaria fue actualizada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Maquinaria',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La maquinaria fue actualizada correctamente' );
            header("Location:" . Route::names('maquinarias.index'));

        } else {            

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('maquinarias.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        // var_dump(empresaId);
        // die();
        Autorizacion::authorize('delete', new Maquinaria);

        // Sirve para validar el Token
        if ( !SaveMaquinariasRequest::validatingToken($error) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = $error;
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );

            header("Location:" . Route::names('maquinarias.index'));
            die();

        }

        $maquinaria = New Maquinaria;
        $maquinaria->id = $id;
        // $maquinaria->empresaId = empresaId();
        $respuesta = $maquinaria->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La maquinaria fue eliminada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Maquinaria',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La maquinaria fue eliminada correctamente' );
            header("Location:" . Route::names('maquinarias.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta maquinaria no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta maquinaria no se podrá eliminar ***' );
            header("Location:" . Route::names('maquinarias.index'));

        }
        
        die();

    }
}
