<?php
	if ( isset($configuracionProgramacion->id) ) {
		$unidadesAbrirServicio = isset($old["unidadesAbrirServicio"]) ? $old["unidadesAbrirServicio"] : $configuracionProgramacion->unidadesAbrirServicio;
	} // else {
		// $unidadesAbrirServicio = isset($old["unidadesAbrirServicio"]) ? $old["unidadesAbrirServicio"] : "0";
	// }
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-header">
              <h3 class="card-title">Configuración general</h3>
            </div>

            <div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<label for="inicialServicioEstatusId">Tipos de Servicio:</label>
				<?php foreach($servicioTipos as $servicioTipo) { ?>
					<div class="form-group mb-1">
						<div class="input-group">
							<input type="text" class="form-control form-control-sm" value="<?php echo mb_strtoupper(fString($servicioTipo["descripcion"])); ?>" readonly>
							<div class="input-group-append">
								<div class="input-group-text">
									<input type="checkbox" name="servicioTipos[]" value="<?php echo $servicioTipo["id"]; ?>" <?php echo $configuracionProgramacion->checkServicioTipo($servicioTipo["id"]) ? "checked" : ""; ?>>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="form-group mt-2">
					<label class="mt-2" for="unidadesAbrirServicio">Unidades para abrir una Orden de Trabajo:</label>
					<input type="text" id="unidadesAbrirServicio" name="unidadesAbrirServicio" value="<?php echo $unidadesAbrirServicio; ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa una cantidad">
				</div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
