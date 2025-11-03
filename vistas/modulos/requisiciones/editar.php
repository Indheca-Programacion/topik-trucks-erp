<?php
	$old = old();

	$cantidadComprobantes = count($requisicion->comprobantesPago);
	$cantidadOrdenes = count($requisicion->ordenesCompra);
	$cantidadFacturas = count($requisicion->facturas);
	$cantidadCotizaciones = count($requisicion->cotizaciones);
	$cantidadVales = count($requisicion->valesAlmacen);
	$cantidadSoportes = count($requisicion->soportes);

	$cantidadDocs = $cantidadComprobantes+$cantidadOrdenes+$cantidadFacturas+$cantidadCotizaciones+$cantidadVales+$cantidadSoportes;

	use App\Route;
?>

<style>

#loading {
	display: none;
	text-align: center;
}
#loading::after {
	display: inline-block;
	margin-left: 10px;
	animation: spin 1s linear infinite;
}

@media (max-width: 768px) {
	#chatCard {
		width: 100%;
		right: 0;
				<div class="card card-info direct-chat direct-chat-info collapsed-card " style="position:fixed; width:fit-content; bottom:0; right:2rem; z-index:3;" id="chatCard">
	}
	#chatCard .card-footer {
		padding: 10px;
	}
	#chatCard .input-group {
		flex-wrap: wrap;
	}
	#chatCard .input-group .form-control {
		margin-bottom: 5px;
	}
}

</style>

<div class="content-wrapper">



	<section class="content-header">
		<div class="container-fluid">
	      <div class="row mb-2 ">
	        <div class="col-sm-6">
	          <h1>Requisiciones <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('requisiciones.index')?>"> <i class="fas fa-tools"></i> Requisiciones</a></li>
	            <li class="breadcrumb-item active">Editar requisición</li>
	          </ol>
	        </div>
	      </div>
	    </div><!-- /.container-fluid -->

	</section>

	<section class="content requisiciones">

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
								Editar requisición
							</h3>
						</div> <!-- <div class="card-header"> -->
						<div class="card-body">
							<?php include "vistas/modulos/errores/form-messages.php"; ?>
							<form id="formSend" method="POST" action="<?php echo Route::names('requisiciones.update', $requisicion->id); ?>" enctype="multipart/form-data">
								<input type="hidden" name="_method" value="PUT">
								<?php 
									if ( isset($traslado->id) ) {
										include "vistas/modulos/requisicion-traslados/formulario.php"; 
									} else {
										include "vistas/modulos/requisiciones/formulario.php"; 
									}
								?>

							</form>
							<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				</div> <!-- /.col -->
			</div> <!-- ./row -->
		</div><!-- /.container-fluid -->

	</section>

		
	<?php if ( isset($requisicion->id) ) : ?>
		<!-- CHAT REQUISICIONES -->
		<div class="position-fixed w-100 d-flex justify-content-end" style="bottom: 0; right:0;">
			<div class="card card-primary direct-chat direct-chat-primary mx-2 collapsed-card">
				<div class="card-header">
					<h3 class="card-title">Chat Directo</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div id="error-message-container"></div>
					<div 
					class="direct-chat-messages"
					style="height: 300px;width: 370px;"
					id="direct-chat-messages"
					>
					</div>
				</div>
				<div class="card-footer">
					<div class="input-group">
						<input type="hidden" name="idRequisicion" id="idRequisicion" value=<?php echo $requisicion->id; ?> >
						<input type="text" name="mensaje" id="mensaje" placeholder="Escribe un mensaje ..." class="form-control">
						<span class="input-group-append">
							<button type="button" class="btn btn-primary" id="btnCrearMensaje">Enviar</button>
						</span>
					</div>
					<div id="mensaje-peticion"></div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- Modal -->
	<div class="modal fade" id="modalVerImagenes" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalVerImagenesLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVerImagenesLabel">Fotos de la Partida <span></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row row-cols-1 row-cols-lg-2 imagenes">
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

	<!-- MODAL DOCUMENTOS -->
	

	<!-- Modal -->
	<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Visualizador de PDF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
					<iframe id="pdfViewer" src="" width="100%" height="600px"></iframe>
                </div>
            </div>
        </div>
    </div>

</div>

	<!-- Modal para crear cotización -->
	<div class="modal fade" id="crearCotizacionModal" role="dialog" aria-labelledby="crearCotizacionModalLabel" >
		<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="crearCotizacionModalLabel">Crear Cotización</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- Formulario o contenido del modal -->
					<form action="<?=Route::routes('requisiciones.crear-cotizacion', $requisicion->id)?>" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="_token" value="<?=token()?>">
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="proveedorId">Nombre del Proveedor</label>
								<select class="form-control select2" id="proveedorId" name="proveedorId" required>
									<option value="" selected>Selecciona un proveedor</option>
									<?php foreach ($proveedores as $proveedor): ?>
										<option value="<?= $proveedor["id"] ?>"><?= $proveedor["proveedor"] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="vendedorId">Nombre del Vendedor</label>
								<select class="form-control select2" id="vendedorId" name="vendedorId">
									<!-- Las opciones se llenarán mediante AJAX -->
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="fechaLimite">Fecha Límite</label>
								<div class="input-group date2" id="fechaLimite" data-target-input="nearest">
									<input type="text" name="fechaLimite" id="fechaLimite" value="" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaLimite">
									<div class="input-group-append" data-target="#fechaLimite" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
									</div>
								</div>
							</div>
							<!-- <div class="col form-group">
								<label for="soporteArchivo">Adjuntar Soporte (opcional)</label>
								<input type="file" class="form-control-file" name="soporteArchivo[]" accept=".pdf" multiple>
								<small class="form-text text-muted">Puedes adjuntar un archivo de soporte (PDF).</small>
							</div> -->
							<div class="col-12 form-group">
								<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisicionDetalles" width="100%">
	
									<thead>
										<tr>
											<th class="text-right" style="min-width: 80px;">Partida</th>
											<th class="text-right">Cant.</th>
											<th>Unidad</th>
											<th style="min-width: 140px;">Costo</th>
											<th style="min-width: 320px;">Concepto</th>
											<th style="min-width: 320px;">Numero de Parte</th>
										</tr>
									</thead>
	
									<tbody class="text-uppercase">
										<?php if ( isset($requisicion->id) ) : ?>
										<?php foreach($requisicion->detalles as $key=>$detalle) : ?>
										<tr>
											<td partida class="text-right">
												<input type="checkbox" name="partidaSeleccionada[]" value="<?php echo $detalle['id']; ?>" checked>
											</td>
											<td class="text-right"><?php echo floatval($detalle['cantidad']); ?></td>
											<td><?php echo fString($detalle['unidad']); ?></td>
											<td><?php echo formatMoney($detalle['costo']); ?></td>
											<td><?php echo $detalle['concepto']; ?></td>
											<td><?php echo $detalle['numeroParte']; ?></td>
										</tr>
										<?php endforeach; ?>
										<?php endif; ?>
									</tbody>
	
								</table>
							</div>
						</div>
						<button type="submit" class="btn btn-primary">Guardar</button>
					</form>
				</div>
			</div>
		</div>
	</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/requisiciones.js?v=1.2.5');
	array_push($arrayArchivosJS, 'vistas/js/mensaje-requisicion.js?v=2.5');
?>



