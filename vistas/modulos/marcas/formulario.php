<?php
	if ( isset($marca->id) ) {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $marca->descripcion;
	} else {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción de la marca">
</div>
