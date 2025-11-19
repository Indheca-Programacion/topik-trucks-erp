<div class="row">

	<div class="col-12">

		<div class="card card-info card-outline horometroCaptura">

			<div class="card-body">

				<form id="formHorometro" enctype="multipart/form-data">

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="fecha">Fecha:</label>
						<div class="input-group date" id="fechaDTP" data-target-input="nearest">
							<input type="text" name="fecha" id="fecha" value="" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaDTP">
							<div class="input-group-append" data-target="#fechaDTP" data-toggle="datetimepicker">
	                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
	                        </div>
						</div>
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="horometroInicial">Horómetro Inicial:</label>
						<input type="text" name="horometroInicial" id="horometroInicial" value="" class="form-control form-control-sm campoSinDecimal" placeholder="Ingrese el Horómetro Inicial" maxlength="8">
					</div>

					<div class="col-md-6 form-group">
						<label for="kilometrajeInicial">Kilometraje Inicial:</label>
						<input type="text" name="kilometrajeInicial" id="kilometrajeInicial" value="" class="form-control form-control-sm campoSinDecimal" placeholder="Ingrese el Kilometraje Inicial" maxlength="10">
					</div>

					<div class="col-md-6 form-group">
						<label for="horometroFinal">Horómetro Final:</label>
						<input type="text" name="horometroFinal" id="horometroFinal" value="" class="form-control form-control-sm campoSinDecimal" placeholder="Ingrese el Horómetro Final" maxlength="8">
					</div>

					<div class="col-md-6 form-group">
						<label for="kilometrajeFinal">Kilometraje Final:</label>
						<input type="text" name="kilometrajeFinal" id="kilometrajeFinal" value="" class="form-control form-control-sm campoSinDecimal" placeholder="Ingrese el Kilometraje Final" maxlength="10">
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="archivo">Archivo:</label>
						<div class="input-group">
							<input type="text" id="archivoActual" value="" class="form-control form-control-sm text-uppercase" placeholder="Selecciona el arhivo" disabled>

							<div class="input-group-append">
								<button type="button" id="btnSubirArchivo" class="btn btn-sm btn-flat btn-info">
									<i class="fas fa-folder-open"></i> Subir
								</button>
							</div>
						</div>
						<input type="file" class="form-control form-control-sm d-none" id="archivo" name="archivo">
						<span class="text-muted">Archivo permitido PDF (con capacidad máxima de 4MB)</span>
					</div>

				</div>

				</form>

				<button type="button" id="btnAgregarHorometro" class="btn btn-success" maquinariaId="<?php echo $maquinaria->id; ?>" disabled>
					<i class="fas fa-plus"></i> Agregar registro
				</button>
				<div id="msgAgregarHorometro"></div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info"> -->

		<?php foreach($maquinaria->horometros as $key => $value) { ?>

		<?php 
		// var_dump($value);
		?>

		<?php if ( $key == 0 ) : ?>
		<!-- <div class="card card-default"> -->
		<div class="card card-info horometros">
		<?php else: ?>
		<div class="card card-info horometros collapsed-card">
		<?php endif; ?>
			<div class="card-header">
				<h3 class="card-title">Fecha: <?php echo fFechaLarga($value["fecha"]); ?></h3>

				<div class="card-tools m-0">
					<button type="button" class="btn btn-tool downloadHorometro" fecha="<?php echo $value["fecha"]; ?>">
						<i class='fas fa-download'></i>
					</button>
					<button type="button" class="btn btn-tool" data-card-widget="collapse">
						<?php if ( $key == 0 ) : ?>
						<i class="fas fa-minus"></i>
						<?php else: ?>
						<i class="fas fa-plus"></i>
						<?php endif; ?>
					</button>
					<button type="button" class="btn btn-tool eliminarHorometro" fecha="<?php echo $value["fecha"]; ?>">
						<i class="fas fa-times text-danger"></i>
					</button>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row">
					<div class="col-sm-6 col-md-3 form-group">
						<label>Horómetro Inicial:</label>
						<input type="text" value="<?php echo number_format($value['horometroInicial'], 0, '.', ','); ?>" class="form-control form-control-sm" readonly>
					</div>

					<div class="col-sm-6 col-md-3 form-group">
						<label>Kilometraje Inicial:</label>
						<input type="text" value="<?php echo number_format($value['kilometrajeInicial'], 0, '.', ','); ?>" class="form-control form-control-sm" readonly>
					</div>

					<div class="col-sm-6 col-md-3 form-group">
						<label>Horómetro Final:</label>
						<input type="text" value="<?php echo number_format($value['horometroFinal'], 0, '.', ','); ?>" class="form-control form-control-sm" readonly>
					</div>

					<div class="col-sm-6 col-md-3 form-group">
						<label>Kilometraje Final:</label>
						<input type="text" value="<?php echo number_format($value['kilometrajeFinal'], 0, '.', ','); ?>" class="form-control form-control-sm" readonly>
					</div>
				</div>
				<!-- /.row -->
			</div>
			<!-- /.card-body -->
		</div>

		<?php } ?>

	</div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->