<?php
	$old = old();

	$cantidadComprobantes = count($comprobacionGasto->comprobantesPago);
	$cantidadSoportes = count($comprobacionGasto->soportes);

	$cantidadDocs = $cantidadComprobantes+$cantidadSoportes;

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
	          <h1>Gastos a comprobar <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('comprobacion-gastos.index')?>"> <i class="fas fa-tools"></i> Gastos a comprobar</a></li>
	            <li class="breadcrumb-item active">Editar gasto a comprobar</li>
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
								Editar gasto a comprobar
							</h3>
						</div> <!-- <div class="card-header"> -->
						<div class="card-body">
							<?php include "vistas/modulos/errores/form-messages.php"; ?>
							<form id="formSend" method="POST" action="<?php echo Route::names('comprobacion-gastos.update', $comprobacionGasto->id); ?>" enctype="multipart/form-data">
								<input type="hidden" name="_method" value="PUT">
								<?php 
									include "vistas/modulos/comprobacion-gasto/formulario.php";
								?>

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


<?php
	array_push($arrayArchivosJS, 'vistas/js/comprobacion-gastos.js?v=1.0');
?>



