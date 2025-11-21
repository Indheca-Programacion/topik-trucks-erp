<?php
	if ( isset($generadorObservacion->id) ) {
		$generadorId = isset($old["generadorId"]) ? $old["generadorId"] : $generadorObservacion->generadorDetalle;
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : $generadorObservacion->fecha_inicio;
		$fecha_fin = isset($old["fecha_fin"]) ? $old["fecha_fin"] : $generadorObservacion->fecha_fin;
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $generadorObservacion->observaciones;
	} else {
		$generadorId = isset($old["generadorId"]) ? $old["generadorId"] : "";
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : "";
		$fecha_fin = isset($old["fecha_fin"]) ? $old["fecha_fin"] : "";
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<div class="row">
	<!-- Numero economico -->
	<div class="col-md-6">
		<label for="">Numero Económico:</label>
		<select class="select2" name="generadorDetalle" id="modalAgregarObservacion_numero">
			<option value="">Seleccione un equipo</option>
			<?php foreach($maquinarias as $maquinaria) { ?>
			<option value="<?php echo $maquinaria["generadorId"]; ?>"
				<?php echo $generadorId == $maquinaria["generadorId"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
			</option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="row">
	<!-- Fecha de inicio -->
	<div class="col-md-6">
		<label for="start_date">Fecha de inicio:</label>
		<div class="input-group date" id="start_date" data-target-input="nearest">
			<input type="text" name="fecha_inicio" value="<?= $fecha_inicio ?>" class="form-control datetimepicker-input form-control-sm" id="modalAgregarObservacion_fecha_inicio" data-target="#start_date" />
			<div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fa fa-calendar"></i></div>
			</div>
		</div>
	</div>
	<!-- Fecha fin -->
	<div class="col-md-6">
		<div class="form-group">
			<label for="end_date">Fecha de fin:</label>
			<div class="input-group date" id="end_date" data-target-input="nearest">
				<input type="text" name="fecha_fin" value="<?= $fecha_inicio ?>" class="form-control form-control-sm datetimepicker-input" id="modalAgregarObservacion_fecha_fin" data-target="#end_date" />
				<div class="input-group-append" data-target="#end_date" data-toggle="datetimepicker">
					<div class="input-group-text"><i class="fa fa-calendar"></i></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<!-- Observaciones -->
	<div class="col form-group">
		<label for="">Observaciones</label>
		<textarea name="observaciones" id="modalAgregarObservacion_observacion" class="form-control"><?php echo $observaciones; ?></textarea>
	</div>
</div>

