<?php

	if (isset($inventario->id)) {

		$ordenCompraId = isset($old["ordenCompra"]) ? $old["ordenCompra"] : $inventario->ordenCompra;
		$almacenId = isset($old["almacenId"]) ? $old["almacenId"] : $inventario->almacenId;
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $inventario->observaciones;
		$entrego = isset($old["entrego"]) ? $old["entrega"] : $inventario->entrego;
		$inventarioId = $inventario->id;
		$fechaEntrega = $inventario->fechaEntrega;
		$firma=$inventario->firma;
		$folio = $requisicion->folio;
		$requisicionId = isset($id) ? $id : $requisicion->id;
		$ordenCompraFolio = $ordenCompra->folio;

	} else {
		
		$requisicionId = isset($id) ? $id : 0;
		$ordenCompraId = isset($old["ordenCompra"]) ? $old["ordenCompra"] : $ordenCompra->id;
		$almacenId = isset($old["almacenId"]) ? $old["almacenId"] : '';
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : '';
		$entrego = isset($old["entrego"]) ? $old["entrego"] : '';
		$fechaEntrega = fFechaLarga(date('Y-m-d'));
		$inventarioId = 0;
		$folio = $requisicion->folio;
		$ordenCompraFolio = $ordenCompra->folio;
	}



?>

<input type="hidden" name="_token" id="token" value="<?php echo token(); ?>">
<input type="hidden" id="requisicionId" value="<?= $requisicionId ?>">
<input type="hidden" id="entradaId" value="<?= $inventario->id ?>">
<input type="hidden" id="inventarioId" value="<?= $inventarioId ?>">
<input type="hidden" id="almacenId" value="<?= $almacenId ?>">

<input type="hidden" id="firma" name="firma">

<div class="row">

	<div class="col-md-6">
		
		<div class="card card-warning card-outline">

			<div class="card-body">
				<div class="alert alert-danger error-validacion mb-2 d-none">
					<ul class="mb-0">
						<!-- <li></li> -->
					</ul>
				</div>
				<div class="row">
					<!-- Requisicion -->
					<div class="col-md-6 form-group">

						<label for="requisicion">Requisicion:</label>
						<input type="text" disabled name="requisicion" class="form-control form-control-sm text-uppercase" value="<?php echo $folio ?>" >
					</div>

					<!-- OC -->
					<div class="col-md-6 form-group">

						<label for="ordenCompra">Orden de Compra:</label>
						<input type="hidden" name="ordenCompra" value="<?php echo $ordenCompraId ?>">
						<input type="text" class="form-control form-control-sm text-uppercase"  disabled value="<?php echo $ordenCompraFolio ?>">

					</div>

					<!-- Almacen -->
					<div class="col-md-6 form-group">

						<label for="almacen">Almacen:</label>
						<select <?php if(isset($inventario->id)) echo 'disabled' ?> name="almacenId" id="almacenId" class="custom-select form-controls select2">
							<option value="">Seleccione una almacen</option>
							<?php foreach($almacenes as $almacen) { ?>
							<option value="<?php echo $almacen["id"]; ?>"
								<?php echo $almacenId == $almacen["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($almacen["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>
					
					<!-- Observaciones -->
					<div class="col-md-6 form-group">
				
						<label for="observaciones">Observaciones:</label>
						<input type="text" <?php if(isset($inventario->id)) echo 'disabled' ?> name="observaciones" class="form-control form-control-sm text-uppercase" value="<?php echo $observaciones ?>" placeholder="ingrese las observaciones">
				
					</div>

					<!-- Entrego -->
					<div class="col-md-6 form-group">

						<label for="entrega">Entrego</label>
						<input type="text" <?php if(isset($inventario->id)) echo 'disabled' ?> name="entrego" class="form-control form-control-sm text-uppercase"value="<?php echo $entrego ?>" placeholder="Ingrese el nombre que entrega">

					</div>

					<!-- Fecha de Entrega -->
					<div class="col-md-6 form-group">
						<label for="fechaEntrega">Fecha de Entrega</label>
						<div class="input-group date" id="fechaEntregaDTP" data-target-input="nearest">
							<input type="text" name="fechaEntrega" id="fechaEntrega" value="<?php echo $fechaEntrega; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de entrega" data-target="#fechaEntregaDTP" <?php  if(isset($inventario->id)) echo 'disabled' ?>>
							
							<div class="input-group-append" data-target="#fechaEntregaDTP" data-toggle="datetimepicker">
								<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
							</div>
						</div>
					</div>

				</div>			

			</div><!-- <div class="card-body"> -->

		</div><!-- <div class="card card-warning card-outline"> -->
	
	</div><!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<?php if(isset($inventario->id)) : ?>
			
			<div class="card card-info card-outline">

				<div class="card-header">
					<h5 class="card-title m-0">Salidas del Inventario</h5>
				</div>

				<div class="card-body">

					<button type="button" class="btn btn-outline-primary float-right mb-2" data-toggle="modal" data-target="#modalCrearSalida"> Generar Salida </button>			
					<div class="table-responsive">
			
						<table class="table table-sm table-bordered table-striped mb-0 tablaDetalle" id="tablaSalidas" width="100%">
							<thead>
								<tr>
									<th class="text-right" >#</th>									
									<th >Folio</th>
									<th >Fecha Salida</th>
									<th>Entregó</th>
									<th>Estatus</th>

									<th style="width: 10px;">Acciones</th>
								</tr>
							</thead>
			
							<tbody class="text-uppercase">
								

							</tbody>
			
						</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaSalidas" width="100%"> -->
			
					</div> <!-- <div class="table-responsive"> -->
			
				</div> <!-- <div class="card-body"> -->
			
			</div> <!-- <div class="card card-info card-outline"> -->
			
		<?php else : ?>
			<div class="row">
				<?php if($id ==null) : ?>
					<input type="hidden" name="directo" id="directo">
					<input type="hidden" name="indirecto" id="indirecto">

					<div class="col-md-6 form-group">
						<label for="descripcion">Descripcion:</label>
						<input type="text" name="descripcion" id="descripcion"  class="form-control form-control-sm text-uppercase">
					</div>
					<div class="col-md-6 form-group">
						<label for="unidad">Unidad:</label>
						<input type="text" name="unidad" id="unidad"  class="form-control form-control-sm text-uppercase">
					</div>
					<div class="col-md-6 form-group">
						<label for="cantidad">Cantidad</label>
						<input type="number" id="cantidad" name="cantidad" class="form-control form-control-sm text-uppercase">
					</div>
					<div class="col-md-6 form-group">
						<label for="costo_unitario">Costo Unitario</label>
						<input type="number" id="costo_unitario" name="costo_unitario" class="form-control form-control-sm text-uppercase">
					</div>
					<div class="col-md-6 form-group">
						<label for="numParte">Num. de Parte</label>
						<input type="text" name="numParte" id ="numeroParte" value="NA" class="form-control form-control-sm text-uppercase">
					</div>
				<?php endif ?>
				<!-- Button trigger modal -->
				<div class="col-12 form-group">
					<?php if($id ==null) : ?>	
						<button type="button" id="btnAgregarPartida" class="btn btn-outline-info">
							<i class="fas fa-plus"></i> Añadir partida
						</button>
					<?php endif ?>
					<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#firmaModal">
						Firmar
					</button>
				</div>
			</div>
		<?php endif ?>
	</div><!-- <div class="col-md-6"> -->

	<div class="card card-success card-outline col-12">

		<div class="card-body">

			<div class="table-responsive">
				<input type="file" id="archivoSubir" style="display: none;" multiple>
				<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisicionDetalles" width="100%">

					<thead>
						<tr>
							<?php if($inventarioId) : ?>
								<th  style="width: 10px;">#</th>
							<?php endif ?>
							<?php if(!isset($inventario->id) && $requisicionId == 0 ) : ?>
								<th  style="width: 10px;"></th>
							<?php endif ?>
							<th class="" style="min	-width: 80px;">Partida</th>
							<th class="">Cant.</th>
							<?php if($inventarioId):?>
								<th class="">Cant. Disponible</th>
							<?php endif; ?>
							<th class="">Costo Unitario</th>
							<th>Unidad</th>
							<th style="min-width: 160px;">Num. de Parte</th>
							<th style="min-width: 320px;">Concepto</th>
							<?php if($inventarioId):?>
								<th class="">Acciones</th>
							<?php endif; ?>

						</tr>
					</thead>

					<tbody class="text-uppercase">

					</tbody>

				</table>

			</div>

		</div> <!-- <div class="card-body"> -->

	</div> <!-- <div class="card card-info card-outline"> -->

</div>