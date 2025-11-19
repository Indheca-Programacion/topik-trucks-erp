<?php
	$old = old();

	$cantidadComprobantes = count($requisicion->comprobantesPago);
	// $cantidadOrdenes = count($requisicion->ordenesCompra);
	// $cantidadFacturas = count($requisicion->facturas);
	// $cantidadCotizaciones = count($requisicion->cotizaciones);
	// $cantidadVales = count($requisicion->valesAlmacen);

	// $cantidadDocs = $cantidadComprobantes+$cantidadOrdenes+$cantidadFacturas+$cantidadCotizaciones+$cantidadVales;

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Requisiciones <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('requisicion-gastos.index')?>"> <i class="fas fa-tools"></i> Requisiciones</a></li>
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
						<form id="formSend" method="POST" action="<?php echo Route::names('requisicion-gastos.update', $requisicion->id); ?>" enctype="multipart/form-data">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/requisicion-gastos/formulario.php"; ?>
							<?php if ( $formularioEditable ) : ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar
							</button>
							<?php else: ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary cargar-facturas d-none" disabled>
								<i class="fas fa-save"></i> Actualizar
							</button>
							<?php endif; ?>

							<div class="btn-group descargar-archivos">
								<button type="button" class="btn btn-outline-info" <?php if ( $cantidadComprobantes == 0 ) echo "disabled"; ?>>
									<i class="fas fa-download"></i> Descargar
								</button>
								<button type="button" class="btn btn-outline-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false" <?php if ( $cantidadComprobantes == 0  ) echo "disabled"; ?>>
									<span class="sr-only">Alternar Menú Desplegable</span>
								</button>
								<div class="dropdown-menu">
									<a class="dropdown-item <?php if ( $cantidadComprobantes == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarComprobantes">Comprobantes de Pago</a>
								
								</div>
							</div>

							<a href="<?php echo Route::names('requisicion-gastos.print', $requisicion->id); ?>" target="_blank" class="btn btn-info float-right"><i class="fas fa-print"></i> Imprimir</a>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

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

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/requisicion-gastos.js?v=1.21');
?>
