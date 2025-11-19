<?php use App\Route; ?>

<div class="content-wrapper position-relative">


	<div id="scrollBtnContainer" class="position-fixed" style="bottom: 1.5rem; right: 1.5rem; z-index: 100; display: none;">
		<button id="scrollBtn" type="button" class="btn btn-primary btn-lg">
			<i class="fas fa-angle-double-down text-lg"></i>
		</button>
	</div>

	<section class="content-header">

		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1>Resumen de Costos <small class="font-weight-light">Visor</small></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
						<li class="breadcrumb-item active">Resumen de Costos</li>
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
					<div class="card card-secondary card-outline">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-binoculars"></i>
								Visor de Resumen de Costos
							</h3>
							<div class="card-tools">
								<div class="input-group input-group-sm " style="flex-wrap: nowrap;">
									<div class="input-group-prepend">
										<label class="input-group-text" for="filtroDivisas">Divisa</label>
									</div>
									<select class="custom-select select2" id="filtroDivisas">
										<?php foreach($divisas as $divisa) { ?>
											<option value="<?php echo $divisa["id"]; ?>">
											<?php echo mb_strtoupper(fString($divisa["nombreCorto"])); ?>
											</option>
										<?php } ?>
									</select>
								</div>
								<button type="button" id="btnFiltrar" class="btn btn-outline-info mt-2">
									<i class="fas fa-sync-alt"></i> Listado
								</button>   
							</div>
						</div>

						<div class="collapse show" id="collapseFiltros">
							<div class="card card-body mb-0">
								<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

									<div class="row">

										<div class="col-md-6 input-group input-group-sm mb-3" style="flex-wrap: nowrap;">
											<div class="input-group-prepend">
												<label class="input-group-text" for="filtroObraId">Obra</label>
											</div>
											<select class="custom-select select2" id="filtroObraId">
												<option value="0" selected>Selecciona una Obra</option>
												<?php foreach($obras as $obra) { ?>
												<option value="<?php echo $obra["id"]; ?>">
												<?php echo '[ ' . mb_strtoupper(fString($obra["empresas.nombreCorto"])) . ' ] ' . mb_strtoupper(fString($obra["descripcion"])); ?>
												</option>
												<?php } ?>
											</select>
										</div>

                                        <div class="col-md-6 input-group input-group-sm mb-3" style="flex-wrap: nowrap;">
											<div class="input-group-prepend">
												<label class="input-group-text" for="filtroEmpresaId">Empresa</label>
											</div>
											<select class="custom-select select2" id="filtroEmpresaId">
												<option value="0" selected>Selecciona una Empresa</option>
												<?php foreach($empresas as $empresa) { ?>
												<option value="<?php echo $empresa["id"]; ?>">
												<?php echo '[ ' . mb_strtoupper(fString($empresa["nombreCorto"])) . ' ] '; ?>
												</option>
												<?php } ?>
											</select>
										</div>
                                        
                                        <!-- Year Filter -->
                                        <div id="yearFilterWrapper" class="col-md-6 input-group input-group-sm mb-3 d-none" style="flex-wrap: nowrap;">
											<div class="input-group-prepend">
									            <label  class="input-group-text" for="filterYear">Año:</label>
											</div>
                                            <select id="filterYear" class="custom-select select2">
                                                <option value="all">Todos los años</option>
                                                <option value="2025">2025</option>
                                                <option value="2024">2024</option>
                                                <option value="2023">2023</option>
                                            </select>
										</div>

                                      	<div id="monthFilterWrapper" class="col-md-6 input-group input-group-sm mb-3 d-none" style="flex-wrap: nowrap;">
											<div class="input-group-prepend">
                                                <label class="input-group-text"  for="filterMonth">Mes:</label>
											</div>

                                            <select id="filterMonth" class="custom-select select2">
                                                <option value="all">Todos los meses</option>
                                                <option value="1">Enero</option>
                                                <option value="2">Febrero</option>
                                                <option value="3">Marzo</option>
                                                <option value="4">Abril</option>
                                                <option value="5">Mayo</option>
                                                <option value="6">Junio</option>
                                                <option value="7">Julio</option>
                                                <option value="8">Agosto</option>
                                                <option value="9">Septiembre</option>
                                                <option value="10">Octubre</option>
                                                <option value="11">Noviembre</option>
                                                <option value="12">Diciembre</option>
                                            </select>

										</div>

									</div><!-- <div class="row"> -->

							</div> <!-- <div class="card card-body mb-0"> -->
						</div> <!-- <div class="collapse" id="collapseFiltros"> -->

            			<div class="card-body px-0 pb-0">

							<div class="card card-primary card-outline card-outline-tabs mb-0 d-none">
								<div class="card-header p-0 border-bottom-0">
									<ul class="nav nav-tabs" id="tabCostosResumen" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="resumen-costos-tab" data-toggle="pill" href="#resumen-costos" role="tab" aria-controls="resumen-costos" aria-selected="true">Resumen</a>
										</li>

									</ul>
								</div>
								<div class="card-body">
									<div class="tab-content" id="tabCostosResumenContent">
										<div class="tab-pane fade show active" id="resumen-costos" role="tabpanel" aria-labelledby="resumen-costos-tab">
											<?php include "vistas/modulos/ResumenCostos/form-section-resumen.php"; ?>
										</div>
									</div>
								</div> <!-- /.card-body -->
							</div> <!-- /.card -->

						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				</div> <!-- /.col -->
			</div> <!-- ./row -->
		</div><!-- /.container-fluid -->
		
	
	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/ResumenCostos.js?v=2.3');
?>
