<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Programación <small class="font-weight-light">Visor</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Programación</li>
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
                Visor de Programación
              </h3>
              <div class="card-tools">
                <button type="button" id="btnFiltrar" class="btn btn-outline-info ml-1 float-right">
                  <i class="fas fa-sync-alt"></i> Listado
                </button>
                <button type="button" class="btn btn-success ml-1 float-right" data-toggle="modal" data-target="#modalSeleccionarMaquinarias">
                  <i class="fas fa-file-alt"></i> Reporte General
                </button>
                <button type="button" id="btnVerFiltros" class="btn btn-info float-right d-none" data-toggle="collapse" data-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros">
                  <i class="fas fa-eye-slash"></i> Filtros
                </button>
              </div>
            </div>

            <div class="collapse show" id="collapseFiltros">
              <div class="card card-body mb-0">
                <input type="hidden" name="_token" value="<?php echo createToken(); ?>">
                <div class="row">

                  <div class="col-md-6">
                    
                    <div class="input-group input-group-sm mb-0" style="flex-wrap: nowrap;">
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
                    
                    <div class="input-group input-group-sm mb-0" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroObraId">Obra</label>
                      </div>
                      <select class="custom-select select2" id="filtroObraId">
                        <option value="0" selected>Selecciona una Obra</option>
                          <?php foreach($obras as $obra) { ?>
                            <option value="<?php echo $obra["id"]; ?>"
                              ><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
                            </option>
                          <?php } ?>
                      </select>
                    </div>

                  </div>

                </div> <!-- <div class="row"> -->
              </div> <!-- <div class="card card-body mb-0"> -->
            </div> <!-- <div class="collapse" id="collapseFiltros"> -->

            <div class="card-body">
              <button type="button" id="btnImprimir" class="btn btn-outline-info mb-2" disabled>
                <i class="fas fa-print"></i> imprimir
              </button>
              <?php if ( $permitirActualizarProgramacion ) : ?>
              <button type="button" id="btnAgregarSeguimiento" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#modalAgregarSeguimiento" disabled>
                <i class="fas fa-plus"></i> Agregar segumiento
              </button>
              <?php endif; ?>

              <!-- <div class="clearfix"></div> -->
        
              <table class="table table-sm table-bordered table-hover d-none" id="tablaProgramacion" width="100%">
                 
                <thead>
                  <tr encabezado-1>
                    <th scope="col" colspan="4" class="text-center bg-info">Especificaciones</th>
                  </tr>
                  <tr encabezado-2>
                    <th scope="col">Equipo</th>
                    <th scope="col">Empresa</th>
                    <th scope="col" style="min-width: 160px;">Ubicación</th>
                    <th scope="col">Estado</th>
                    <!-- <th class="text-center text-nowrap" col-fecha>Día 1</th>
                    <th class="text-center text-nowrap" col-fecha>Día 2</th>
                    <th class="text-center text-nowrap" col-fecha>Día 3</th>
                    <th class="text-center text-nowrap" col-fecha>Día 4</th>
                    <th class="text-center text-nowrap" col-fecha>Día 5</th>
                    <th class="text-center text-nowrap" col-fecha>Día 6</th>
                    <th class="text-center text-nowrap" col-fecha>Día 7</th>
                    <th>Total Litros</th> -->
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

    <!-- Modal id="modalAgregarSeguimiento" -->
    <div class="modal fade" id="modalAgregarSeguimiento" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalAgregarSeguimientoLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAgregarSeguimientoLabel"><i class="fas fa-plus"></i> Agregar Seguimiento</h5>
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

            <form id="formSeguimientoSend">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label for="modalAgregarSeguimiento_maquinariaId">Número Económico:</label>
                  <select name="maquinariaId" id="modalAgregarSeguimiento_maquinariaId" class="custom-select form-controls select2ModalAgregarSeguimiento">
                    <option value="">Selecciona un Número Económico</option>
                  </select>
                </div>

                <div class="table-responsive">
                  <table class="table table-sm tablaDetalle table-bordered table-striped mb-0 d-none" id="tablaAgregarSeguimiento" width="100%">
                    <thead>
                      <tr>
                        <th scope="col" width="50%">Tipo de Servicio</th>
                        <th scope="col" class="text-right" width="25%">Servicio Anterior</th>
                        <th scope="col" class="text-right" width="25%">Intervalo entre Servicios</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </form>

            <!-- <div class="alert alert-danger error-validacion mb-0 d-none">
              <ul class="mb-0">
                <li></li>
              </ul>
            </div> -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-outline-primary btnGuardar" disabled>
              <i class="fas fa-save"></i> Guardar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal id="modalCrearServicio" -->
    <div class="modal fade" id="modalCrearServicio" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalCrearServicioLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollablex">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCrearServicioLabel"><i class="fas fa-plus"></i> Crear Servicio</h5>
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

            <form id="formCrearServicioSend">

              <div class="row">

                <div class="col-lg-6">

                  <div class="card card-info card-outline mb-0">

                    <div class="card-body p-3">

                      <div class="row">

                        <div class="col-12 form-group">
                          <label for="modalCrearServicio_empresa">Empresa:</label>
                          <select type="text" id="modalCrearServicio_empresa" name="empresaId" class="form-control form-control-sm text-uppercase select2ModalCrearServicio" placeholder="Ingresa la empresa">
                            <option value="">Selecciona una Empresa</option>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_servicioCentroId">Centro de Servicio:</label>
                          <select name="servicioCentroId" id="modalCrearServicio_servicioCentroId" class="custom-select form-controls select2ModalCrearServicio">
                            <option value="">Selecciona un Centro</option>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group d-none">
                          <label for="modalCrearServicio_servicioEstatusId">Estatus:</label>
                          <select name="servicioEstatusId" id="modalCrearServicio_servicioEstatusId" class="custom-select form-controls " readonly>
                            <option value="">Selecciona un Estatus</option>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group d-none">
                          <label for="modalCrearServicio_solicitudTipoId">Tipo de Solicitud:</label>
                          <select name="solicitudTipoId" id="modalCrearServicio_solicitudTipoId" class="custom-select form-controls d-none">
                            <option value="">Selecciona un Tipo de Solicitud</option>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_fechaSolicitud">Fecha Solicitud:</label>
                          <div class="input-group date" id="modalCrearServicio_fechaSolicitudDTP" data-target-input="nearest">
                            <?php $permitirModificarFechas = false; ?>
                            <?php $fechaSolicitud = fFechaLarga(date("Y-m-d")); ?>
                            <?php if ( $permitirModificarFechas ) : ?>
                            <input type="text" name="fechaSolicitud" id="modalCrearServicio_fechaSolicitud" value="<?php echo $fechaSolicitud; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#modalCrearServicio_fechaSolicitudDTP">
                            <?php else: ?>
                            <input type="text" id="fechaSolicitud" value="<?php echo $fechaSolicitud; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#modalCrearServicio_fechaSolicitudDTP" disabled>
                            <?php endif; ?>
                            <div class="input-group-append" data-target="#modalCrearServicio_fechaSolicitudDTP" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                            </div>
                          </div>
                        </div>

                        <div class="col-xl-6 form-group mb-0">
                          <label for="modalCrearServicio_horasProyectadas">Horas Hombre Proyectadas:</label>
                          <input type="text" id="modalCrearServicio_horasProyectadas" name="horasProyectadas" value="" class="form-control form-control-sm text-right campoConDecimal" placeholder="Ingresa las horas proyectadas">
                        </div>

                      </div> <!-- <div class="row"> -->

                    </div> <!-- <div class="card-body"> -->

                  </div> <!-- <div class="card card-info card-outline"> -->
                </div> <!-- <div class="col-md-6"> -->

                <div class="col-lg-6">

                  <div class="card card-warning card-outline mb-0">

                    <div class="card-body p-3">

                      <div class="row">

                        <div class="col-xl-6 form-group d-none">
                          <label for="modalCrearServicio_mantenimientoTipoId">Tipo de Mantenimiento:</label>
                          <select name="mantenimientoTipoId" id="modalCrearServicio_mantenimientoTipoId" class="custom-select form-controls select2ModalCrearServicio">
                            <option value="">Selecciona un Tipo de Mantenimiento</option>
                          </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <input type="hidden" name="servicioTipoId" id="modalCrearServicio_servicioTipoId" value="">
                          <label for="modalCrearServicio_servicioTipo">Tipo de Servicio:</label>
                          <input type="text" id="modalCrearServicio_servicioTipo" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el tipo de servicio" readonly>
                        </div>

                        <div class="col-xl-6 form-group">
                          <input type="hidden" name="maquinariaId" id="modalCrearServicio_maquinariaId" value="">
                          <label for="modalCrearServicio_numeroEconomico">Número Económico:</label>
                          <input type="text" id="modalCrearServicio_numeroEconomico" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número económico" readonly>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_ubicacion">Ubicación:</label>
                            <select name="ubicacionId" id="modalCrearServicio_ubicacion" class="custom-select form-control form-control-sm text-uppercase select2ModalCrearServicio">
                            </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_obra">Obra:</label>
                          <select name="obraId" id="modalCrearServicio_obra" class="custom-select form-control form-control-sm text-uppercase select2ModalCrearServicio">
                          </select>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_fechaProgramacion">Fecha de finalización estimada:</label>
                          <div class="input-group date" id="modalCrearServicio_fechaProgramacionDTP" data-target-input="nearest">
                            <input type="text" name="fechaProgramacion" id="modalCrearServicio_fechaProgramacion" value="" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de finalización estimada" data-target="#modalCrearServicio_fechaProgramacionDTP">
                            <div class="input-group-append" data-target="#modalCrearServicio_fechaProgramacionDTP" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                            </div>
                          </div>
                        </div>

                        <div class="col-xl-6 form-group">
                          <label for="modalCrearServicio_kitMantenimiento">Seleccionar kit de mantenimiento:</label>
                          <select name="kitMantenimiento" id="modalCrearServicio_kitMantenimiento" class="custom-select form-control form-control-sm text-uppercase select2ModalCrearServicio">
                          </select>
                        </div>

                        <div class="col-12 form-group mb-0">
                          <label for="descripcion">Descripción del Trabajo a realizar:</label>
                          <textarea name="descripcion" id="modalCrearServicio_descripcion" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa la Descripción del Trabajo a realizar"></textarea>
                        </div>

                      </div> <!-- <div class="row"> -->

                    </div> <!-- <div class="card-body"> -->

                  </div> <!-- <div class="card card-warning card-outline"> -->

                </div> <!-- <div class="col-md-6"> -->

              </div> <!-- <div class="row"> -->

            </form>

            <!-- <div class="alert alert-danger error-validacion mb-0 d-none">
              <ul class="mb-0">
                <li></li>
              </ul>
            </div> -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-outline-primary btnGuardar" disabled>
              <i class="fas fa-save"></i> Guardar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Seleccionar Maquinarias -->
    <div class="modal fade" id="modalSeleccionarMaquinarias" data-backdrop="static" data-keyboard="false" aria-labelledby="modalSeleccionarMaquinariasLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalSeleccionarMaquinariasLabel"><i class="fas fa-search"></i> Seleccionar Maquinarias</h5>
            <br>
            <span class="text-muted">Si hay alguna maquinaria seleccionada y no aparece en el reportes es debido a que no tiene seguimiento de mantenimiento.</span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label for="filtroEmpresaReporteMaquinarias">Empresa:</label>
                  <select class="custom-select select2" id="filtroEmpresaReporteMaquinarias">
                    <option value="0" selected>Selecciona una Empresa</option>
                    <?php foreach($empresas as $empresa) { ?>
                    <option value="<?php echo $empresa["id"]; ?>">
                      <?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
                    </option>
                    <?php } ?>
                  </select>

                </div>
                <div class="col-md-6 form-group">
                  <label for="filtroObraReporteMaquinarias">Obra:</label>
                  <select class="custom-select select2" id="filtroObraReporteMaquinarias">
                    <option value="0" selected>Selecciona una Obra</option>
                      <?php foreach($obras as $obra) { ?>
                        <option value="<?php echo $obra["id"]; ?>"
                          ><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
                        </option>
                      <?php } ?>
                  </select>

                </div>
              </div>
              <div class="form-group mb-0">
                <table id="tablaMaquinariasReporte" class="table table-sm table-bordered table-hover mb-0" width="100%">
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
                      <th>Estatus</th>
                    </tr> 
                  </thead>
                  <tbody class="text-uppercase">
                    <!-- Aquí se agregarán las filas dinámicamente -->
                  </tbody>
                </table>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-success btn-sm" id="btnGenerarReporteMaquinarias" >
              <i class="fas fa-file-alt"></i> Generar Reporte
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/programacion.js?v=1.7');
?>
