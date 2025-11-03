<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Gastos <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('gastos.index')?>"> <i class="fas fa-list-alt"></i> Gastos</a></li>
	            <li class="breadcrumb-item active">Editar gastos</li>
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
			<div class="col-md-6">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Editar gastos
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body" id="gasto-section">
						<div class="alert alert-danger error-validacion d-none">
							<ul class="mb-0">
								<li></li>
							</ul>
						</div>
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('gastos.update', $gastos->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/gastos/formulario.php"; ?>
							<div class="row">
								<div class="col-md-12">
									<button type="button" id="btnSend" class="btn btn-outline-primary <?php if($gastos->requisicionId !== null) echo 'd-none' ;?>">
										<i class="fas fa-save"></i> Actualizar
									</button>
									<a href="<?php echo Route::names('gastos.print', $gastos->id); ?>" target="_blank" class="btn btn-info"><i class="fas fa-print"></i> Imprimir</a>
									<button type="button" id="btnDownload" class="btn btn-outline-primary <?php if($gastos->cerrada == 0) echo 'd-none' ;?>"><i class="fas fa-download"></i> Descargar Archivos</button>
									<?php if ( $gastos->requisicionId !== null ) : ?>
										<?php if ( $gastos->cerrada == 1 ) : ?>
											<button type="button" id="btnEnProceso" data-estatus="2" class="btn btn-primary btn-cambiar-estatus">
												<i class="fas fa-location-arrow"></i> Marcar como en Proceso
											</button>
										<?php endif; ?>
										<?php if ( $gastos->cerrada == 2 ) : ?>
											<button type="button" id="btnProcesado" data-estatus="3" class="btn btn-success btn-cambiar-estatus">
												<i class="fas fa-check"></i> Marcar Procesado
											</button>
										<?php endif; ?>
										<?php if ( $gastos->cerrada == 3 ) : ?>
											<button type="button" id="btnPagado" data-estatus="4" class="btn btn-success btn-cambiar-estatus">
												<i class="fas fa-money-check-alt"></i> Marcar como Pagado
											</button>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ( is_null($gastos->usuarioIdAutorizacion) && $permitirAutorizar ) : ?>
										<button type="button" id="btnAutorizar" class="btn btn-success float-right" >
											<i class="fas fa-check"></i> Autorizar Gasto
										</button>
									<?php endif; ?>
									<?php if ( is_null($gastos->usuarioIdRevision) && $permitirRevisar ) : ?>
										<button type="button" id="btnRevisar" class="btn btn-success float-right mx-2">
											<i class="fas fa-eye"></i> Revisar Gasto
										</button>
									<?php endif; ?>
									<?php if ( !is_null($gastos->usuarioIdRevision) ) : ?>
										<span class="float-right mx-2">
											<span class="badge badge-success p-2" style="font-size: 1rem;">
												<i class="fas fa-eye"></i> Revisado (Revisó: <?=$revisoNombre?>)
											</span>
										</span>
									<?php endif; ?>
								</div>
							</div>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
			<?php if($gastos->cerrada == 0 ) : ?>
				<div class="col-md-6">
					<div class="card card-success card-outline">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-edit"></i>
								Añadir Gastos
							</h3>
						</div><!-- <div class="card-header"> -->
						<div class="card-body" id="addPartidas">
							<div class="alert alert-danger error-validacion d-none">
								<ul class="mb-0">
									<li></li>
								</ul>
							</div>
							<?php include "vistas/modulos/errores/form-messages.php"; ?>
							<?php include "vistas/modulos/gastos/form-add.php"; ?>
						</div><!-- /.card-body -->
					</div><!-- /.card -->
				</div> <!-- /.col -->
			<?php endif ?>
			<div class="col-12">
				<div class="card card-warning card-outline">
					<div class="card-header">
						<h3 class="card-title">
							Gastos
						</h3>
					</div><!-- <div class="card-header"> -->
                    <div class="card-body">
						<input type="file" id="archivoSubir" style="display: none;" multiple>
                        <table class="table table-sm table-bordered table-striped" id="tablaDetallesGastos" width="100%">
							<thead>
								<tr>
									<th style="width:10px">#</th>
									<th>Fecha</th>
									<th>Tipo de Gasto</th>
									<th>Costo</th>
									<th>Cantidad</th>
									<th>Unidad</th>
									<th>Factura</th>
									<th>Observaciones</th>
									<th>Numero Economico</th>
									<th>Acciones</th>
								</tr> 
							</thead>
						</table>
						<button class="btn btn-info mt-2 float-right" id="btnDescargarFacturas">
							<i class="fas fa-download"></i> Descargar Facturas
						</button>
                    </div><!-- /.card-body -->
                </div><!-- /.card -->
			</div>
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

	<!-- Modal id="modalVerArchivos" -->
	<div class="modal fade" id="modalVerArchivos" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalVerArchivosLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVerArchivosLabel">Evidencia Documental <span></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="accordion" id="accordionArchivos">
					</div>
					<div class="alert alert-danger error-validacion d-none">
						<ul class="mb-0">
							<li></li>
						</ul>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalSeleccionarItem" tabindex="-1" role="dialog" aria-labelledby="modalSeleccionarItemLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalSeleccionarItemLabel">Selecciona un concepto de la factura</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<select id="selectItemFactura" class="form-control select2">
						
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" id="btnSeleccionarItemFactura" class="btn btn-primary">Seleccionar</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/gastos.js?v=1.8');
?>
