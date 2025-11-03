<?php
    if (isset($resguardo->id)) {
        $usuarioEntregoId = isset($old["usuarioEntregoId"]) ? $old["usuarioEntregoId"] : $resguardo->usuarioEntregoId;

        $usuarioRecibioId = isset($old["usuarioRecibioId"]) ? $old["usuarioRecibioId"] : $resguardo->usuarioRecibioId;
        $almacenId = isset($old["almacenId"]) ? $old["almacenId"] : $resguardo->almacenId;
        $fechaEntrego = isset($old["fechaEntrego"]) ? $old["fechaEntrego"] : $resguardo->fechaEntrego;
        $observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $resguardo->observaciones;
        $salidaId = isset($old["salidaId"]) ? $old["salidaId"] : $resguardo->salidaId;


    } else {
        $usuarioEntrego = isset($old["usuarioEntrego"]) ? $old["usuarioEntrego"] : "";

        $usuarioRecibio = isset($old["usuarioRecibio"]) ? $old["usuarioRecibio"] : "";
        $almacenId = isset($old["obra"]) ? $old["obra"] : "";
        $fechaAsignacion = isset($old["fechaAsignacion"]) ? $old["fechaAsignacion"] : "";
        $inventario = isset($old["inventario"]) ? $old["inventario"] : "";
        $observaciones = isset($old["observaciones"]) ? $old["observaciones"] : "";
        $salidaId = isset($old["salidaId"]) ? $old["salidaId"] : "";

    }

?>
<input type="hidden" name="_token" value="<?php echo token(); ?>">
<input type="hidden" id="resguardoId" value="<?php echo $resguardo->id ?>" name="resguardoId" >
<input type="hidden" id="firma" name="firma">

<div class="row">
    
    <!-- FORMULARIO DE RESGUAROS -->
    <div class="col-md-5">
        <div class="card card-warning card-outline">
            <div class="card-body">
                <div class="alert alert-danger error-validacion mb-2 d-none">
                    <ul class="mb-0">
                        <!-- <li></li> -->
                    </ul>
                </div>

                <div class="row">
                    <!-- Usuario que recibe -->
                    <div class="col-md-6 form-group">
                        <label for="usuarioRecibio">Usuario que recibe:</label>
                        <select <?php if(isset($resguardo->id)) echo 'disabled' ?> name="usuarioRecibio" id="usuarioRecibio" class="custom-select form-controls form-control-sms select2" style="width: 100%">
                            <option value="">Selecciona una empleado</option>
                            <?php foreach($usuarios as $usuario) { ?>
                            <option value="<?php echo $usuario["id"]; ?>"
                                <?php echo $usuarioRecibioId == $usuario["id"] ? ' selected' : ''; ?>
                                ><?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Almacen -->
                    <div class="col-md-6 form-group">

                        <label for="obra">Almacen:</label>
                        <select <?php if(isset($resguardo->id)) echo 'disabled' ?> name="obra" id="obra" class="custom-select form-controls form-control-sms select2" style="width: 100%">
                            <option value="">Selecciona una almacen</option>
                            <?php foreach($almacenes as $almacen) { ?>
                            <option value="<?php echo $almacen["id"]; ?>"
                                <?php echo $almacenId == $almacen["id"] ? ' selected' : ''; ?>>
                                <?php echo mb_strtoupper(fString($almacen["descripcion"])); ?>
                            </option>
                            <?php } ?>
                        </select>

                    </div>

                    <!-- Fecha -->
                    <div class="col-md-6 form-group">
                        <label for="fechaAsignacion">Fecha de Entregado:</label>

                        <input class="form-control form-control-sm" 
								type="datetime-local" 
								id="fecha" 
                                <?php if(isset($resguardo->id) || $id==null ) echo 'disabled' ?>
								name="fecha" 
								value="<?php echo $fechaEntrego; ?>"
								placeholder="Ingresa la fecha" 
								required>
                    </div>

                    <!-- Folio de salida -->
                    <div class="col-md-6 form-group">
                        <label for="salidaId">Folio de salida:</label>
						<input type="text" <?php if(isset($resguardo->id) || $id==null ) echo 'disabled' ?> name="salidaId" id="salidaId" class="form-control form-control-sm text-uppercase" value="<?php echo $salidaId ?>" placeholder="">
                    </div>

                    <!-- Observaciones -->
                    <div class="col-12 form-group">
                        <label for="observaciones">Observaciones:</label>
                        <textarea name="observaciones" id="observaciones" class="form-control form-control-sm text-uppercase"><?php echo $observaciones?></textarea>
                    </div>


                </div>			
            </div><!-- <div class="card-body"> -->
        </div><!-- <div class="card card-warning card-outline"> -->
    </div>

	<div class="col-md-7">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
					Transferencias De Resguardo
				</h3>
            </div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-sm table-bordered table-striped mb-0 "id="tablaTransferencias" width="100%">
						<thead>
							<tr>
								<th>Usuario Recibio</th>
								<th>Usuario Entrego</th>
								<th>Fecha Entrego</th>
								<th>Resguardo Original</th>
								<th>Resguardo Nuevo</th>
								<th>Concepto</th>
								<th>Cantidad</th>

							</tr>
						</thead>
						<tbody class="text-uppercase">
						</tbody>
					</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaSalidas" width="100%"> -->
				</div> <!-- <div class="table-responsive"> -->
			</div> <!-- <div class="card-body"> -->
		</div> <!-- <div class="card card-info card-outline"> -->	                   
    </div>
    <!-- TABLA PARTIDAS RESGUARDOS -->
	<div class="col-md-12">
		<?php if(isset($resguardo->id)) : ?>
			<div class="card card-info card-outline">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-striped mb-0 " id="tablaResguardoPartida" width="100%">
							<thead>
								<tr>
									<th>Id</th>
									<th >Concepto</th>
									<th >Cantidad</th>
									<th >Unidad</th>
									<th >Numero Parte</th>
									<th >Partida</th>
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
	</div>


</div>


