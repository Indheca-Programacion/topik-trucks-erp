<?php

namespace App\Controllers;

require_once "app/Models/ChecklistMaquinaria.php";
require_once "app/Policies/ChecklistMaquinariaPolicy.php";
require_once "app/Requests/SaveChecklistMaquinariasRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\ChecklistMaquinaria;
use App\Policies\ChecklistMaquinariaPolicy;
use App\Requests\SaveChecklistMaquinariasRequest;
use App\Route;

class ChecklistMaquinariasController
{
    public function index()
    {
        Autorizacion::authorize('view', new ChecklistMaquinaria);

        $ChecklistMaquinaria = New ChecklistMaquinaria;

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/checklist-maquinarias/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create($id = null)
    {
        $checklistMaquinaria = new ChecklistMaquinaria;
        Autorizacion::authorize('create', $checklistMaquinaria);

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        require_once "app/Models/Ubicacion.php";
        $ubicacion = New \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        $old["maquinariaId"] = $id;

        $maquinariaId = $id;

        require_once "app/Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultar();

        $contenido = array('modulo' => 'vistas/modulos/checklist-maquinarias/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', new ChecklistMaquinaria);

        $request = SaveChecklistMaquinariasRequest::validated();

        $checklistMaquinaria = new ChecklistMaquinaria;
        
        $respuesta = $checklistMaquinaria->crear($request);


        if ($respuesta) {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'bg-success',
                'titulo' => 'Crear Checklist Maquinaria',
                'subTitulo' => 'OK',
                'mensaje' => 'El checklist de maquinaria fue creado correctamente'
            );
            header("Location:" . Route::names('checklist-maquinarias.edit', $checklistMaquinaria->id));
        } else {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'bg-danger',
                'titulo' => 'Crear Checklist Maquinaria',
                'subTitulo' => 'Error',
                'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo'
            );
            header("Location:" . Route::routes('maquinarias.crear-checklist', $request["maquinariaId"]));
        }

        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new ChecklistMaquinaria);

        $checklistMaquinarias = new ChecklistMaquinaria;

        if ($checklistMaquinarias->consultar(null, $id)) {

            $imagenesSubidas = $checklistMaquinarias->obtenerImagenes($id);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, usuarioAutenticado()["id"]);

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinarias = $maquinaria->consultar();
            $maquinaria->consultar(null, $checklistMaquinarias->maquinariaId);

            require_once "app/Models/ChecklistTarea.php";
            $checklistTarea = New \App\Models\ChecklistTarea;
            $checklistTareas = $checklistTarea->consultar('maquinariaTipoId', $maquinaria->maquinariaTipoId);

            $checkListTareasRespuestas = $checklistTarea->consultarRespuestas($id);
            $checkListTareasObservaciones = $checklistTarea->consultarObservaciones($id);

            // Agrupar tareas por sección
            $tareasPorSeccion = [];
            // Agrupar tareas por sección con el formato solicitado
            $tareasPorSeccion = [];

            // Agrupar tareas por sección, usando un array indexado en lugar de asociativo por id
            $secciones = [];

            foreach ($checklistTareas as $tarea) {
                $seccion = $tarea["seccion"] ?? 'Sin Sección';
                $sectionId = $tarea["sectionId"] ?? null;

                // Buscar si la sección ya existe en el array
                $indiceSeccion = null;
                foreach ($secciones as $idx => $sec) {
                    if ($sec['id'] === $sectionId) {
                        $indiceSeccion = $idx;
                        break;
                    }
                }

                if ($indiceSeccion === null) {
                    // No existe, agregar nueva sección
                    $secciones[] = [
                        'id' => $sectionId,
                        'descripcion' => mb_strtoupper($seccion),
                        'opciones' => [],
                        'tiene_observaciones' => true
                    ];
                    $indiceSeccion = array_key_last($secciones);
                }

                $secciones[$indiceSeccion]['opciones'][] = [
                    'id' => $tarea["id"],
                    'texto' => mb_strtoupper($tarea["descripcion"]),
                    'clasificaciones' => [
                        ['valor' => '1', 'texto' => 'Bueno'],
                        ['valor' => '2', 'texto' => 'Malo'],
                        ['valor' => '0', 'texto' => 'N/A']
                    ]
                ];
            }

            // Array de respuestas
            $respuestas = [];
            foreach ($checkListTareasRespuestas as $respuesta) {
                $seccion = $respuesta['seccion'] ?? 'Sin Sección';
                if (!isset($respuestas[$seccion])) {
                    $respuestas[$seccion] = [];
                }
                $respuestas[$seccion][] = $respuesta;
            }

            $tareasPorSeccion = $secciones;

            // Agrupar observaciones por sección en un array asociativo
            $observacionesPorSeccion = [];
            foreach ($checkListTareasObservaciones as $observacion) {
                $seccion = $observacion['seccion'] ?? 'Sin Sección';
                if (!isset($observacionesPorSeccion[$seccion])) {
                    $observacionesPorSeccion[$seccion] = [];
                }
                $observacionesPorSeccion[$seccion]= $observacion;
            }

            // echo "<pre>";
            // var_dump($observacionesPorSeccion);
            // echo "</pre>";
            // die();
            
            $contenido = array('modulo' => 'vistas/modulos/checklist-maquinarias/editar.php');
            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', new ChecklistMaquinaria);

        $request = SaveChecklistMaquinariasRequest::validated($id);

        $checklistMaquinaria = new ChecklistMaquinaria;
        $checklistMaquinaria->id = $id;
        $respuesta = $checklistMaquinaria->actualizar($request);

        if ($respuesta) {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'bg-success',
                'titulo' => 'Actualizar Checklist Maquinaria',
                'subTitulo' => 'OK',
                'mensaje' => 'El checklist de maquinaria fue actualizado correctamente'
            );
            header("Location:" . Route::names('checklist-maquinarias.index'));
        } else {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'bg-danger',
                'titulo' => 'Actualizar Checklist Maquinaria',
                'subTitulo' => 'Error',
                'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo'
            );
            header("Location:" . Route::names('checklist-maquinarias.edit', $id));
        }

        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', new ChecklistMaquinaria);

        // Sirve para validar el Token
        if (!SaveChecklistMaquinariasRequest::validatingToken($error)) {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'bg-danger',
                'titulo' => 'Eliminar Checklist Maquinaria',
                'subTitulo' => 'Error',
                'mensaje' => $error
            );
            header("Location:" . Route::names('checklist-maquinarias.index'));
            die();
        }

        $checklistMaquinaria = new ChecklistMaquinaria;
        $checklistMaquinaria->id = $id;
        $respuesta = $checklistMaquinaria->eliminar();

        if ($respuesta) {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'bg-success',
                'titulo' => 'Eliminar Checklist Maquinaria',
                'subTitulo' => 'OK',
                'mensaje' => 'El checklist de maquinaria fue eliminado correctamente'
            );
            header("Location:" . Route::names('checklist-maquinarias.index'));
        } else {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
                'clase' => 'bg-danger',
                'titulo' => 'Eliminar Checklist Maquinaria',
                'subTitulo' => 'Error',
                'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este checklist de maquinaria no se podrá eliminar ***'
            );
            header("Location:" . Route::names('checklist-maquinarias.index'));
        }

        die();
    }

    public function print($id)
    {
        Autorizacion::authorize('view', new ChecklistMaquinaria);

        $checklistMaquinarias = new ChecklistMaquinaria;

        if ($checklistMaquinarias->consultar(null, $id)) {

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null,  $checklistMaquinarias->obraId);

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicacion->consultar(null, $checklistMaquinarias->ubicacionId);

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinaria->consultar(null, $checklistMaquinarias->maquinariaId);

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $maquinaria->empresaId);

            require_once "app/Models/ChecklistTarea.php";
            $checklistTarea = New \App\Models\ChecklistTarea;
            $respuestas = $checklistTarea->consultarRespuestas($checklistMaquinarias->id);

            // Crear un array asociativo con la descripción como clave y el campo 'respuesta' como valor
            $respuestasPorDescripcion = [];
            foreach ($respuestas as $respuesta) {
                $descripcion = mb_strtoupper($respuesta['descripcion']) ?? '';
                $respuestasPorDescripcion[$descripcion] = $respuesta['respuesta'] ?? null;
            }
            // Agrupar tareas por sección
            $tareasPorSeccion = [];

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $checklistMaquinarias->usuarioIdCreacion);

            $operador = $usuario->nombreCompleto;
            $operadorFirma = $usuario->firma;
            unset($usuario);

            $usuario = New \App\Models\Usuario;

            $supervisor = "";
            $supervisorFirma = "";

            if (!is_null($checklistMaquinarias->usuarioIdAutorizacion)) {
                $usuario->consultar(null, $checklistMaquinarias->usuarioIdAutorizacion);
                $supervisor = $usuario->nombreCompleto;
                $supervisorFirma = $usuario->firma;
            }

            $supervisorCliente = "";
            $supervisorClienteFirma = "";

            if (!is_null($checklistMaquinarias->usuarioIdAutorizacionCliente)) {
                $usuario->consultar(null, $checklistMaquinarias->usuarioIdAutorizacionCliente);
                $supervisorCliente = $usuario->nombreCompleto;
                $supervisorClienteFirma = $usuario->firma;
            }

            if ( $maquinaria->maquinariaTipoId == 16) {
                include "reportes/checklist_grua.php";
            } else {
                echo "<h1>Reporte no disponible</h1>";
            }


        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
