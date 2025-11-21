<?php
	if ( isset($cliente->id) ) {
		$nombreCompleto = isset($old["nombreCompleto"]) ? $old["nombreCompleto"] : $cliente->nombreCompleto;
		$prefijo = isset($old["prefijo"]) ? $old["prefijo"] : $cliente->prefijo;
		$telefono = isset($old["telefono"]) ? $old["telefono"] : $cliente->telefono;
		$correo = isset($old["correo"]) ? $old["correo"] : $cliente->correo;
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $cliente->observaciones;
		$metodoPago = isset($old["metodoPago"]) ? $old["metodoPago"] : $cliente->metodoPago;
	} else {
		$nombreCompleto = isset($old["nombreCompleto"]) ? $old["nombreCompleto"] : "";
		$prefijo = isset($old["prefijo"]) ? $old["prefijo"] : "";
		$telefono = isset($old["telefono"]) ? $old["telefono"] : "";
		$correo = isset($old["correo"]) ? $old["correo"] : "";
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : "";
		$metodoPago = isset($old["metodoPago"]) ? $old["metodoPago"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="nombreCompleto">Nombre Completo:</label>
	<input type="text" class="form-control" id="nombreCompleto" name="nombreCompleto" value="<?= $nombreCompleto; ?>" required>
</div>

<div class="form-group">
	<label for="prefijo">Prefijo:</label>
	<input type="text" class="form-control" id="prefijo" name="prefijo" value="<?= $prefijo; ?>" required>
</div>

<div class="form-group">
	<label for="telefono">Teléfono:</label>
	<input type="text" class="form-control" id="telefono" name="telefono" value="<?= $telefono; ?>" required>
</div>

<div class="form-group">
	<label for="correo">Correo:</label>
	<input type="email" class="form-control" id="correo" name="correo" value="<?= $correo; ?>" required>
</div>

<div class="form-group">
	<label for="observaciones">Observaciones:</label>
	<textarea class="form-control" id="observaciones" name="observaciones" rows="3"><?= $observaciones; ?></textarea>
</div>

