<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Presupuestos <small class="font-weight-light">Crear</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('presupuestos.index')?>"> <i class="fas fa-truck"></i> Presupuestos</a></li>
	            <li class="breadcrumb-item active">Crear presupuesto</li>
	          </ol>
	        </div>
	      </div>
	    </div><!-- /.container-fluid -->

	</section>

	<section class="content">

	<?php if ( !is_null(flash()) ) : ?>
      <div class="d-none" id="msgToast" clase="<?=flash()->clase?>" titulo="<?=flash()->titulo?>" subtitulo="<?=flash()->subTitulo?>" mensaje="<?=flash()->mensaje?>"></div>
    <?php endif; ?>

    <div class="container-fluid">
		<div class="row">
			<div class="col">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-plus"></i>
							Crear Presupuesto
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('presupuestos.store'); ?>" enctype="multipart/form-data">
							
							<?php include "vistas/modulos/presupuestos/formulario-step.php"; ?>									
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

	<div class="modal fade" id="modalAgregarCliente" aria-labelledby="modalAgregarClienteLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="modalAgregarClienteLabel">Agregar Cliente</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
			<form method="POST" id="formSendCliente">
				<?php include "vistas/modulos/clientes/formulario.php"; ?>
				<button type="button" class="btn btn-outline-primary" id="btnGuardarCliente">
					<i class="fas fa-save"></i> Guardar Cliente
				</button>										
				<div id="msgSendCliente"></div>
			</form>
	      </div>
	    </div>
	  </div>

	</div>

	<div class="modal fade" id="modalAgregarMaquinaria" aria-labelledby="modalAgregarMaquinariaLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="modalAgregarMaquinariaLabel">Agregar Maquinaria</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
			<form method="POST" id="formSendMaquinaria">
				<input type="hidden" name="accion" value="crear">
				<input type="hidden" name="_token" value="<?= token() ?>">
				<div class="row">

					<div class="col-md-6">

						<div class="card card-info card-outline">

							<div class="card-body">

								<div class="row">

									<div class="col-md-6 form-group">
										<label for="numeroEconomico">Número Económico:</label>
										<input type="text" name="numeroEconomico" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número económico">
									</div>

									<div class="col-md-6 form-group">
										<label for="marcaId">Marca:</label>

										<div class="input-group">

											<select name="marcaId" id="marcaId" class="custom-select form-controls select2Add">
											<?php if ( isset($maquinaria->id) ) : ?>
											<!-- <select id="marcaId" class="form-control select2" style="width: 100%" disabled> -->
											<?php else: ?>
											<!-- <select name="marcaId" id="marcaId" class="form-control select2Add" style="width: 100%"> -->
												<option value="">Selecciona una Marca</option>
											<?php endif; ?>
												<?php foreach($marcas as $marca) { ?>
												<option value="<?php echo $marca["id"]; ?>"
													><?php echo mb_strtoupper(fString($marca["descripcion"])); ?>
												</option>
												<?php } ?>
											</select>

											<div class="input-group-append">
												<button type="button" id="btnAddMarcaId" class="btn btn-sm btn-success" disabled>
													<i class="fas fa-plus-circle"></i>
												</button>
											</div>

										</div>
									</div>

								</div>

								<div class="row">

									<!-- <div class="form-group"> -->
									<div class="col-md-6 form-group">
										<label for="maquinariaTipoId">Tipo de Maquinaria:</label>

										<div class="input-group">

											<!-- <select name="maquinariaTipoId" id="maquinariaTipoId" class="custom-select form-controls xselect2Add" style="width: 100%"> -->
											<select name="maquinariaTipoId" id="maquinariaTipoId" class="custom-select form-controls select2Add">
											<?php if ( isset($maquinaria->id) ) : ?>
											<!-- <select id="maquinariaTipoId" class="form-control select2" style="width: 100%" disabled> -->
											<?php else: ?>
												<option value="">Selecciona un Tipo de Maquinaria</option>
											<?php endif; ?>
												<?php foreach($maquinariaTipos as $maquinariaTipo) { ?>
												<option value="<?php echo $maquinariaTipo["id"]; ?>"
													><?php echo mb_strtoupper(fString($maquinariaTipo["descripcion"])); ?>
												</option>
												<?php } ?>
											</select>

											<div class="input-group-append">
												<button type="button" id="btnAddMaquinariaTipoId" class="btn btn-sm btn-success" disabled>
													<i class="fas fa-plus-circle"></i>
												</button>
											</div>

										</div>
									</div>

								</div>

								<div class="row">

									<div class="col-md-6 form-group">
										<label for="modeloId">Modelo:</label>

										<div class="input-group">

											<select name="modeloId" id="modeloId" class="custom-select form-controls select2Add">
											<?php if ( !isset($maquinaria->id) ) : ?>
												<option value="">Selecciona un Modelo</option>
											<?php else: ?>
											<?php endif; ?>
												<?php foreach($modelos as $modelo) { ?>

												<option value="<?php echo $modelo["id"]; ?>"
													><?php echo mb_strtoupper(fString($modelo["descripcion"])); ?>
												</option>

												<?php } ?>
											
											</select>

											<div class="input-group-append">
												<button type="button" id="btnAddModeloId" class="btn btn-sm btn-success" disabled>
													<i class="fas fa-plus-circle"></i>
												</button>
											</div>

										</div>
									</div>
					
									<div class="col-md-6 form-group">
										<label for="year">Año:</label>
										<input type="text" name="year" value="" class="form-control form-control-sm campoSinDecimal" placeholder="Ingresa el año de la Maquinaria" maxlength="4">
									</div>

								</div>

								<div class="row">
									
									<div class="col-md-12 form-group">
										<label for="descripcion">Descripción:</label>
										<input type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción">
									</div>

								</div>

								<div class="row">
									
									<div class="col-md-12 form-group">
										<label for="serie">Serie:</label>
										<input type="text" name="serie" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la serie de la Maquinaria">
									</div>

								</div>

								<div class="row">

									<div class="col-md-6 form-group">
										<label for="colorId">Color:</label>

										<div class="input-group">

											<select name="colorId" id="colorId" class="custom-select form-controls select2Add">
											<?php if ( isset($maquinaria->id) ) : ?>
											<!-- <select id="colorId" class="form-control select2" style="width: 100%" disabled> -->
											<?php else: ?>
											<?php endif; ?>
												<option value="">Selecciona un Color</option>
												<?php foreach($colores as $color) { ?>
												<option value="<?php echo $color["id"]; ?>"
													><?php echo mb_strtoupper(fString($color["descripcion"])); ?>
												</option>
												<?php } ?>
											</select>

											<div class="input-group-append">
												<button type="button" id="btnAddColorId" class="btn btn-sm btn-success" disabled>
													<i class="fas fa-plus-circle"></i>
												</button>
											</div>

										</div>

									</div>

									<div class="col-md-6 form-group">
										<label for="estatusId">Estatus:</label>

										<div class="input-group">

											<select name="estatusId" id="estatusId" class="custom-select form-controls select2Add">
											<?php if ( isset($maquinaria->id) ) : ?>
											<!-- <select id="estatusId" class="form-control select2" style="width: 100%" disabled> -->
											<?php else: ?>
												<option value="">Selecciona un Estatus</option>
											<?php endif; ?>
												<?php foreach($estatus as $status) { ?>
												<option value="<?php echo $status["id"]; ?>"
													><?php echo mb_strtoupper(fString($status["descripcion"])); ?>
												</option>
												<?php } ?>
											</select>

											<div class="input-group-append">
												<button type="button" id="btnAddEstatusId" class="btn btn-sm btn-success" disabled>
													<i class="fas fa-plus-circle"></i>
												</button>
											</div>

										</div>

									</div>
					
								</div>

							</div> <!-- <div class="box-body"> -->

						</div> <!-- <div class="box box-info"> -->

					</div> <!-- <div class="col-md-6"> -->

					<div class="col-md-6">

						<div class="card card-warning card-outline">

							<div class="card-body">

								<div class="row">

									<div class="col-md-6 form-group">
										<label for="ubicacion">Ubicación:</label>

										<div class="input-group">

											<input type="text" name="ubicacion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la ubicación de la Maquinaria">
										</div>

									</div>
					
								</div>				

								<div class="form-group">
									<label for="observaciones">Observaciones:</label>
									<textarea name="observaciones" id="editor" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa las características del Producto"><?php echo fString($observaciones); ?></textarea>
								</div>
							</div> <!-- <div class="box-body"> -->

						</div> <!-- <div class="box box-warning"> -->

					</div> <!-- <div class="col-md-6"> -->

				</div> <!-- <div class="row"> -->

				<button type="button" class="btn btn-outline-primary" id="btnGuardarMaquinaria">
					<i class="fas fa-save"></i> Guardar Maquinaria
				</button>										
				<div id="msgSendMaquinaria"></div>
			</form>
	      </div>
	    </div>
	  </div>
		
	</div>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/presupuestos.js?v=1.01');
?>
