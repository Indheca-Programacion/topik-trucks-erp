<div class="row">

  <?php
  use App\Route;
  // var_dump($maquinaria->servicios);
  ?>

	<div class="col-12">

		<?php if ( $maquinaria->kits ) : ?>
			
		<div class="card card-info card-outline">

            <div class="card-tools pt-1">
                <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#modalAgregarKitMantenimiento">
                    <i class="fas fa-plus"></i> Agregar Kit de Mantenimiento
                </button>

            </div>
			<!-- <div class="card-body table-responsive p-0"> -->
            <div class="card-body">

  				<table class="table table-hover text-nowrap" id="tablaMaquinariaKits" width="100%">
                <!-- <table class="table table-bordered table-striped" id="tablaMaquinarias" width="100%"> -->
                  <thead>
                    <tr>
                        <th style="width:10px">#</th>
                        <th>Tipo de Mantenimiento</th>
                        <th>Tipo de Maquinaria</th>
                        <th>Modelo</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>

                  	<?php foreach($maquinaria->kits as $key => $value) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td class="text-uppercase"><?= $value["tipoMantenimiento"] ?></td>
                        <td class="text-uppercase"><?= $value["tipoMaquinaria"] ?></td>
                        <td class="text-uppercase"><?= $value["modelo"] ?></td>
                        <td class="text-uppercase"><?= $value["proveedor"]??'NO ESPECIFICADO' ?></td>
                        <td>
                            <a target="_blank" href="<?=Route::names('kit-mantenimiento.edit', $value["id"])?>" class="btn btn-outline-info btn-sm" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method='POST' action='<?=Route::names('kits-maquinarias.destroy', $value["kitId"])?>' style='display: inline'>
                                <input type='hidden' name='_method' value='DELETE'>
                                <input type='hidden' name='id' value='<?php echo $maquinaria->id; ?>'>
                                <button type='button' class='btn btn-sm btn-danger eliminar' folio='<?php echo $value["tipoMantenimiento"]; ?>'>
                                    <i class='far fa-times-circle'></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>

                  </tbody>
                </table>

          <?php
            // $comandoJS = "fDataTable('#tablaMaquinariaServicios');"
            if ( isset($comandoJS) ) $comandoJS .= "fDataTable('#tablaMaquinariaKits');";
            else $comandoJS = "fDataTable('#tablaMaquinariaKits');";
          ?>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info"> -->

		<?php else: ?>
        <button type="button" class="btn btn-outline-primary mt-2 mr-2 float-right" data-toggle="modal" data-target="#modalAgregarKitMantenimiento">
            <i class="fas fa-plus"></i> Agregar Kit de Mantenimiento
        </button>
		<div class="jumbotron">
			<p class="display-4">Maquinaria sin Kits de Mantenimiento</p>
		</div>
		<?php endif; ?>

	</div> <!-- <div class="col-12"> -->

    <!-- Modal -->
    <div class="modal fade" id="modalAgregarKitMantenimiento" tabindex="-1" role="dialog" aria-labelledby="modalAgregarKitMantenimientoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarKitMantenimientoLabel">Agregar Kit de Mantenimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <!-- Aquí puedes agregar el formulario para agregar un kit de mantenimiento -->
                    <div class="form-group">
                        <label for="kits">Kits de Mantenimiento</label>
                        <select name="kit_id" id="kits" class="form-control select2">
                            <option value="">Seleccione un Kit</option>
                            <?php foreach ($kits as $kit): ?>
                                <option value="<?= $kit['id'] ?>"><?= $kit['tipoMantenimiento'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="maquinaria_id" value="<?= $maquinaria->id ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" id="agregarKitMantenimiento" class="btn btn-primary">Agregar</button>
                </div>
            </div>
        </div>
    </div>

</div> <!-- <div class="row"> -->