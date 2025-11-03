<?php
	if ( isset($vendedor->id) ) {
		$nombre = isset($old["nombreCompleto"]) ? $old["nombreCompleto"] : $vendedor->nombreCompleto;
		$telefono = isset($old["telefono"]) ? $old["telefono"] : $vendedor->telefono;
		$correo = isset($old["correo"]) ? $old["correo"] : $vendedor->correo;
		$zona = isset($old["zona"]) ? $old["zona"] : $vendedor->zona;
	} else {
		$nombre = isset($old["nombreCompleto"]) ? $old["nombreCompleto"] : "";
		$telefono = isset($old["telefono"]) ? $old["telefono"] : "";
		$correo = isset($old["correo"]) ? $old["correo"] : "";
		$zona = isset($old["zona"]) ? $old["zona"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<div class="form-group">
	<label for="nombreCompleto">Nombre Completo:</label>
	<input type="text" id="nombreCompleto" name="nombreCompleto" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre completo">
</div>
<div class="form-group">
	<label for="telefono">Teléfono:</label>
	<input type="text" id="telefono" name="telefono" value="<?php echo fString($telefono); ?>" class="form-control form-control-sm" placeholder="Ingresa el teléfono">
</div>
<div class="form-group">
	<label for="correo">Correo Electrónico:</label>
	<input type="email" id="correo" name="correo" value="<?php echo fString($correo); ?>" class="form-control form-control-sm" placeholder="Ingresa el correo electrónico">
</div>
<div class="form-group">
	<label for="zona">Zona:</label>
	<input type="text" id="zona" name="zona" value="<?php echo fString($zona); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la zona">
</div>

