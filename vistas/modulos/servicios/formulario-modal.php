<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="fechaSolicitud">Fecha Solicitud:</label>
						<div class="input-group date" id="fechaSolicitudDTP" data-target-input="nearest">
							<input type="date" id="fechaSolicitud" name="fechaSolicitud[]" value="<?php echo date("Y-m-d"); ?>" class="form-control form-control-sm" placeholder="Ingresa la fecha de solicitud">
						</div>
					</div>

					<div class="col-md-6 form-group">
						<label for="maquinariaUbicacionDescripcion">Ubicación:</label>
						<input type="text" name="ubicacion[]" id="ubicacion" value="" class="form-control form-control-sm text-uppercase">
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="folio">Folio de Orden de Trabajo:</label>
						<input type="text" id="folio" name="folio[]" value="" class="form-control form-control-sm text-uppercase" placeholder="" disabled>
					</div>
					
					<div class="col-md-6 form-group">
						<label for="horasProyectadas">Horas Hombre Proyectadas:</label>
						<input type="text" id="horasProyectadas" name="horasProyectadas[]" value="0" class="form-control form-control-sm text-right campoConDecimal" placeholder="Ingresa las horas proyectadas">
					</div>

					<input type="hidden" id="estatusId" name="estatusId[]" value="1">

				</div>

				
				<div class="form-group">
					<label for="descripcion">Descripción del Trabajo a realizar:</label>
					<textarea name="descripcion[]" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa la Descripción del Trabajo a realizar"></textarea>
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-body">

				<div class="row">

					<div class="col-md-6 form-group">

						<label for="mantenimientoTipoId">Tipo de Mantenimiento:</label>
						<select name="mantenimientoTipoId[]" id="mantenimientoTipoId" class="custom-select form-controls">
							<option value="">Selecciona un Tipo de Mantenimiento</option>
							<?php foreach($mantenimientoTipos as $mantenimientoTipo) { ?>
							<option value="<?php echo $mantenimientoTipo["id"]; ?>"
								><?php echo mb_strtoupper(fString($mantenimientoTipo["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

					<div class="col-md-6 form-group">

						<label for="servicioTipoId">Tipo de Servicio:</label>
						<select name="servicioTipoId[]" id="servicioTipoId" class="custom-select form-controls">
							<option value="">Selecciona un Tipo de Servicio</option>
							<?php foreach($servicioTipos as $servicioTipo) { ?>
							<option value="<?php echo $servicioTipo["id"]; ?>"
								><?php echo mb_strtoupper(fString($servicioTipo["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

				</div>

				<hr>

				<div class="form-group">
					<label for="descripcion">Evidencia del trabajo terminado:</label>
					<div class="subir-fotos mb-1">

						<button type="button" class="btn btn-info mb-2 btnSubirFotos" data-id="1">
							<i class="fas fa-images"></i> Subir Fotos
						</button>

						<span class="previsualizar">
						</span>

						<input type="file" class="form-control form-control-sm d-none" id="imagenes_1" name="imagenes_1[]" multiple>

					</div>
					<div class="mb-1 text-muted">Archivos permitidos JPG O PNG (con capacidad máxima de 1MB)</div>
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
