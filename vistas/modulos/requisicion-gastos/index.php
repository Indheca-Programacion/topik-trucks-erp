<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Requisiciones <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Requisiciones</li>
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
                Listado de Requisiciones de Gastos
              </h3>
              <div class="card-tools">
                <!-- <a href="<?=Route::names('requisicion-gastos.create')?>" class="btn btn-outline-primary">
                  <i class="fas fa-plus"></i> Crear requisici車n
                </a> -->
                <button type="button" id="btnFiltrar" class="btn btn-outline-info ml-1 float-right">
                  <i class="fas fa-sync-alt"></i> Listado
                </button>
                <button type="button" id="btnVerFiltros" class="btn btn-info float-right d-none" data-toggle="collapse" data-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros">
                  <i class="fas fa-eye"></i> Filtros
                </button>
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
                        <label class="input-group-text" for="filtroUbicacionId">Centro de Costo</label>
                      </div>
                      <select class="custom-select select2" id="filtroUbicacionId">
                        <option value="0" selected>Selecciona un Centro</option>
                        <?php foreach($ubicaciones as $ubicacion) { ?>
                        <option value="<?php echo $ubicacion["id"]; ?>">
                          <?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <!-- <div class="input-group input-group-sm mb-2 mb-md-0" style="flex-wrap: nowrap;"> -->
                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroServicioEstatusId">Estatus</label>
                      </div>
                      <select class="custom-select select2" id="filtroServicioEstatusId">
                        <option value="0" selected>Selecciona un Estatus</option>
                        <?php foreach($servicioStatus as $servicioEstatus) { ?>
                        <?php if ( $servicioEstatus["requisicionAbierta"] || $servicioEstatus["requisicionCerrada"] ) : ?>
                        <option value="<?php echo $servicioEstatus["id"]; ?>">
                          <?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
                        </option>
                        <?php endif; ?>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6 mb-2">

                    <!-- <div class="input-group input-group-sm mb-2 mb-md-0" style="flex-wrap: nowrap;"> -->
                    <div class="input-group input-group-sm mb-2 mb-md-0 date" id="fechaInicialDTP" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroFechaInicial">Fecha Inicial:</label>
                      </div>
                      <input type="text" id="filtroFechaInicial" class="form-control form-control-sms datetimepicker-input" placeholder="Ingresa la fecha inicial" data-target="#fechaInicialDTP">
                      <div class="input-group-append" data-target="#fechaInicialDTP" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                      </div>
                    </div>
                    <!-- </div> -->

                  </div>

                  <div class="col-md-6">

                    <div class="input-group input-group-sm date" id="fechaFinalDTP" data-target-input="nearest">
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

                    <div class="input-group input-group-sm date" id="concepto" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="concepto">Concepto:</label>
                      </div>
                      <input type="text" id="filtroConcepto" class="form-control form-control-sms" placeholder="Ingresa el concepto">
                      
                    </div>

                  </div>

                </div> <!-- <div class="row"> -->
              </div> <!-- <div class="card card-body mb-0"> -->
            </div> <!-- <div class="collapse" id="collapseFiltros"> -->

            <div class="card-body">
        
              <table class="table table-sm table-bordered table-striped" id="tablaRequisiciones" width="100%">
                 
                <thead>
                 <tr>
                   <th style="width:10px">#</th>
                   <th>Empresa</th>
                   <th>Folio</th>
                   <th>Estatus</th>
                   <th>Fecha Requisicion</th>
                   <th>Solicito</th>
                   <th>Acciones</th>
                 </tr>
                </thead>

                <tbody class="text-uppercase">
                </tbody>

               </table>

            </div> <!-- /.card-body -->
          </div> <!-- /.card -->
        </div> <!-- /.col -->
      </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

  </section>

</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/requisicion-gastos.js?v=1.00');
?>
