<?php
	if ( isset($puesto->id) ) {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $puesto->nombre;
	} else {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="nombreCorto">Nombre:</label>
	<input type="text" id="nombre" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre ">
</div>
