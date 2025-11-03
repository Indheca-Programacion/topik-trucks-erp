<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Generador <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('generadores.index')?>"> <i class="fas fa-list-alt"></i> Generadores</a></li>
	            <li class="breadcrumb-item active">Editar Generador</li>
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
					<div class="card-header p-0 border-bottom-0">
						<ul class="nav nav-tabs" id="tabServicio" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="orden-trabajo-tab" data-toggle="pill" href="#orden-trabajo" role="tab" aria-controls="orden-trabajo" aria-selected="true">Detalles de Generador</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="requisiciones-tab" data-toggle="pill" href="#requisiciones" role="tab" aria-controls="requisiciones" aria-selected="false">Detalles de incidencias</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="estimaciones-tab" data-toggle="pill" href="#estimaciones" role="tab" aria-controls="estimaciones" aria-selected="false">Estimaciones</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="desemepeno-tab" data-toggle="pill" href="#desemepeno" role="tab" aria-controls="desemepeno" aria-selected="false">Desempeño de maquinaria</a>
							</li>
						</ul>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<div class="tab-content" id="tabServicioContent">
							<div class="tab-pane fade show active" id="orden-trabajo" role="tabpanel" aria-labelledby="orden-trabajo-tab">
								<div class="row">
									<div class="col-6">
										<?php include "vistas/modulos/generadores/formulario.php"; ?>
										<div class="row">
											<div class="col-md-6 form-group align-self-start">
												<a href="<?php echo Route::names('generadores.print', $id); ?>" target="_blank" class="btn btn-info"><i class="fas fa-print"></i> Generador</a>
												<a href='<?=Route::names('estimaciones.print',$id)?>' target='_blank' class='btn btn-info'><i class='fas fa-print'></i> Estimaciones</a>							
											</div>
											<?php if ($editarDetallesGenerador) : ?>
											<div class="col-md-6 form-group align-self-end">
												<button type="button" class="btn btn-outline-primary " data-toggle="modal" data-target="#modalAñadirEquipo">Añadir Equipo</button>
												<button type="button" class="btn btn-outline-primary " data-toggle="modal" data-target="#modalAgregarIncidencia">Registrar Incidencia</button>
											</div>
											<?php endif; ?>
											<div class="col form-group">
												<?php if (!isset($generador->firmado) && $autorizarGenerador ) : ?>
													<button type="button" class="btn btn-outline-primary" id="btnFirmar">
														<i class="fas fa-file-signature"></i> Autorizar
													</button>
												<?php endif; ?>
												<?php if (!isset($generador->generadorSupervisorFirma) && $autorizarEstimacionesSuperviso ) : ?>
													<button type="button" class="btn btn-outline-primary" id="btnAutorizarSupervisor">
														<i class="fas fa-file-signature"></i> Autorizar Supervisor
													</button>
												<?php endif; ?>

											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-hover" id="tablaResumen">
												<thead>
													<tr>
														<td>Numero Economico</td>
														<td>Total Dias</td>
														<td style="background-color:#00913f;color:white;">Dias Efectivos</td>
														<td style="background-color:#FF0000;color:white;">Fallas Mecanicas</td>
														<td style="background-color:#FFD300;">Paros Operativos</td>
														<td style="background-color:#572364;color:white;">Clima</td>
														<td style="background-color:#FF8000;color:white;">Dia Parcial</td>
														<td>% D.M.</td>
													</tr>
												</thead>
												<tbody>
													
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="table-responsive">
										<table class="table table-bordered table-striped table-hover" id="tablaMaquinarias">
											<thead>
												<tr>
													<th>#</th>
													<th>Numero Economico</th>
													<th><div style="width: 160px;">Equipos</div></th>
													<th>Marca</th>
													<th>Modelo</th>
													<th>Serie</th>
													<th>Fecha Inicio</th>
													<?php for ($i = 0; $i < $numDias; $i++) { ?>
														<th style="width: 10px;"><?php echo ($i + 1); ?></th>
													<?php } ?>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="requisiciones" role="tabpanel" aria-labelledby="requisiciones-tab">
								<div class="row">
									<div class="col-12">
										<button type="button" class="btn btn-outline-primary float-right mx-2" data-toggle="modal" data-target="#modalAgregarObservacion">Añadir Observacion</button>
									</div>
									<div class="col mt-2">
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-hover" id="tablaIncidencias">
												<thead>
													<tr>
														<th><div style="width: 60;">Maquinaria</div></th>
														<th>Fecha Inicio</th>
														<th>Fecha Fin</th>
														<th>Observaciones</th>
														<th>Acciones</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="estimaciones" role="tabpanel"  aria-labelledby="estimaciones-tab">
								<div class="row">
									<div class="col-12 form-group align-self-end">
										<?php if (!isset($generador->estimacionFirma) && $autorizarEstimacion ) : ?>
											<button type="button" class="btn btn-outline-primary" id="btnAutorizarEstimacion">
												<i class="fas fa-file-signature"></i> Autorizar Gerente
											</button>
										<?php endif; ?>
										<?php if (!isset($generador->estimacionSupervisorFirma) && $autorizarEstimacionesSuperviso ) : ?>
											<button type="button" class="btn btn-outline-primary" id="btnAutorizarEstimacionesSupervisor">
												<i class="fas fa-file-signature"></i> Autorizar Supervisor
											</button>
										<?php endif; ?>
										<a href='<?=Route::names('estimaciones.print',$id)?>' target='_blank' class='btn btn-info '><i class='fas fa-print'></i> Imprimir</a>							
									</div>
									<div class="col">
										<table class="table table-bordered table-striped" id="tablaEstimaciones">
											<thead>
												<tr>
													<td>Numero Economico</td>
													<td>Descripcion</td>
													<td>Total Dias</td>
													<td>P.U. por 30 días</td>
													<td>Costo del mes</td>
													<td>$ Operacion</td>
													<td>$ Comb.</td>
													<td>$ Mantto.</td>
													<td>$ Flete</td>
													<td>Ajuste</td>
													<td>Importe</td>
												</tr>
											</thead>
											<tbody>
												<?php
													foreach ($arrayEstimaciones as $registro) {
														if ($editarEstimaciones) {
															$disabled = '';
														} else {
															$disabled = 'disabled';
														}
														echo '<tr>';
														echo '<td class="partida" detalle_id="'.$registro["id"].'">' . $registro['numeroEconomico'] . '</td>';
														echo '<td>' . $registro['descripcion'] . '</td>';
														echo '<td><span class="dias">' . $registro['totalDias'] . '</span></td>';
														echo '<td><input '.$disabled.' name="detalles[costo][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['costo'] . '"></td>';
														echo '<td><span class="pu">' . $registro['pu'] . '</span></td>';
														echo '<td><input '.$disabled.' name="detalles[operacion][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['operacion'] . '"></td>';
														echo '<td><input '.$disabled.' name="detalles[comb][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['comb'] . '"></td>';
														echo '<td><input '.$disabled.' name="detalles[mantto][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['mantto'] . '"></td>';
														echo '<td><input '.$disabled.' name="detalles[flete][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['flete'] . '"></td>';
														echo '<td><input '.$disabled.' name="detalles[ajuste][]" class="form-control form-control-sm " type="number" value="' . $registro['ajuste'] . '"></td>';
														echo '<td>$ <span class="importe">'.$registro['importe'].'</span></td>';
														echo '</tr>';
													}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="desemepeno" role="tabpanel"  aria-labelledby="desemepeno-tab">
								<div class="row">
									<div class="col-12 mb-2">
										<a href='<?=Route::names('desempeno.print',$id)?>' target='_blank' class='btn btn-info float-right'><i class='fas fa-print'></i> Imprimir</a>							
									</div>
									<div class="col">
										<table class="table table-bordered table-striped" id="tablaDesempeno">
											<thead>
												<tr>
													<td>Numero Economico</td>
													<td>Total Dias</td>
													<td>Horas Operativas Disponibles</td>
													<td>Horas Motor Registradas</td>
													<td>Ralenti Registradas</td>
													<td>LTS Comb consumidos</td>
													<td>Rendimiento</td>
													<td>% Aprovechamiento</td>
													<td>Observaciones</td>
												</tr>
											</thead>
											<tbody>
												<?php
													foreach ($arrayDesempeño as $registro) {
														echo '<tr>';
														echo '<td class="partida" detalle_id="'.$registro["id"].'">' . $registro['numeroEconomico'] . '</td>';
														echo '<td><span class="dias">' . $registro['totalDias'] . '</span></td>';
														echo '<td><span class="hod">' . $registro['hod'] . '</span></td>';
														echo '<td><input name="detalles[hmr][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['hmr'] . '"></td>';
														echo '<td><input name="detalles[rr][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['rr'] . '"></td>';
														echo '<td><input name="detalles[lcc][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['lcc'] . '"></td>';
														echo '<td><span class="rendimiento">' . $registro['rendimiento'] . ' %</span></td>';
														echo '<td><span class="aprovechamiento">' . $registro['aprovechamiento'] . ' %</span></td>';
														echo '<td><input name="detalles[observaciones][]" class="form-control form-control-sm text-uppercase" type="text" value="' . $registro['observaciones'] . '"</td>';
														echo '</tr>';
													}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
			
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

</div>

<!-- modalAñadirEquipo -->
<div class="modal fade" id="modalAñadirEquipo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalAñadirEquipoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Añadir Equipo</h5>
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
		<form  id="formAñadirEquipo">
			<div class="row">
				<input type="hidden" id="generadorId" name="generadorId" value="<?= $generador->id ?>">
				<input type="hidden" name="fk_maquinaria" id="modalAñadirEquipo_maquinariaId">
				<div class="col-md-6 form-group">
					<label for="modalAñadirEquipo_numeroId">Numero:</label>
					<div class="input-group">
						<input type="text" disabled name="numeroId" id="modalAñadirEquipo_numeroId" class="form-control form-control-sm text-uppercase">
						<div class="input-group-append">
							<button type="button" id="btnBuscarNumero" class="btn btn-sm btn-outline-primary" title="Buscar Numero">
								<i class="fas fa-search"></i>
							</button>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<label for="">Equipo</label>
					<input type="text" disabled id="modalAñadirEquipo_Equipo" class="form-control form-control-sm text-uppercase">
				</div>
				<div class="col-md-6">
					<label for="">Marca</label>
					<input type="text" disabled id="modalAñadirEquipo_Marca" class="form-control form-control-sm text-uppercase">
				</div>
				<div class="col-md-6">
					<label for="">Serie</label>
					<input type="text" disabled id="modalAñadirEquipo_Serie" class="form-control form-control-sm text-uppercase">
				</div>
			</div>
			<div class="row mt-2">
				<div class="col-md-6">
					<label for="fecha">Fecha de Inicio:</label>
					<div class="input-group fecha" id="fechaInicio" data-target-input="nearest">
						<input type="date" name="fechaInicio" id="fecha" value="" class="form-control form-control-sm" placeholder="Ingresa la fecha">
					</div>
				</div>
			</div>
		</form>
	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary btnAgregar">Añadir</button>
      </div>
    </div>
  </div>
</div>
<!-- modalAgregarIncidencia -->
<div class="modal fade" id="modalAgregarIncidencia" tabindex="-1" role="dialog" aria-labelledby="modalAgregarIncidenciaLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    	<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalAgregarIncidenciaLongTitle">Agregar Incidencia</h5>
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
				<div class="row">
					<!-- Numero Economico -->
					<div class="col-md-6">
						<label for="">Numero Económico:</label>
						<select class="select2" name="economico[]" id="modalAgregarIncidencia_numero"  multiple="multiple">
								<?php foreach($arrayGeneradores as $generador) { ?>
									<option value="<?php echo $generador["maquinariaId"]; ?>"
										><?php echo mb_strtoupper(fString($generador["numeroEconomico"])); ?>
									</option>
								<?php } ?>
						</select>
					</div>
					<!-- Incidencia -->
					<div class="col-md-6 form-group">
						<label for="">Incidencia:</label>
						<select class="select2" id="modalAgregarIncidencia_incidencia">
							<option value="0">Seleccione una incidencia</option>
							<option value="1">Laborados</option>
							<option value="2">Falla Mecanica</option>
							<option value="3">Paros Operativos</option>
							<option value="4">Dia Parcial</option>
							<option value="7">Clima</option>
							<option value="-1">Vacio</option>
						</select>
					</div>
					<!-- Fecha Desde -->
					<div class="col-md-6 form-group">
						<label for="fechaInicio">Desde:</label>
						<div class="input-group date" id="fechaInit" data-target-input="nearest">
							<input type="text" name="fecha" id="modalAgregarIncidencia_fechaInicio" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaInit">
							<div class="input-group-append" data-target="#fechaInit" data-toggle="datetimepicker">
								<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
							</div>
						</div>
					</div>
					<!-- Fecha Hasta -->
					<div class="col-md-6 form-group">
						<label for="fechaFin">Hasta:</label>
						<div class="input-group date2" id="fechaEnd" data-target-input="nearest">
							<input type="text" name="fecha" id="modalAgregarIncidencia_fechaFin" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaEnd">
							<div class="input-group-append" data-target="#fechaEnd" data-toggle="datetimepicker">
								<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
							</div>
						</div>
					</div>
					<!-- Observaciones -->
					<div class="col-12 form-group d-none" id="modalAgregarIncidencia_observacion">
						<label for="observaciones">Observaciones</label>
						<textarea name="observaciones" id="modalAgregarIncidencia_observacion_input" class="form-control"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			<button type="button" class="btn btn-primary btnAgregarIncidencia">Agregar</button>
			</div>
    	</div>
	</div>
</div>
<!-- Modal Seleccionar maquina -->
<div class="modal fade" id="modalBuscarMaquina" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalBuscarMaquinaLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<!-- <div class="modal-content" style="min-height: 30rem;"> -->
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalBuscarInsumoLabel"><i class="fas fa-search"></i> Buscar Maquinaria</h5>
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

				<table class="table table-sm table-bordered" id="tablaSeleccionarMaquina" width="100%">
					<thead>
						<tr>
							<th style="width:10px">#</th>
							<th>Empresa</th>
							<th>Tipo de Maquinaria</th>
							<th>Num. Económico</th>
							<th>Num. Factura</th>
							<th>Descripcion</th>
							<th>Marca</th>
							<th>Modelo</th>
							<th>Año</th>
							<th>Serie</th>
							<th>Color</th>
							<th>Ubicación</th>
							<th>Almacén</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<!-- <button type="button" class="btn btn-outline-primary btnSeleccionar">
					<i class="fas fa-check"></i> Seleccionar
				</button> -->
			</div>
		</div>
	</div>
</div>
<!-- Modal Agregar Observaciones -->
<div class="modal fade" id="modalAgregarObservacion" tabindex="-1" role="dialog" aria-labelledby="modalAgregarObservacionLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalBuscarInsumoLabel">Añadir Observacion</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="formSendObservacion" method="POST" enctype="multipart/form-data">
				<div class="modal-body">
					<!-- Errores -->
					<div class="alert alert-danger error-validacion mb-2 d-none">
						<ul class="mb-0">
							<!-- <li></li> -->
						</ul>
					</div>
					<div class="row">
						<!-- Numero economico -->
						<div class="col-md-6 form-group">
							<label for="">Numero Económico:</label>
							<select class="select2" name="generadorDetalle" id="modalAgregarObservacion_numero">
								<option value="">Seleccione un equipo</option>
							</select>
						</div>
					</div>
					<div class="row">
						<!-- Fecha de inicio -->
						<div class="col-md-6 from-group">
							<label for="start_date">Fecha de inicio:</label>
							<div class="input-group date" id="start_date" data-target-input="nearest">
								<input type="text" name="fecha_inicio" class="form-control datetimepicker-input form-control-sm" id="modalAgregarObservacion_fecha_inicio" data-target="#start_date" />
								<div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
						</div>
						<!-- Fecha fin -->
						<div class="col-md-6 from-group">
							<label for="end_date">Fecha de fin:</label>
							<div class="input-group date" id="end_date" data-target-input="nearest">
								<input type="text" name="fecha_fin" class="form-control form-control-sm datetimepicker-input" id="modalAgregarObservacion_fecha_fin" data-target="#end_date" />
								<div class="input-group-append" data-target="#end_date" data-toggle="datetimepicker">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
						</div>
						<!-- Observaciones -->
						<div class="col form-group">
							<label for="">Observaciones</label>
							<textarea name="observaciones" id="modalAgregarObservacion_observacion" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</form>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary btnAgregarObservacion">Agregar</button>
			</div>
		</div>
	</div>
</div>

<button id="openModalDetails" class="d-none" data-toggle="modal" data-target="#myModal"></button>
<?php
	array_push($arrayArchivosJS, 'vistas/js/generadores.js?v=2.1');
?>
