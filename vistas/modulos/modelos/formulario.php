<?php
	if ( isset($modelo->id) ) {
		$marcaId = isset($old["marcaId"]) ? $old["marcaId"] : $modelo->marcaId;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $modelo->descripcion;
	} else {
		$marcaId = isset($old["marcaId"]) ? $old["marcaId"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="marcaId">Marca:</label>
	<select name="marcaId" id="marcaId" class="custom-select form-controls form-control-sms select2" style="width: 100%">
	<?php if ( isset($modelo->id) ) : ?>
	<!-- <select id="marcaId" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled> -->
	<?php else: ?>
		<option value="">Selecciona una Marca</option>
	<?php endif; ?>
		<?php foreach($marcas as $marca) { ?>
		<option value="<?php echo $marca["id"]; ?>"
			<?php echo $marcaId == $marca["id"] ? ' selected' : ''; ?>
			><?php echo mb_strtoupper(fString($marca["descripcion"])); ?>
		</option>
		<?php } ?>
	</select>	
</div>

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del modelo">
</div>
