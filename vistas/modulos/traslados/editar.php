<?php
	$old = old();

	use App\Route;
?>
<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Traslados <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('traslados.index')?>"> <i class="fas fa-clipboard-check"></i> Traslados</a></li>
	            <li class="breadcrumb-item active">Editar Tarea</li>
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
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="tabTraslados" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="reporte-tab" data-toggle="pill" href="#reporte" role="tab" aria-controls="reporte" aria-selected="true">Reporte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="requisiciones-tab" data-toggle="pill" href="#requisiciones" role="tab" aria-controls="requisiciones" aria-selected="true">Requisiciones</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="tabTrasladosContent">
                    <div class="tab-pane fade show active" id="reporte" role="tabpanel" aria-labelledby="reporte-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-edit"></i>
                                            Editar traslado
                                        </h3>
                                    </div> <!-- <div class="card-header"> -->
                                    <div class="card-body">
                                        <?php include "vistas/modulos/errores/form-messages.php"; ?>
                                        <form id="formSend" method="POST" action="<?php echo Route::names('traslados.update', $traslado->id); ?>">
                                            <input type="hidden" name="_method" value="PUT">
                                            <?php include "vistas/modulos/traslados/formulario.php"; ?>
                                            <button type="button" id="btnSend" class="btn btn-outline-primary">
                                                <i class="fas fa-save"></i> Actualizar
                                            </button>
                                            <a class="btn btn-outline-info" id="btnDescargarTodo"><i class="fas fa-file-download"></i> Descargar Todo</a>
                                            <span class="badge badge-info" style="font-size: 1em; padding: 0.5em 1em;">
                                                <?php echo isset($trasladoArchivos) ? count($trasladoArchivos) . ' archivos' : '0 archivos'; ?>
                                            </span>
                                            <div id="msgSend"></div>
                                        </form>
                                        <?php include "vistas/modulos/errores/form-messages-validation.php"; ?>

                                        <div class="col-6-md form-group">
                                            <a id="btnPrint" target="_blank" href="<?php echo Route::names('traslados.print', $traslado->id); ?>" class="btn btn-outline-info float-right">
                                                <i class="fas fa-print"></i> Imprimir
                                            </a>
                                        </div>
                                    </div> <!-- /.card-body -->
                                </div> <!-- /.card -->
                            </div> <!-- /.col -->
                            <div class="col-md-6">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title
                                        ">
                                            <i class="fas fa-plus"></i>
                                            Añadir Gastos
                                        </h3>
                                    </div> <!-- <div class="card-header"> -->
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="hidden" id="traslado" name="traslado" value="<?php echo isset($traslado->id) ? $traslado->id : ""; ?>">
                                            <div class="col-md-6 form-group">
                                                <label for="gasto">Gasto:</label>
                                                <select id="gasto" class="custom-select form-controls-sm select2">
                                                    <option value="0">Selecciona un gasto</option>
                                                    <option value="1">Deducible</option>
                                                    <option value="2">No deducible</option>
                                                </select>
                                            </div> <!-- /.col -->
                                            <div class="col-md-6 form-group">
                                                <label for="total">Total:</label>
                                                <input type="text" id="total" class="form-control form-control-sm campoConDecimal text-uppercase" placeholder="Ingresa el total">
                                            </div> <!-- /.col -->
                                            <div class="col-md-6 form-group">
                                                <label for="descripcion">Descripcion:</label>
                                                <input type="text" id="descripcion" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción">
                                            </div> <!-- /.col -->
                                        </div>
                                        <div id="section-deducible" class="row section-deducible d-none">
                                            <div class="col-md-6 form-group">
                                                <label for="proveedor">Proveedor:</label>
                                                <input type="text" id="proveedor" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el proveedor">
                                            </div> <!-- /.col -->
                                            <div class="col-md-6 form-group">
                                                <label for="folio">Folio:</label>
                                                <input type="text" id="folio" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el folio">
                                            </div> <!-- /.col -->
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="facturaPDF">Factura PDF:</label>
                                                    <form class="dropzone needsclick" id="demo-upload" action="">
                                                        <div id="dropzone">
                                    
                                                            <div class="dz-message needsclick">    
                                                                Suelta la factura o haz clic para subir.
                                                                
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div> <!-- /.col -->
                                                <div class="col-md-6 form-group">
                                                    <label for="facturaXML">Factura XML:</label>
                                                    <form class="dropzone needsclick" id="xml-upload" action="">
                                                        <div id="dropzone">
                                    
                                                            <div class="dz-message needsclick">    
                                                                Suelta la factura o haz clic para subir.
                                                                
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div> <!-- /.row -->
                                        </div>
                                        <div id="section-no-deducible" class="row section-no-deducible">
                                            <div class="row">
                                                <div class="col form-group">
                                                    <label for="soporte">Soporte:</label>
                                                    <form class="dropzone needsclick" id="soporte-upload" action="">
                                                        <div id="dropzone">
                                    
                                                            <div class="dz-message needsclick">    
                                                                Suelta el soporte o haz clic para subir.
                                                                
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" id="btnAddGasto" class="btn btn-outline-primary">
                                            <i class="fas fa-plus"></i> Añadir Gasto
                                        </button>
                                        <div id="msgGasto"></div>
                                        
                                    </div> <!-- /.card-body -->
                                </div> <!-- /.card -->
                            </div> <!-- /.col -->
                            <div class="col-12">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-list"></i>
                                            Gastos
                                        </h3>
                                    </div> <!-- <div class="card-header"> -->
                                    <div class="card-body">
                                        <table id="tblGastos" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Gasto</th>
                                                    <th>Proveedor</th>
                                                    <th>Folio</th>
                                                    <th>Total</th>
                                                    <th>Descripcion</th>
                                                    <th style="width:10px;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div> <!-- /.card-body -->
                                </div> <!-- /.card -->
                            </div> <!-- /.col -->
                        </div> <!-- ./row -->
                    </div> <!-- /.tab-pane -->
                    <div class="tab-pane fade" id="requisiciones" role="tabpanel" aria-labelledby="requisiciones-tab">
                        <?php include "vistas/modulos/traslados/form-section-requisiciones.php";?>
                    </div> <!-- /.tab-pane -->
                </div> <!-- /.tab-content -->
            </div>
        </div>
    </div><!-- /.container-fluid -->

	</section>

    <!-- Modal id="modalVerArchivos" -->
	<div class="modal fade" id="modalVerArchivos" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalVerArchivosLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVerArchivosLabel">Evidencia Documental <span></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="accordion" id="accordionArchivos">
					</div>
					<div class="alert alert-danger error-validacion d-none">
						<ul class="mb-0">
							<li></li>
						</ul>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/traslados.js?v=1.4');
?>
