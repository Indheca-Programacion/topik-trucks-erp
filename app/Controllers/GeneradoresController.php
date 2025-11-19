<?php

namespace App\Controllers;

require_once "app/Models/Generadores.php";
require_once "app/Policies/GeneradoresPolicy.php";
require_once "app/Requests/SaveGeneradoresRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Empresa.php";

use App\Models\Generadores;
use \App\Models\Empresa;
use App\Policies\GeneradoresPolicy;
use App\Requests\SaveGeneradoresRequest;
use App\Route;

class GeneradoresController
{
    public function index(){

        Autorizacion::authorize('view', new Generadores);

        $generador =  new Generadores;

        $contenido = array('modulo' => 'vistas/modulos/generadores/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create(){

        $generador = new Generadores;
        Autorizacion::authorize('create', $generador);

        $generadores = $generador->consultar();
        $contenido = array('modulo' => 'vistas/modulos/generadores/crear.php');

        $empresa = new Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Ubicacion.php";
        $ubicacion = new \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        require_once "app/Models/Obra.php";
        $obra = new \App\Models\Obra;
        $obras = $obra->consultar();

        include "vistas/modulos/plantilla.php";

    }

    public function store(){
        
        
        Autorizacion::authorize('create', new Generadores);
        $generadores = new Generadores;

        $request = SaveGeneradoresRequest::validated();

        // CREAR GENERADOR
        $id_generador = $generadores->crear($request);

        // OBTENER MI COORDINADOR 
        $ubicacionCoordinador = obtenerCoordinador();

        define("CONST_COORDINADOR_POR_DEFECTO",80);

        if( obtenerCoordinador() == null){
            $ubicacionCoordinador = CONST_COORDINADOR_POR_DEFECTO;
        }

        // CREAR Y ASIGNAR TAREA
        $respuesta = crearTareaAsignar($id_generador,$ubicacionCoordinador,"asignarGenerador");

        // Consultar los detalles de generador
        require_once "app/Models/GeneradorDetalles.php";
        $generadorDetalles = New \App\Models\GeneradorDetalles;
        $generadorDetalles->ubicacionId = $request["ubicacionId"];
        $generadorDetalles->obraId = $request["obraId"];

        // Se evalua si se escogio un generador a copiar
        if (isset($_POST["generadorId"]) && !empty($_POST["generadorId"])) {
            $detalles = $generadorDetalles->consultarDetalles($_POST["generadorId"]);
            
            $arrayDetalles = array();
            foreach ($detalles as $key => $value) {
                array_push( $arrayDetalles, [
                    "fk_maquinaria"=> $value["id"],
                    "fk_generador" => $generadores->id,
                    "fechaInicio" => $value["fecha"],
                    ]
                );
            }

            require_once "app/Models/Desempeno.php";
            $desempeno = new \App\Models\Desempeno;
            foreach ($arrayDetalles as $key => $value) {
                $respuesta = $generadorDetalles->crear($value);
                $datosDesempeno = array(
                    "generador_detalle" => $generadorDetalles->id,
                    "hmr" => 0,
                    "rr" => 0,
                    "lcc" => 0,
                    "observaciones" => ""
                );
                $resultadoDesempeno = $desempeno->crear($datosDesempeno);
            }

        }
        

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Generador',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El generador fue creado correctamente' );
            header("Location:" . Route::names('generadores.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Generadores',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('generadores.create'));
        }
        die();

    }

    public function edit($id){

        Autorizacion::authorize('update', new Generadores);

        $generador = new Generadores;
        $generador->consultar(null,$id);

        require_once "app/Models/Usuario.php";
        $usuario = new \App\Models\Usuario;
        $usuario->consultar(null, usuarioAutenticado()["id"]);
        $usuario->consultarPerfiles();
        $usuario->consultarPermisos();

        // OBTENER TAREA PARA TERMINARLA

        $autorizarEstimacion = false;
        if ($usuario->checkPermiso('estimaciones-aut') || $usuario->checkAdmin()) {
            $autorizarEstimacion = true;
        }

        $editarEstimaciones = false;
        if ($usuario->checkPermiso('estimaciones-edit') || $usuario->checkAdmin()) {
            $editarEstimaciones = true;
        }

        $autorizarEstimacionesSuperviso = false;
        if ($usuario->checkPermiso('estimacion-rev-auth') || $usuario->checkAdmin()) {
            $autorizarEstimacionesSuperviso = true;
        }

        $editarDetallesGenerador = false;
        if ($usuario->checkPermiso('generador-detalles') || $usuario->checkAdmin()) {
            $editarDetallesGenerador = true;
        }

        $autorizarGenerador= false;
        if ($usuario->checkPermiso('generadores-aut') || $usuario->checkAdmin()) {
            $autorizarGenerador= true;
        }

        require_once "app/Models/Ubicacion.php";
        $ubicacion = new \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        require_once "app/Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultar();

        require_once "app/Models/Obra.php";
        $obra = new \App\Models\Obra;
        $obras = $obra->consultar();

        $empresa = new Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/GeneradorDetalles.php";
        $generadorDetalle = New \App\Models\GeneradorDetalles;
        $generadorDetalles = $generadorDetalle->consultarDetalles($id);

        $estimaciones = $generadorDetalle->consultarEstimaciones($id);

        $arrayEstimaciones = array();

        foreach ($estimaciones as $key => $value) {
            $laborados = json_decode($value["laborados"]);
            $fallas = json_decode($value["fallas"]);
            $paros = json_decode($value["paros"]);
            $clima = json_decode($value["clima"]);
            $totalDias = count($laborados) +  count($paros) + count($clima);
            //
            $fechaIngresada = $generador->mes;
            $partesFecha = explode('-', $fechaIngresada);
            $año = $partesFecha[0];
            $mes = $partesFecha[1];
            $totalDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $año);
            
            $division = $totalDiasMes != 0 ? count($laborados) / $totalDiasMes : 0;
            $pu = (floatval($value["costo"])/30) * $totalDias;

            $importe = number_format($pu+$value["operacion"]+$value["comb"]+$value["mantto"]+$value["flete"]+$value["ajuste"],2);

            array_push(  $arrayEstimaciones,[
                    "id" => $value["id"],
                    "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                    "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                    "totalDias" => $totalDias,
                    "costo" => $value["costo"],
                    "pu" => number_format($pu,2),
                    "operacion" => $value["operacion"],
                    "comb" => $value["comb"],
                    "mantto" => $value["mantto"],
                    "flete" => $value["flete"],
                    "ajuste" => $value["ajuste"],
                    "importe" => $importe
                ]
            );
        }

        $arrayGeneradores = array();

        foreach ($generadorDetalles as $key => $value) {
            array_push( $arrayGeneradores, [ 
                "consecutivo" => ($key + 1),
                "maquinariaId" => $value["id"],
                "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                "equipo" => mb_strtoupper(fString($value["equipo"])),
                "marca" => mb_strtoupper(fString($value["marca"])),
                "modelo" => mb_strtoupper(fString($value["modelo"])),
                "serie" => mb_strtoupper(fString($value["serie"])),
                "fecha" => fFechaLarga($value["fecha"]),
                "equipo" => mb_strtoupper(fString($value["equipo"])),
                "laborados" => json_decode($value["laborados"]), 
                "fallas" => json_decode($value["fallas"]), 
                "paros" => json_decode($value["paros"]),
                "clima" => json_decode($value["clima"]), 
                ] );
        }

        $desempeno = $generadorDetalle->consultarDesempeno($id);

        $arrayDesempeño = array();

        foreach ($desempeno as $key => $value) {
            $laborados = json_decode($value["laborados"]);
            $fallas = json_decode($value["fallas"]);
            $paros = json_decode($value["paros"]);
            $clima = json_decode($value["clima"]);
            $totalDias = count($laborados) + count($fallas) + count($paros) + count($clima);

            $week=0;
            $weekend=0;

            foreach ($laborados as $dia) {
                $date = $dia.'-'.substr($generador->mes,2);
                $timestamp = strtotime($date);
                $dayOfWeek = date('N', $timestamp);

                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                    $week +=1;
                }else{
                    $weekend +=1;
                }
            }

            $hod = (($week*8)+($weekend*4));

            $rendimiento = $hod != 0 ? ($value["lcc"]/$hod) * 100 : 0;
            $aprovechamiento = $hod != 0 ? (($value["hmr"] - $value["rr"]) /$hod)*100 : 0;
            array_push( $arrayDesempeño, [ 
                "id" => $value["id"],
                "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                "totalDias" => count($laborados),
                "hod" => $hod,
                "hmr" => $value["hmr"],
                "rr" => $value["rr"],
                "lcc" => $value["lcc"],
                "rendimiento" => number_format($rendimiento,2),
                "aprovechamiento" => number_format($aprovechamiento,2),
                "observaciones" => mb_strtoupper(fString($value["observaciones"])),
            ] );
        }

        if ( $generador->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/generadores/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Generadores);

        // Sirve para validar el Token
        if ( !SaveGeneradoresRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Generador',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('generadores.index'));
            die();

        }

        $generador = New Generadores;
        $generador->id = $id;
        $respuesta = $generador->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Generadores',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El generador fue eliminado correctamente' );

            header("Location:" . Route::names('generadores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Generador',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('generadores.index'));

        }
        
        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Generadores);

        $generador = New Generadores;

        
        if ( $generador->consultar(null , $id) ) {
            
            require_once "app/Models/GeneradorDetalles.php";
            $generadorDetalles = New \App\Models\GeneradorDetalles;

            require_once "app/Models/GeneradorObservaciones.php";
            $generadorObservaciones = New \App\Models\GeneradorObservaciones;
            
            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null,$generador->usuarioIdCreacion);

            $usuarioNombre = $usuario->nombre;
            $elaboro = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $elaboro .= ' ' . $usuario->apellidoMaterno;
            $elaboroFirma = $usuario->firma;
            unset($usuario);

            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null,$generador->firmado);

            $usuarioNombre = $usuario->nombre;
            $superintendente = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $superintendente .= ' ' . $usuario->apellidoMaterno;
            $autorizoFirma = $usuario->firma;
            unset($usuario);

            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null,$generador->generadorSupervisorFirma);

            $usuarioNombre = $usuario->nombre;
            $superviso = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $superviso .= ' ' . $usuario->apellidoMaterno;
            $supervisoFirma = $usuario->firma;
            $superviso = mb_strtoupper($superviso);
            unset($usuario);

            $maquinarias = $generadorDetalles->consultarDetalles($id);

            $maquinariasPorEmpresa = [];
            foreach ($maquinarias as $maquinaria) {
                $empresaId = $maquinaria['empresaId'];
                if (!isset($maquinariasPorEmpresa[$empresaId])) {
                    $maquinariasPorEmpresa[$empresaId]["maquinarias"] = [];
                    $maquinariasPorEmpresa[$empresaId]["observaciones"] = [];
                }
                $maquinariasPorEmpresa[$empresaId]["maquinarias"][] = $maquinaria;
                require_once "app/Models/Empresa.php";
                $empresa = new Empresa;
                $empresa->consultar(null, $empresaId);
                $maquinariasPorEmpresa[$empresaId]["empresa"] = $empresa->imagen;
                unset($empresa);
            }

            $observaciones = $generadorObservaciones->consultarObservaciones($id);

            foreach ($observaciones as $observacion) {
                $empresaId = $observacion['empresaId'];
                if (!isset($maquinariasPorEmpresa[$empresaId])) {
                    $maquinariasPorEmpresa[$empresaId]["maquinarias"] = [];
                }
                if (!isset($maquinariasPorEmpresa[$empresaId]["observaciones"])) {
                    $maquinariasPorEmpresa[$empresaId]["observaciones"] = [];
                }
                $maquinariasPorEmpresa[$empresaId]["observaciones"][] = $observacion;
            }

            $empresa = New Empresa;
            $empresa->consultar(null,$generador->empresaId);

            include "reportes/generador.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
