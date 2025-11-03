<?php
	if ( isset($ubicacion->id) ) {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $ubicacion->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $ubicacion->nombreCorto;
	} else {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción de la ubicación">
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto">
</div>
