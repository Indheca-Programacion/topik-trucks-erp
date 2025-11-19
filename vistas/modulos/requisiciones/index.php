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
                Listado de Requisiciones
              </h3>
              <div class="card-tools">
                <!-- <a href="<?=Route::names('requisiciones.create')?>" class="btn btn-outline-primary">
                  <i class="fas fa-plus"></i> Crear requisición
                </a> -->
                <button type="button" id="btnFiltrar" class="btn btn-outline-info ml-1 float-right">
                  <i class="fas fa-sync-alt"></i> Listado
                </button>
                <button type="button" id="btnVerFiltros" class="btn btn-info float-right" data-toggle="collapse" data-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros">
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
                        <label class="input-group-text" for="filtroUbicacionId">Ubicacion</label>
                      </div>
                      <select class="custom-select select2" id="filtroUbicacionId" multiple="multiple">
                        <option value="">Selecciona una Ubicacion</option>
                        <?php foreach($ubicaciones as $ubicacion) { ?>
                        <option value="<?php echo $ubicacion["id"]; ?>">
                          <?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">
                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                        <div class="input-group-prepend">
                          <label class="input-group-text" for="filtroCentroServicioId">Centro de Servicio</label>
                        </div>
                        <select class="custom-select select2" id="filtroCentroServicioId" multiple="multiple">
                          <option value="">Selecciona un Centro de Servicio</option>
                          <?php foreach($servicioCentros as $servicioCentro) { ?>
                          <option value="<?php echo $servicioCentro["id"]; ?>" <?php echo (in_array($servicioCentro["id"], $arrayPuestos)) ? 'selected' : ''; ?>>
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

                    <!-- <div class="input-group input-group-sm mb-2 mb-md-0" style="flex-wrap: nowrap;"> -->
                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroServicioEstatusId">Estatus</label>
                      </div>
                      <select class="custom-select select2" id="filtroServicioEstatusId">
                        <option value="0" selected>Selecciona un Estatus</option>
                        <?php foreach($servicioStatus as $servicioEstatus) { ?>
                        <?php if ( $servicioEstatus["requisicionAbierta"] || $servicioEstatus["requisicionCerrada"] ) : ?>
                        <option value="<?php echo $servicioEstatus["id"]; ?>" <?php echo ($estatusIdDefault == $servicioEstatus["id"]) ? 'selected' : ''; ?>>
                          <?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
                        </option>
                        <?php endif; ?>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6 form-group">

                    <!-- <div class="input-group input-group-sm mb-2 mb-md-0" style="flex-wrap: nowrap;"> -->
                    <div class="input-group input-group-sm mb-2 mb-md-0 date" id="fechaInicialDTP" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroFechaInicial">Fecha Inicial:</label>
                      </div>
                      <input type="text" id="filtroFechaInicial" class="form-control form-control-sms datetimepicker-input" placeholder="Ingresa la fecha inicial" data-target="#fechaInicialDTP" value="<?= $fechaInicio; ?>">
                      <div class="input-group-append" data-target="#fechaInicialDTP" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                      </div>
                    </div>
                    <!-- </div> -->

                  </div>

                  <div class="col-md-6 form-group">

                    <div class="input-group input-group-sm date" id="fechaFinalDTP" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroFechaFinal">Fecha Final:</label>
                      </div>
                      <input type="text" id="filtroFechaFinal" class="form-control form-control-sms datetimepicker-input" placeholder="Ingresa la fecha final" data-target="#fechaFinalDTP" value="<?= $fechaActual; ?>">
                      <div class="input-group-append" data-target="#fechaFinalDTP" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-6 form-group">

                    <div class="input-group input-group-sm date" id="concepto" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="concepto">Concepto:</label>
                      </div>
                      <input type="text" id="filtroConcepto" class="form-control form-control-sms" placeholder="Ingresa el concepto">
                      
                    </div>

                  </div>

                  <div class="col-md-6 form-group">

                    <div class="input-group input-group-sm date" id="ordenCompra" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="ordenCompra">Orden Compra/Proveedor:</label>
                      </div>
                      <input type="text" id="filtroOrdenCompra" class="form-control form-control-sms" placeholder="Ingresa la orden de compra o nombre del proveedor">
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
                   <!-- <th>Centro de Servicio</th> -->
                   <th>Folio</th>
                   <th>Estatus</th>
                   <th>Fecha Requisición</th>
                   <th>Obra</th>
                   <th>Ubicacion Maquinaria</th>
                   <th>Solicitó</th>
                   <th>Número Económico</th>
                   <th>Ordenes Compra</th>
                   <th>Estatus</th>

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
  array_push($arrayArchivosJS, 'vistas/js/requisiciones.js?v=1.2');
?>
