<?php
	if ( isset($servicioTipo->id) ) {
		$numero = isset($old["numero"]) ? $old["numero"] : $servicioTipo->numero;
		$unidadId = isset($old["unidadId"]) ? $old["unidadId"] : $servicioTipo->unidadId;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $servicioTipo->descripcion;
	} else {
		$numero = isset($old["numero"]) ? $old["numero"] : "";
		$unidadId = isset($old["unidadId"]) ? $old["unidadId"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="numero">Número:</label>
	<input type="text" id="numero" name="numero" value="<?php echo fString($numero); ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el número del tipo de servicio">
</div>

<div class="form-group">
	<label for="unidadId">Unidad:</label>
	<select name="unidadId" id="unidadId" class="custom-select form-controls form-control-sms select2" style="width: 100%">
	<?php if ( isset($modelo->id) ) : ?>
	<!-- <select id="unidadId" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled> -->
	<?php else: ?>
		<option value="">Selecciona una Unidad</option>
	<?php endif; ?>
		<?php foreach($unidades as $unidad) { ?>
		<option value="<?php echo $unidad["id"]; ?>"
			<?php echo $unidadId == $unidad["id"] ? ' selected' : ''; ?>
			><?php echo mb_strtoupper(fString($unidad["descripcion"])); ?>
		</option>
		<?php } ?>
	</select>	
</div>

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del tipo de servicio">
</div>
