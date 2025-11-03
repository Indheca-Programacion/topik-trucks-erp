<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Resguardos <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('resguardos.index')?>"> <i class="fas fa-list-alt"></i> Resguardos</a></li>
	            <li class="breadcrumb-item active">Editar unidad</li>
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
			<div class="col-12">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Editar resguardo
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('resguardos.update', $resguardo->id); ?>" enctype="multipart/form-data" >
							<input type="hidden" id="resguardoId" value="<?= $resguardo->id ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/resguardos/formulario.php"; ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar
							</button>
							<a   href="<?=Route::names('resguardos.print', $resguardo->id) ?>" class="btn btn-outline-success">
								<i class="fas fa-save"></i> Imprimir
							</a>
							<?php if ($activarTransferencia) : ?>
								<button type="button" id="btnTransferirResguardo" class="btn btn-outline-dark">
									<i class="fas fa-save"></i> Transferir
								</button>
							<?php endif; ?>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

</div>

<!-- TRANSFERENCIA -->
<div class="modal fade" id="transferenciaModal" tabindex="-1" role="dialog" aria-labelledby="transferenciaModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">

			<div class="modal-header bg-info text-white">
				<h5 class="modal-title" id="transferenciaModalLabel">Trasferencia</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body row">
				<div class="table-responsive">
					<table class="table table-bordered table-striped" 
							id="tablaPartidasResguardosTransferencias" 
							style="width: 100%;">
						<thead>
						<tr>
							<th>#</th>
							<th>Id</th>
							<th>Concepto</th>
							<th>Cantidad</th>
							<th>Unidad</th>
							<th>Numero Parte</th>
							<th>Partida</th>
						</tr>
						</thead>
						<tbody class="text-uppercase">
						</tbody>
					</table>
				</div>

                <!-- Usuario que recibe -->
                <div class="col-md-6 form-group">
                    <label for="usuarioRecibioTransferencia">Usuario que recibe:</label>
                    <select name="usuarioRecibioTransferencia" id="usuarioRecibioTransferencia" class="custom-select form-controls form-control-sms select2" style="width: 100%">
                        <option value="">Selecciona una empleado</option>
                        <?php foreach($usuarios as $usuario) { ?>
							<option value="<?php echo $usuario["id"]; ?>">
								<?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
							</option>
                        <?php } ?>
                    </select>
                </div>

				<!-- Fecha -->
                <div class="col-md-6 form-group">
                    <label for="fechaTransferencia">Fecha de Entregado:</label>

                    <input class="form-control form-control-sm" 
							type="datetime-local" 
							id="fechaTransferencia" 
							name="fechaTransferencia" 
							value=""
							placeholder="Ingresa la fecha" 
							required>
                </div>
				
				<div class="row">
					<div class="col-12">							
						<div class="text-center" role="alert">
							<strong>Nota:</strong> Para firmar, dibuje su firma en el recuadro de abajo.
						</div> <!-- <div class="" role="alert"> -->
					</div>
					<div class="col-md-12 text-center">
						<canvas class="border" id="canvas" ></canvas>
					</div>
					<div class="col-md-12 form-group d-flex justify-content-center">
						<button id="btnLimpiar" type="button" class="btn btn-outline-info"><i class="fas fa-broom"></i>Limpiar</button>
					</div>
				</div>
			</div>

			<div class="modal-footer d-flex justify-content-between align-items-center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cerrar
				</button>
				<button type="button" id="enviarTransferencia" class="btn btn-outline-success">
					<i class="fas fa-save"></i> Aceptar
				</button>
			</div>

		</div>
	</div>
</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/resguardos.js?v=1.2');
?>
