<?php
	use App\Route;
	if ( isset($servicio->id) ) {
		// var_dump($servicio);
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $servicio->empresaId;
		$servicioCentroId = $servicio->servicioCentroId;
		$numero = $servicio->numero;
		$folio = isset($old["folio"]) ? $old["folio"] : $servicio->folio;
		$maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : $servicio->maquinariaId;
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : $servicio->ubicacionId;
		$obraId = isset($old["obraId"]) ? $old["obraId"] : $servicio->obraId;
		$horoOdometro = isset($old["horoOdometro"]) ? $old["horoOdometro"] : $servicio->horoOdometro;
		$mantenimientoTipoId = isset($old["mantenimientoTipoId"]) ? $old["mantenimientoTipoId"] : $servicio->mantenimientoTipoId;
		$servicioTipoId = isset($old["servicioTipoId"]) ? $old["servicioTipoId"] : $servicio->servicioTipoId;
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : $servicio->servicioEstatusId;
		$solicitudTipoId = isset($old["solicitudTipoId"]) ? $old["solicitudTipoId"] : $servicio->solicitudTipoId;
		$horasProyectadas = isset($old["horasProyectadas"]) ? $old["horasProyectadas"] : number_format($servicio->horasProyectadas, 2, '.', ',');
		// $horasReales = number_format($servicio->horasReales, 2, '.', ',');
		$horasReales = number_format($servicio->sumHorasTrabajadas, 2, '.', ',');
		// $fechaSolicitud = isset($old["fechaSolicitud"]) ? $old["fechaSolicitud"] : fFechaLarga($servicio->fechaSolicitud);
		$fechaSolicitud = fFechaLarga($servicio->fechaSolicitud);
		$fechaProgramacion = isset($old["fechaProgramacion"]) ? $old["fechaProgramacion"] : ( is_null($servicio->fechaProgramacion) ? "" : fFechaLarga($servicio->fechaProgramacion) );
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : ( is_null($servicio->fechaFinalizacion) ? "" : fFechaLarga($servicio->fechaFinalizacion) );
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $servicio->descripcion;

		// Datos de la Maquinaria
		$maquinariaTipoDescripcion = isset($old["maquinariaTipoDescripcion"]) ? $old["maquinariaTipoDescripcion"] : $servicio->maquinaria['maquinaria_tipos.descripcion'];
		// $maquinariaUbicacionDescripcion = isset($old["maquinariaUbicacionDescripcion"]) ? $old["maquinariaUbicacionDescripcion"] : $servicio->maquinaria['ubicaciones.descripcion'];
		$maquinariaUbicacionDescripcion = isset($old["maquinariaUbicacionDescripcion"]) ? $old["maquinariaUbicacionDescripcion"] : $servicio->ubicacion['descripcion'];
		$maquinariaMarcaDescripcion = isset($old["maquinariaMarcaDescripcion"]) ? $old["maquinariaMarcaDescripcion"] : $servicio->maquinaria['marcas.descripcion'];
		$maquinariaModeloDescripcion = isset($old["maquinariaModeloDescripcion"]) ? $old["maquinariaModeloDescripcion"] : $servicio->maquinaria['modelos.descripcion'];
		$maquinariaDescripcion = isset($old["maquinariaDescripcion"]) ? $old["maquinariaDescripcion"] : $servicio->maquinaria['descripcion'];
		$maquinariaSerie = isset($old["maquinariaSerie"]) ? $old["maquinariaSerie"] : $servicio->maquinaria['serie'];
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$servicioCentroId = isset($old["servicioCentroId"]) ? $old["servicioCentroId"] : "";
		$numero = "";
		$folio = isset($old["folio"]) ? $old["folio"] : "";
		$maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : "";
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : "";
		$obraId = isset($old["obraId"]) ? $old["obraId"] : "";
		// $horoOdometro = isset($old["horoOdometro"]) ? $old["horoOdometro"] : "0.0";
		$mantenimientoTipoId = isset($old["mantenimientoTipoId"]) ? $old["mantenimientoTipoId"] : "";
		$servicioTipoId = isset($old["servicioTipoId"]) ? $old["servicioTipoId"] : "";
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : "";
		$solicitudTipoId = isset($old["solicitudTipoId"]) ? $old["solicitudTipoId"] : "";
		$horasProyectadas = isset($old["horasProyectadas"]) ? $old["horasProyectadas"] : "0.00";
		$horasReales = "";
		$fechaSolicitud = isset($old["fechaSolicitud"]) ? $old["fechaSolicitud"] : fFechaLarga(date("Y-m-d"));
		// $fechaSolicitud = fFechaLarga(date("Y-m-d"));
		$fechaProgramacion = isset($old["fechaProgramacion"]) ? $old["fechaProgramacion"] : "";
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";

		// Datos de la Maquinaria
		$maquinariaTipoDescripcion = isset($old["maquinariaTipoDescripcion"]) ? $old["maquinariaTipoDescripcion"] : "";
		$maquinariaUbicacionDescripcion = isset($old["maquinariaUbicacionDescripcion"]) ? $old["maquinariaUbicacionDescripcion"] : "";
		$maquinariaMarcaDescripcion = isset($old["maquinariaMarcaDescripcion"]) ? $old["maquinariaMarcaDescripcion"] : "";
		$maquinariaModeloDescripcion = isset($old["maquinariaModeloDescripcion"]) ? $old["maquinariaModeloDescripcion"] : "";
		$maquinariaDescripcion = isset($old["maquinariaDescripcion"]) ? $old["maquinariaDescripcion"] : "";
		$maquinariaSerie = isset($old["maquinariaSerie"]) ? $old["maquinariaSerie"] : "";
	}
?>

<div class="card card-primary card-outline card-outline-tabs">
	<div class="card-header p-0 border-bottom-0">
		<ul class="nav nav-tabs" id="tabServicio" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="orden-trabajo-tab" data-toggle="pill" href="#orden-trabajo" role="tab" aria-controls="orden-trabajo" aria-selected="true">Orden de Trabajo</a>
			</li>
			<li class="nav-item">
				<?php if ( isset($servicio->id) ) : ?>
				<a class="nav-link" id="requisiciones-tab" data-toggle="pill" href="#requisiciones" role="tab" aria-controls="requisiciones" aria-selected="false">Requisiciones</a>
				<?php else: ?>
				<a class="nav-link disabled" id="requisiciones-tab" data-toggle="pill" role="tab" aria-controls="requisiciones" aria-selected="false">Requisiciones</a>
				<?php endif; ?>
			</li>
			<li class="nav-item">
				<?php if ( isset($servicio->id) ) : ?>
				<a class="nav-link" id="actividades-tab" data-toggle="pill" href="#actividades" role="tab" aria-controls="actividades" aria-selected="false">Actividades</a>
				<?php else: ?>
				<a class="nav-link disabled" id="actividades-tab" data-toggle="pill" role="tab" aria-controls="actividades" aria-selected="false">Actividades</a>
				<?php endif; ?>
			</li>
		</ul>
	</div>
	<div class="card-body px-2">
		<div class="tab-content" id="tabServicioContent">
			<div class="tab-pane fade show active" id="orden-trabajo" role="tabpanel" aria-labelledby="orden-trabajo-tab">
				<?php if ( isset($servicio->id) ) : ?>
				<form id="formSend" method="POST" action="<?php echo Route::names('servicios.update', $servicio->id); ?>" enctype="multipart/form-data">
					<input type="hidden" name="_method" value="PUT">
				<?php else: ?>
				<form id="formSend" method="POST" action="<?php echo Route::names('servicios.store'); ?>">
				<?php endif; ?>

				<?php
				include "vistas/modulos/servicios/form-section-orden-trabajo.php";
				?>

				</form>

				<?php if ( isset($servicio->id) && $formularioEditable ) : ?>
					<form id="formArchivosSend" enctype="multipart/form-data">
						<input type="file" class="form-control form-control-sm d-none" id="archivos" name="archivos[]" multiple>
					</form>
				<?php endif; ?>

				<?php if ( isset($servicio->id) ) : ?>
					<?php if ( $formularioEditable ) : ?>
					<button type="button" id="btnSend" class="btn btn-outline-primary">
						<i class="fas fa-save"></i> Actualizar
					</button>
					<?php endif; ?>
					<a href="<?php echo Route::names('servicios.print', $servicio->id); ?>" target="_blank" class="btn btn-info float-right ml-1"><i class="fas fa-print"></i> Imprimir</a>
					<?php if ( $formularioEditable ) : ?>
					<?php if ( $permitirCancelar ) : ?>
					<button type="button" id="btnCancelar" class="btn btn-outline-danger float-right ml-1">
						<i class="fas fa-lock"></i> Cancelar OT
					</button>
					<?php endif; ?>
					<?php if ( $permitirSolicitarFinalizar && ( $servicio->cant_imagenes > 0 || $servicio->cant_archivos > 0 ) ) : ?>
					<button type="button" id="btnSolicitar" class="btn btn-outline-info float-right">
						<i class="fas fa-lock"></i> Solicitar Finalizar OT
					</button>
					<?php endif; ?>
					<?php else: ?>
					<?php if ( $permitirFinalizar && ( $servicio->cant_imagenes > 0 || $servicio->cant_archivos > 0 ) ) : ?>
					<button type="button" id="btnFinalizar" class="btn btn-outline-info float-right">
						<i class="fas fa-lock"></i> Finalizar OT
					</button>
					<?php endif; ?>
					<?php endif; ?>
				<?php else: ?>
					<button type="button" id="btnSend" class="btn btn-outline-primary">
						<i class="fas fa-save"></i> Guardar
					</button>
				<?php endif; ?>
					<div id="msgSend"></div>

				<!-- </form> -->
			</div>
			<div class="tab-pane fade" id="requisiciones" role="tabpanel" aria-labelledby="requisiciones-tab">
				<?php
				if ( isset($servicio->id) ) include "vistas/modulos/servicios/form-section-requisiciones.php";
				?>
			</div>
			<div class="tab-pane fade" id="actividades" role="tabpanel" aria-labelledby="actividades-tab">
				<?php
				if ( isset($servicio->id) ) include "vistas/modulos/servicios/form-section-actividades.php";
				?>
			</div>
		</div>
	</div> <!-- /.card -->
</div>