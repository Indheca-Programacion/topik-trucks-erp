<?php
	$old = old();

	use App\Route;
	$ruta= Route::rutaServidor().'vistas/img/qr/qr-code.png';
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Maquinarias <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('maquinarias.index')?>"> <i class="fas fa-truck"></i> Maquinarias</a></li>
	            <li class="breadcrumb-item active">Editar maquinaria</li>
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
			<div class="col-md-12">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Editar maquinaria
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<?php include "vistas/modulos/maquinarias/formulario.php"; ?>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	    <!-- Modal id="modalCrearServicio" -->
    <div class="modal fade" id="modalCrearServicio" data-backdrop="static" data-keyboard="false" aria-labelledby="modalCrearServicioLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollablex">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCrearServicioLabel"><i class="fas fa-plus"></i> Crear Servicio</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger error-validacion mb-2 d-none">
              <ul class="mb-0">
                <!-- <li></li> -->
              </ul>
            </div>

            <form id="formCrearServicioSend">

              <div class="row">

                <div class="col-lg-6">

                  <div class="card card-info card-outline mb-0">

                    <div class="card-body p-3">

                      <div class="row">

                        <div class="col-12 form-group">
                          <label for="modalCrearServicio_empresa">Empresa:</label>
                          <select type="text" id="modalCrearServicio_empresa" name="empresaId" class="form-control form-control-sm text-uppercase select2" placeholder="Ingresa la empresa">
                            <option value="">Selecciona una Empresa</option>
							<?php foreach($empresas as $key => $value) { ?>
							  <option value="<?php echo $value['id']; ?>"><?php echo $value['razonSocial']; ?></option>
							<?php } ?>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_servicioCentroId">Centro de Servicio:</label>
                          <select name="servicioCentroId" id="modalCrearServicio_servicioCentroId" class="custom-select form-controls select2">
                            <option value="">Selecciona un Centro</option>
							<?php foreach($servicioCentros as $key => $value) { ?>
							  <option value="<?php echo $value['id']; ?>"><?php echo $value['descripcion']; ?></option>
							<?php } ?>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group d-none">
                          <label for="modalCrearServicio_servicioEstatusId">Estatus:</label>
                          <select name="servicioEstatusId" id="modalCrearServicio_servicioEstatusId" class="custom-select form-controls " readonly>
                            <option value="">Selecciona un Estatus</option>
							<?php foreach($estatus as $key => $value) { ?>
							  <option value="<?php echo $value['id']; ?>" <?php if ( $value['id'] == 1 ) echo 'selected'; ?>><?php echo $value['descripcion']; ?></option>
							<?php } ?>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group d-none">
                          <label for="modalCrearServicio_solicitudTipoId">Tipo de Solicitud:</label>
                          <select name="solicitudTipoId" id="modalCrearServicio_solicitudTipoId" class="custom-select form-controls">
                            <option value="">Selecciona un Tipo de Solicitud</option>
                            <?php foreach($tiposSolicitud as $key => $value) { ?>
                              <option value="<?php echo $value['id']; ?>" <?php if ( $value['id'] == 1 ) echo 'selected'; ?>><?php echo $value['descripcion']; ?></option>
                            <?php } ?>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_fechaSolicitud">Fecha Solicitud:</label>
                          <div class="input-group date" id="modalCrearServicio_fechaSolicitudDTP" data-target-input="nearest">
                            <?php $permitirModificarFechas = false; ?>
                            <?php $fechaSolicitud = fFechaLarga(date("Y-m-d")); ?>
                            <input type="text" id="fechaSolicitud" value="<?php echo $fechaSolicitud; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#modalCrearServicio_fechaSolicitudDTP" disabled>
                            <div class="input-group-append" data-target="#modalCrearServicio_fechaSolicitudDTP" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                            </div>
                          </div>
                        </div>

                        <div class="col-xl-6 form-group mb-0">
                          <label for="modalCrearServicio_horasProyectadas">Horas Hombre Proyectadas:</label>
                          <input type="text" id="modalCrearServicio_horasProyectadas" name="horasProyectadas" value="" class="form-control form-control-sm text-right campoConDecimal" placeholder="Ingresa las horas proyectadas">
                        </div>

                      </div> <!-- <div class="row"> -->

                    </div> <!-- <div class="card-body"> -->

                  </div> <!-- <div class="card card-info card-outline"> -->
                </div> <!-- <div class="col-md-6"> -->

                <div class="col-lg-6">

                  <div class="card card-warning card-outline mb-0">

                    <div class="card-body p-3">

                      <div class="row">

                        <div class="col-xl-6 form-group d-none">
                          <label for="modalCrearServicio_mantenimientoTipoId">Tipo de Mantenimiento:</label>
                          <select name="mantenimientoTipoId" id="modalCrearServicio_mantenimientoTipoId" class="custom-select form-controls select2">
                            <option value="">Selecciona un Tipo de Mantenimiento</option>
                              <?php foreach($tiposMantenimiento as $key => $value) { ?>
                                <option value="<?php echo $value['id']; ?>" <?php if ( $value['id'] == 1 ) echo 'selected'; ?>><?php echo $value['descripcion']; ?></option>
                              <?php } ?>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <input type="hidden" name="servicioTipoId" id="modalCrearServicio_servicioTipoId" value="">
                          <label for="modalCrearServicio_servicioTipo">Tipo de Servicio:</label>
                          <select name="servicioTipoId" id="modalCrearServicio_servicioTipoId" class="custom-select form-control form-control-sm text-uppercase select2">
							<option value="">Selecciona un Tipo de Servicio</option>
							<?php foreach($servicioTipos as $key => $value) { ?>
							  <option value="<?php echo $value['id']; ?>"><?php echo $value['descripcion']; ?></option>
							<?php } ?>
						  </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <input type="hidden" name="maquinariaId" id="modalCrearServicio_maquinariaId" value="<?php echo $maquinaria->id; ?>">
                          <label for="modalCrearServicio_numeroEconomico">Número Económico:</label>
                          <input type="text" id="modalCrearServicio_numeroEconomico" value="<?php echo $maquinaria->numeroEconomico; ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número económico" readonly>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_ubicacion">Ubicación:</label>
                            <select name="ubicacionId" id="modalCrearServicio_ubicacion" class="custom-select form-control form-control-sm text-uppercase select2">
								<option value="">Selecciona una Ubicación</option>
								<?php foreach($ubicaciones as $key => $value) { ?>
								  <option value="<?php echo $value['id']; ?>" <?php if ( $maquinaria->ubicacionId == $value['id'] ) echo 'selected'; ?>><?php echo $value['descripcion']; ?></option>
								<?php } ?>
                            </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_obra">Obra:</label>
                          <select name="obraId" id="modalCrearServicio_obra" class="custom-select form-control form-control-sm text-uppercase select2">
							<option value="">Selecciona una Obra</option>
							<?php foreach($obras as $key => $value) { ?>
							  <option value="<?php echo $value['id']; ?>" <?php if ( $maquinaria->obraId == $value['id'] ) echo 'selected'; ?>><?php echo $value['nombreCorto']; ?></option>
							<?php } ?>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_fechaProgramacion">Fecha de finalización estimada:</label>
                          <div class="input-group date" id="modalCrearServicio_fechaProgramacionDTP" data-target-input="nearest">
                            <input type="text" name="fechaProgramacion" id="modalCrearServicio_fechaProgramacion" value="" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de finalización estimada" data-target="#modalCrearServicio_fechaProgramacionDTP">
                            <div class="input-group-append" data-target="#modalCrearServicio_fechaProgramacionDTP" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                            </div>
                          </div>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_kitMantenimiento">Seleccionar kit de mantenimiento:</label>
                          <select name="kitMantenimiento" id="modalCrearServicio_kitMantenimiento" class="custom-select form-control form-control-sm text-uppercase select2">
							<?php foreach($kitsMantenimiento as $key => $value) { ?>
							  <option value="<?php echo $value['id']; ?>"><?php echo $value['tipoMantenimiento']; ?></option>
							<?php } ?>
                          </select>
                        </div>

                        <div class="col-12 form-group mb-0">
                          <label for="descripcion">Descripción del Trabajo a realizar:</label>
                          <textarea name="descripcion" id="modalCrearServicio_descripcion" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa la Descripción del Trabajo a realizar"></textarea>
                        </div>

                      </div> <!-- <div class="row"> -->

                    </div> <!-- <div class="card-body"> -->

                  </div> <!-- <div class="card card-warning card-outline"> -->

                </div> <!-- <div class="col-md-6"> -->

              </div> <!-- <div class="row"> -->

            </form>

            <!-- <div class="alert alert-danger error-validacion mb-0 d-none">
              <ul class="mb-0">
                <li></li>
              </ul>
            </div> -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-outline-primary btnGuardar">
              <i class="fas fa-save"></i> Guardar
            </button>
          </div>
        </div>
      </div>
    </div>

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/maquinarias.js?v=1.4');
	array_push($arrayArchivosJS, 'vistas/js/maquinaria-horometros.js?v=1.01');
	array_push($arrayArchivosJS, 'vistas/js/maquinaria-servicios.js?v=1.00');
?>
