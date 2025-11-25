<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Servicios <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Servicios</li>
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
                <i class="fas fa-list-ol"></i> 
                Listado de Servicios
              </h3>
              <div class="card-tools">
                <div class="btn-group float-right" role="group" aria-label="Basic example">
                  <button type="button" class="btn btn-outline-info" id="btnVerFiltros" data-toggle="collapse" data-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros">
                    <i class="fas fa-eye"></i> Filtros
                  </button>
                  <button type="button" class="btn btn-outline-info" id="btnFiltrar">
                    <i class="fas fa-sync-alt"></i> Listado
                  </button>
                  <button type="button" class="btn btn-outline-info" id="btnGenerarPDF">
                    <i class="fas fa-file-pdf text-danger"></i> Reporte
                  </button>
                </div>
              </div>
            </div>

            <div class="collapse" id="collapseFiltros">
              <div class="card card-body mb-0">
                <div class="row">

                  <div class="col-md-6">
                    
                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroEmpresaId">Empresa</label>
                      </div>
                      <select class="custom-select select2" id="filtroEmpresaId">
                        <option value="0" selected>Selecciona una Empresa</option>
                        <?php foreach($empresas as $empresa) { ?>
                        <option value="<?php echo $empresa["id"]; ?>">
                          <?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroServicioCentroId">Centro de Servicio</label>
                      </div>
                      <select class="custom-select select2" id="filtroServicioCentroId">
                        <option value="0" selected>Selecciona un Centro</option>
                        <?php foreach($servicioCentros as $servicioCentro) { ?>
                        <option value="<?php echo $servicioCentro["id"]; ?>">
                          <?php echo mb_strtoupper(fString($servicioCentro["descripcion"])); ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <!-- <div class="input-group input-group-sm mb-2 mb-md-0" style="flex-wrap: nowrap;"> -->
                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroMaquinariaId">Número Económico</label>
                      </div>
                      <select class="custom-select select2" id="filtroMaquinariaId">
                        <option value="0" selected>Selecciona un Número Económico</option>
                        <?php foreach($maquinarias as $maquinaria) { ?>
                        <option value="<?php echo $maquinaria["id"]; ?>">
                          <?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
                          [ <?php echo mb_strtoupper(fString($maquinaria["serie"])); ?> ]
                        </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroServicioEstatusId">Estatus</label>
                      </div>
                      <select class="custom-select select2" id="filtroServicioEstatusId">
                        <option value="0" selected>Selecciona un Estatus</option>
                        <?php foreach($servicioStatus as $servicioEstatus) { ?>
                        <?php if ( $servicioEstatus["servicioAbierto"] || $servicioEstatus["servicioCerrado"] ) : ?>
                        <option value="<?php echo $servicioEstatus["id"]; ?>">
                          <?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
                        </option>
                        <?php endif; ?>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="input-group input-group-sm mb-2 mb-md-0 date mb-2" id="fechaInicialDTP" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroFechaInicial">Fecha Inicial:</label>
                      </div>
                      <input type="text" id="filtroFechaInicial" class="form-control form-control-sms datetimepicker-input" placeholder="Ingresa la fecha inicial" data-target="#fechaInicialDTP">
                      <div class="input-group-append" data-target="#fechaInicialDTP" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="input-group input-group-sm date mb-2" id="fechaFinalDTP" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroFechaFinal">Fecha Final:</label>
                      </div>
                      <input type="text" id="filtroFechaFinal" class="form-control form-control-sms datetimepicker-input" placeholder="Ingresa la fecha final" data-target="#fechaFinalDTP">
                      <div class="input-group-append" data-target="#fechaFinalDTP" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroServicioTipoId">Tipo de servicio</label>
                      </div>
                      <select class="custom-select select2" id="filtroServicioTipoId">
                        <option value="0" selected>SELECCIONA EL TIPO DE SERVICIO</option>
                        <?php foreach($serviciosTipo as $servicio) { ?>
                        <option value="<?php echo $servicio["id"]; ?>">
                          <?php echo mb_strtoupper(fString($servicio["descripcion"])); ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroMantenimientoTipoId">Tipo de mantenimiento</label>
                      </div>
                      <select class="custom-select select2" id="filtroMantenimientoTipoId">
                        <option value="0" selected>SELECCIONA EL TIPO DE MANTENIMIENTO</option>
                        <?php foreach($mantenimientosTipo as $mantenimiento) { ?>
                        <option value="<?php echo $mantenimiento["id"]; ?>">
                          <?php echo mb_strtoupper(fString($mantenimiento["nombreCorto"])); ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                </div> <!-- <div class="row"> -->
              </div> <!-- <div class="card card-body mb-0"> -->
            </div> <!-- <div class="collapse" id="collapseFiltros"> -->

            <div class="card-body">

              <table class="table table-sm table-bordered table-striped" id="tablaServicios" width="100%">
                 
                <thead>
                 <tr>
                   <th style="width:10px">#</th>
                   <th>Folio</th>
                   <th>Estatus</th>
                   <th>Fecha Solicitud</th>
                   <th>Tipo de Mantenimiento</th>
                   <th>Tipo de Servicio</th>
                   <th>Tipo de Maquinaria</th>
                   <th>Número Económico</th>
                   <th>Marca</th>
                   <th>Modelo</th>
                   <th>Serie</th>
                   <th>Descripción</th>
                   <th>Creó</th>
                   <th>Acciones</th>
                 </tr> 
                </thead>

                <tbody>
                </tbody>

              </table>

            </div> <!-- /.card-body -->
          </div> <!-- /.card -->
        </div> <!-- /.col -->
      </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

  </section>

  <!-- Modal -->
  <div class="modal fade" id="modalVerReporte" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalVerReporteLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 14px; right: 20px;">
            <span aria-hidden="true" style="color: white;">&times;</span>
          </button>
          <div class="archivo">
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/servicios.js?v=1.14');
?>
