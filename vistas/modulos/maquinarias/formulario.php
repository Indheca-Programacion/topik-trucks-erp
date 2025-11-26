<?php
	use App\Route;
	if ( isset($maquinaria->id) ) {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $maquinaria->empresaId;
		$numeroEconomico = isset($old["numeroEconomico"]) ? $old["numeroEconomico"] : $maquinaria->numeroEconomico;
		$maquinariaTipoId = isset($old["maquinariaTipoId"]) ? $old["maquinariaTipoId"] : $maquinaria->maquinariaTipoId;
		$marcaId = isset($old["marcaId"]) ? $old["marcaId"] : $maquinaria->modelo["marcaId"];
		$modeloId = isset($old["modeloId"]) ? $old["modeloId"] : $maquinaria->modeloId;
		$year = isset($old["year"]) ? $old["year"] : $maquinaria->year;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $maquinaria->descripcion;
		$serie = isset($old["serie"]) ? $old["serie"] : $maquinaria->serie;
		$colorId = isset($old["colorId"]) ? $old["colorId"] : $maquinaria->colorId;
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : $maquinaria->estatusId;
		$ubicacion = isset($old["ubicacion"]) ? $old["ubicacion"] : $maquinaria->ubicacion;
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $maquinaria->observaciones;
		$fugas = isset($old["fugas"]) ? $old["fugas"] : $maquinaria->fugas;
		$transmision = isset($old["transmision"]) ? $old["transmision"] : $maquinaria->transmision;
		$sistema = isset($old["sistema"]) ? $old["sistema"] : $maquinaria->sistema;
		$motor = isset($old["motor"]) ? $old["motor"] : $maquinaria->motor;
		$pintura = isset($old["pintura"]) ? $old["pintura"] : $maquinaria->pintura;
		$seguridad = isset($old["seguridad"]) ? $old["seguridad"] : $maquinaria->seguridad;
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$numeroEconomico = isset($old["numeroEconomico"]) ? $old["numeroEconomico"] : "";
		$maquinariaTipoId = isset($old["maquinariaTipoId"]) ? $old["maquinariaTipoId"] : "";
		$marcaId = isset($old["marcaId"]) ? $old["marcaId"] : "";
		$modeloId = isset($old["modeloId"]) ? $old["modeloId"] : "";
		$year = isset($old["year"]) ? $old["year"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$serie = isset($old["serie"]) ? $old["serie"] : "";
		$colorId = isset($old["colorId"]) ? $old["colorId"] : "";
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : "";
		$ubicacion = isset($old["ubicacion"]) ? $old["ubicacion"] : "";
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : "";
		$fugas = isset($old["fugas"]) ? $old["fugas"] : "";
		$transmision = isset($old["transmision"]) ? $old["transmision"] : "";
		$sistema = isset($old["sistema"]) ? $old["sistema"] : "";
		$motor = isset($old["motor"]) ? $old["motor"] : "";
		$pintura = isset($old["pintura"]) ? $old["pintura"] : "";
		$seguridad = isset($old["seguridad"]) ? $old["seguridad"] : "";
	}
?>

<!-- <div class="row">
	<div class="col-12"> -->
		<input type="hidden" name="maquinariaId" id="maquinariaId" value="<?=$maquinaria->id?>">
	<div class="card card-primary card-outline card-outline-tabs">
		<div class="card-header p-0 border-bottom-0">
			<ul class="nav nav-tabs" id="tabMaquinarias" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="identificacion-tab" data-toggle="pill" href="#identificacion" role="tab" aria-controls="identificacion" aria-selected="true">Identificación</a>
				</li>
				<li class="nav-item">
					<?php if ( isset($maquinaria->id) ) : ?>
					<a class="nav-link" id="horometro-tab" data-toggle="pill" href="#horometro" role="tab" aria-controls="horometro" aria-selected="false">Horómetro</a>
					<?php else: ?>
					<a class="nav-link disabled" id="horometro-tab" data-toggle="pill" role="tab" aria-controls="horometro" aria-selected="false">Horómetro</a>
					<?php endif; ?>
				</li>
				<li class="nav-item">
					<?php if ( isset($maquinaria->id) ) : ?>
					<a class="nav-link" id="servicios-tab" data-toggle="pill" href="#servicios" role="tab" aria-controls="servicios" aria-selected="false">Servicios</a>
					<?php else: ?>
					<a class="nav-link disabled" id="servicios-tab" data-toggle="pill" role="tab" aria-controls="servicios" aria-selected="false">Servicios</a>
					<?php endif; ?>
				</li>
				<li class="nav-item">
					<?php if ( isset($maquinaria->id) ) : ?>
					<a class="nav-link" id="aprovechamiento-tab" data-toggle="pill" href="#aprovechamiento" role="tab" aria-controls="aprovechamiento" aria-selected="false">Aprovechamiento</a>
					<?php else: ?>
					<a class="nav-link disabled" id="aprovechamiento-tab" data-toggle="pill" role="tab" aria-controls="aprovechamiento" aria-selected="false">Aprovechamiento</a>
					<?php endif; ?>
				</li>
				<li class="nav-item">
					<?php if ( isset($maquinaria->id) ) : ?>
					<a class="nav-link" id="fotos-tab" data-toggle="pill" href="#fotos" role="tab" aria-controls="fotos" aria-selected="false">Fotos</a>
					<?php else: ?>
					<a class="nav-link disabled" id="fotos-tab" data-toggle="pill" role="tab" aria-controls="fotos" aria-selected="false">Fotos</a>
					<?php endif; ?>
				</li>
				<li class="nav-item">
					<?php if ( isset($maquinaria->id) ) : ?>
					<a class="nav-link" id="checklist-tab" data-toggle="pill" href="#checklist" role="tab" aria-controls="checklist" aria-selected="false">Checklist</a>
					<?php else: ?>
					<a class="nav-link disabled" id="checklist-tab" data-toggle="pill" role="tab" aria-controls="checklist" aria-selected="false">Checklist</a>
					<?php endif; ?>

				</li>
				<li class="nav-item">
					<?php if ( isset($maquinaria->id) ) : ?>
					<a class="nav-link" id="kit-mantenimiento-tab" data-toggle="pill" href="#kit-mantenimiento" role="tab" aria-controls="kit-mantenimiento" aria-selected="false">Kits de Mantenimiento</a>
					<?php else: ?>
					<a class="nav-link disabled" id="kit-mantenimiento-tab" data-toggle="pill" role="tab" aria-controls="kit-mantenimiento" aria-selected="false">Kits de Mantenimiento</a>
					<?php endif; ?>
				</li>
			</ul>
		</div>
		<div class="card-body px-2">
			<div class="tab-content" id="tabMaquinariasContent">
				<div class="tab-pane fade show active" id="identificacion" role="tabpanel" aria-labelledby="identificacion-tab">
					<?php if ( isset($maquinaria->id) ) : ?>
					<form id="formSend" method="POST" action="<?php echo Route::names('maquinarias.update', $maquinaria->id); ?>">
						<input type="hidden" name="_method" value="PUT">
					<?php endif; ?>

					<?php
					include "vistas/modulos/maquinarias/form-section-identificacion.php";
					?>
					<button type="button" id="btnSend" class="btn btn-outline-primary">
					<?php if ( isset($maquinaria->id) ) : ?>
						<i class="fas fa-save"></i> Actualizar
					<?php else: ?>
						<i class="fas fa-save"></i> Guardar
					<?php endif; ?>
					</button>
					<div id="msgSend"></div>

					<?php if ( isset($maquinaria->id) ) : ?>
					</form>

					<?php endif; ?>
				</div>
				<div class="tab-pane fade" id="horometro" role="tabpanel" aria-labelledby="horometro-tab">
					<?php
					if ( isset($maquinaria->id) ) include "vistas/modulos/maquinarias/form-section-horometro.php";
					?>
				</div>
				<div class="tab-pane fade" id="servicios" role="tabpanel" aria-labelledby="servicios-tab">
					<?php
					if ( isset($maquinaria->id) ) include "vistas/modulos/maquinarias/form-section-servicios.php";
					?>
				</div>
				<div class="tab-pane fade" id="aprovechamiento" role="tabpanel" aria-labelledby="aprovechamiento-tab">
					<?php
					if ( isset($maquinaria->id) ) include "vistas/modulos/maquinarias/form-section-aprovechamiento.php";
					?>
				</div>
				<div class="tab-pane fade" id="fotos" role="tabpanel" aria-labelledby="fotos-tab">
					<?php
					if ( isset($maquinaria->id) ) include "vistas/modulos/maquinarias/form-section-fotos.php";
					?>
				</div>
				<div class="tab-pane fade" id="checklist" role="tabpanel" aria-labelledby="checklist-tab">
					<?php
					if ( isset($maquinaria->id) ) include "vistas/modulos/maquinarias/form-section-checklist.php";
					?>
				</div>
				<div class="tab-pane fade" id="kit-mantenimiento" role="tabpanel" aria-labelledby="kit-mantenimiento-tab">
					<?php
					if ( isset($maquinaria->id) ) include "vistas/modulos/maquinarias/form-section-kit-mantenimiento.php";
					?>
				</div>
			</div>
		</div> <!-- /.card -->
	</div>
<!-- 	</div>
</div> -->
