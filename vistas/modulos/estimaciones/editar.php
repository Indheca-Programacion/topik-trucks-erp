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
	            <li class="breadcrumb-item"><a href="<?=Route::names('estimaciones.index')?>"> <i class="fas fa-list-alt"></i> Generadores</a></li>
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
								<a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab" aria-controls="general" aria-selected="true">Detalles de Generador</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="observaciones-tab" data-toggle="pill" href="#observaciones" role="tab" aria-controls="observaciones" aria-selected="false">Detalles de incidencias</a>
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
							<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
								<div class="row">
									<div class="col-6">
										<?php include "vistas/modulos/generadores/formulario.php"; ?>
										<div class="row">
											<div class="col form-group align-self-start">
												<button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalVistaPrevia">
													<i class="fas fa-print"></i> Vista Previa
												</button>
												<?php if (!isset($generador->firmado) ) : ?>
													<button type="button" class="btn btn-success float-right" id="btnFirmar">
														<i class="fas fa-file-signature"></i> <strong>Autorizar</strong>
													</button>
												<?php else: ?>
													<button type="button" class="btn btn-secondary float-right" disabled>
														<i class="fas fa-file-signature"></i> <strong>Autorizado</strong>
													</button>
												<?php endif; ?>
												<button type="button" class="btn btn-warning" id="btnMandarCorregir">
													<i class="fas fa-edit"></i> Mandar a Corregir
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="observaciones" role="tabpanel" aria-labelledby="observaciones-tab">
								<div class="row">
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
									<div class="col-6">
										<?php include "vistas/modulos/estimaciones/formulario.php"; ?>
										<div class="row">
											<div class="col form-group align-self-start">
												<?php if (!isset($generador->estimacionFirma) ) : ?>
													<button type="button" class="btn btn-success btn-lg float-right" id="btnAutorizarEstimacion">
													<i class="fas fa-file-signature"></i> <strong>Autorizar</strong>
												</button>
												<?php else: ?>
													<button type="button" class="btn btn-secondary float-right" disabled>
														<i class="fas fa-file-signature"></i> <strong>Autorizado</strong>
													</button>
												<?php endif; ?>
												<button type="button" class="btn btn-info" id="modalEstimacionesbtn" data-toggle="modal" data-target="#modalVistaPreviaEstimacion">
													<i class="fas fa-print"></i> Vista Previa
												</button>
												<button type="button" class="btn btn-warning" id="btnMandarCorregir">
													<i class="fas fa-edit"></i> Mandar a Corregir
												</button>
											</div>
										</div>
									</div>
								</div>
								<div class="col-12">
									<table class="table table-bordered table-striped table-responsiveya " id="tablaEstimaciones">
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
													echo '<tr>';
													echo '<td class="partida" detalle_id="'.$registro["id"].'">' . $registro['numeroEconomico'] . '</td>';
													echo '<td>' . $registro['descripcion'] . '</td>';
													echo '<td><span class="dias">' . $registro['totalDias'] . '</span></td>';
													echo '<td><input name="detalles[costo][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['costo'] . '"></td>';
													echo '<td><span class="pu">' . $registro['pu'] . '</span></td>';
													echo '<td><input name="detalles[operacion][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['operacion'] . '"></td>';
													echo '<td><input name="detalles[comb][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['comb'] . '"></td>';
													echo '<td><input name="detalles[mantto][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['mantto'] . '"></td>';
													echo '<td><input name="detalles[flete][]" class="form-control form-control-sm campoConDecimal" type="text" value="' . $registro['flete'] . '"></td>';
													echo '<td><input name="detalles[ajuste][]" class="form-control form-control-sm " type="number" value="' . $registro['ajuste'] . '"></td>';
													echo '<td>$ <span class="importe">'.$registro['importe'].'</span></td>';
													echo '</tr>';
												}
											?>
										</tbody>
									</table>
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

	
	<!-- Modal -->
	<div class="modal fade" id="modalVistaPrevia" tabindex="-1" role="dialog" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 90vw;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVistaPreviaLabel">Vista Previa Generador</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="display: flex; justify-content: center; align-items: center;">
					<iframe class="embed-responsive-item" src="<?php echo Route::names('generadores.print', $id) ?>" width="100%" height="700" frameborder="0" style="max-width: 85vw;"></iframe>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="modalVistaPreviaEstimacion" tabindex="-1" role="dialog" aria-labelledby="modalVistaPreviaEstimacionLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 90vw;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVistaPreviaEstimacionLabel">Vista Previa Estimación</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="display: flex; justify-content: center; align-items: center;">
					<iframe class="embed-responsive-item" src="<?php echo Route::names('estimaciones.print', $id) ?>" width="100%" height="700" frameborder="0" style="max-width: 85vw;"></iframe>
				</div>
			</div>
		</div>
	</div>

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/estimaciones.js?v=1.0');
	array_push($arrayArchivosJS, 'vistas/js/generadores.js?v=1.0');
?>
