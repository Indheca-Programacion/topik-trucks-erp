<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Cargas de Combustible <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Cargas de Combustible</li>
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
                Listado de Cargas de Combustible
              </h3>
              <div class="card-tools">
                <a href="<?=Route::names('combustible-cargas.create')?>" class="btn btn-outline-primary ml-1 float-right">
                  <i class="fas fa-plus"></i> Crear carga de combustible
                </a>    
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
                        <label class="input-group-text" for="filtroEmpleadoId">Empleado</label>
                      </div>
                      <select class="custom-select select2" id="filtroEmpleadoId">
                        <option value="0" selected>Selecciona un Empleado</option>
                        <?php foreach($empleados as $empleado) { ?>
                        <option value="<?php echo $empleado["id"]; ?>">
                          <?php echo mb_strtoupper(fString($empleado["nombreCompleto"])); ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                </div> <!-- <div class="row"> -->
              </div> <!-- <div class="card card-body mb-0"> -->
            </div> <!-- <div class="collapse" id="collapseFiltros"> -->

            <div class="card-body">
        
              <table class="table table-sm table-bordered table-striped" id="tablaCombustibleCargas" width="100%">
                 
                <thead>
                 <tr>
                   <th style="width:10px">#</th>
                   <th>Empresa</th>
                   <th>Empleado</th>
                   <th>Fecha</th>
                   <th>Hora</th>
                   <th>Litros</th>
                   <th>Observaciones</th>
                   <th>Acciones</th>
                 </tr> 
                </thead>

                <!-- <tbody class="text-uppercase"> -->
                <tbody>
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
  array_push($arrayArchivosJS, 'vistas/js/combustible-cargas.js?v=1.04');
?>
