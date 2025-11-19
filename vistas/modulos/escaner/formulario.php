<?php
	use App\Route;
	if ( isset($maquinaria->id) ) {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $maquinaria->empresaId;
		$numeroEconomico = isset($old["numeroEconomico"]) ? $old["numeroEconomico"] : $maquinaria->numeroEconomico;
		$numeroFactura = isset($old["numeroFactura"]) ? $old["numeroFactura"] : $maquinaria->numeroFactura;
		$maquinariaTipoId = isset($old["maquinariaTipoId"]) ? $old["maquinariaTipoId"] : $maquinaria->maquinariaTipoId;
		$marcaId = isset($old["marcaId"]) ? $old["marcaId"] : $maquinaria->modelo["marcaId"];
		$modeloId = isset($old["modeloId"]) ? $old["modeloId"] : $maquinaria->modeloId;
		$year = isset($old["year"]) ? $old["year"] : $maquinaria->year;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $maquinaria->descripcion;
		$serie = isset($old["serie"]) ? $old["serie"] : $maquinaria->serie;
		$colorId = isset($old["colorId"]) ? $old["colorId"] : $maquinaria->colorId;
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : $maquinaria->estatusId;
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : $maquinaria->ubicacionId;
		$almacenId = isset($old["almacenId"]) ? $old["almacenId"] : $maquinaria->almacenId;
		$obraId = isset($old["obraId"]) ? $old["obraId"] : $maquinaria->obraId;
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $maquinaria->observaciones;
	}
    $rutaServicio = Route::names("servicios.create");
    $rutaCombustible = Route::names("combustible-cargas.create");
?>

<input type="hidden" name="maquinariaId" id="maquinariaId" value="<?=$maquinaria->id?>">
<div class="row">

    <div class="col-md-6">

                <input type="hidden" name="_token" value="<?php echo createToken(); ?>">

                <div class="row">

                    <div class="col-md-12 form-group">

                        <label for="empresaId">Empresa:</label>
                        <!-- <select name="empresaId" id="empresaId" class="custom-select form-controls select2" style="width: 100%"> -->
                        <select name="empresaId" id="empresaId" class="custom-select form-controls select2" disabled>
                        <?php if ( isset($maquinaria->id) ) : ?>
                        <!-- <select id="empresaId" class="custom-select form-controls select2" style="width: 100%" disabled> -->
                        <?php else: ?>
                            <option value="">Selecciona una Empresa</option>
                        <?php endif; ?>
                            <?php foreach($empresas as $empresa) { ?>
                            <option value="<?php echo $empresa["id"]; ?>"
                                <?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>
                                ><?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
                            </option>
                            <?php } ?>
                        </select>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 form-group">
                        <label for="numeroEconomico">Número Económico:</label>
                        <input disabled type="text" name="numeroEconomico" value="<?php echo fString($numeroEconomico); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número económico">
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="numeroFactura">Número de Factura:</label>
                        <input disabled type="text" name="numeroFactura" value="<?php echo fString($numeroFactura); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número o folio de Factura">
                    </div>

                </div>

                <div class="row">

                    <!-- <div class="form-group"> -->
                    <div class="col-md-6 form-group">
                        <label for="maquinariaTipoId">Tipo de Maquinaria:</label>
                        <!-- <select name="maquinariaTipoId" id="maquinariaTipoId" class="custom-select form-controls xselect2Add" style="width: 100%"> -->
                        <select disabled name="maquinariaTipoId" id="maquinariaTipoId" class="custom-select form-controls select2Add">
                        <?php if ( isset($maquinaria->id) ) : ?>
                        <!-- <select id="maquinariaTipoId" class="form-control select2" style="width: 100%" disabled> -->
                        <?php else: ?>
                            <option value="">Selecciona un Tipo de Maquinaria</option>
                        <?php endif; ?>
                            <?php foreach($maquinariaTipos as $maquinariaTipo) { ?>
                            <option value="<?php echo $maquinariaTipo["id"]; ?>"
                                <?php echo $maquinariaTipoId == $maquinariaTipo["id"] ? ' selected' : ''; ?>
                                ><?php echo mb_strtoupper(fString($maquinariaTipo["descripcion"])); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="marcaId">Marca:</label>

                            <select disabled name="marcaId" id="marcaId" class="custom-select form-controls select2Add">
                            <?php if ( isset($maquinaria->id) ) : ?>
                            <!-- <select id="marcaId" class="form-control select2" style="width: 100%" disabled> -->
                            <?php else: ?>
                            <!-- <select name="marcaId" id="marcaId" class="form-control select2Add" style="width: 100%"> -->
                                <option value="">Selecciona una Marca</option>
                            <?php endif; ?>
                                <?php foreach($marcas as $marca) { ?>
                                <option value="<?php echo $marca["id"]; ?>"
                                    <?php echo $marcaId == $marca["id"] ? ' selected' : ''; ?>
                                    ><?php echo mb_strtoupper(fString($marca["descripcion"])); ?>
                                </option>
                                <?php } ?>
                            </select>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 form-group">
                        <label for="modeloId">Modelo:</label>

                            <select disabled name="modeloId" id="modeloId" class="custom-select form-controls select2Add">
                            <?php if ( !isset($maquinaria->id) ) : ?>
                                <option value="">Selecciona un Modelo</option>
                            <?php else: ?>
                            <?php endif; ?>
                                <?php foreach($modelos as $modelo) { ?>

                                <!-- $maquinaria->modelo["marcaId"] == $modelo["marcaId"] -->

                                <?php if ( $marcaId == $modelo["marcaId"] ) : ?>

                                <option value="<?php echo $modelo["id"]; ?>"
                                    <?php echo $modeloId == $modelo["id"] ? ' selected' : ''; ?>
                                    ><?php echo mb_strtoupper(fString($modelo["descripcion"])); ?>
                                </option>

                                <?php endif; ?>

                                <?php } ?>
                            
                            </select>
                    </div>
    
                    <div class="col-md-6 form-group">
                        <label for="year">Año:</label>
                        <input disabled type="text" name="year" value="<?php echo fString($year); ?>" class="form-control form-control-sm campoSinDecimal" placeholder="Ingresa el año de la Maquinaria" maxlength="4">
                    </div>

                </div>

                <div class="row">
                    
                    <div class="col-md-12 form-group">
                        <label for="descripcion">Descripción:</label>
                        <input disabled type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción">
                    </div>

                </div>

                <div class="row">
                    
                    <div class="col-md-12 form-group">
                        <label for="serie">Serie:</label>
                        <input disabled type="text" name="serie" value="<?php echo fString($serie); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la serie de la Maquinaria">
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 form-group">
                        <label for="colorId">Color:</label>

                            <select disabled name="colorId" id="colorId" class="custom-select form-controls select2Add">
                            <?php if ( isset($maquinaria->id) ) : ?>
                            <!-- <select id="colorId" class="form-control select2" style="width: 100%" disabled> -->
                            <?php else: ?>
                            <?php endif; ?>
                                <option value="">Selecciona un Color</option>
                                <?php foreach($colores as $color) { ?>
                                <option value="<?php echo $color["id"]; ?>"
                                    <?php echo $colorId == $color["id"] ? ' selected' : ''; ?>
                                    ><?php echo mb_strtoupper(fString($color["descripcion"])); ?>
                                </option>
                                <?php } ?>
                            </select>

                    </div>

                    <div class="col-md-6 form-group">
                        <label for="estatusId">Estatus:</label>

                            <select disabled name="estatusId" id="estatusId" class="custom-select form-controls select2Add">
                            <?php if ( isset($maquinaria->id) ) : ?>
                            <!-- <select id="estatusId" class="form-control select2" style="width: 100%" disabled> -->
                            <?php else: ?>
                                <option value="">Selecciona un Estatus</option>
                            <?php endif; ?>
                                <?php foreach($estatus as $status) { ?>
                                <option value="<?php echo $status["id"]; ?>"
                                    <?php echo $estatusId == $status["id"] ? ' selected' : ''; ?>
                                    ><?php echo mb_strtoupper(fString($status["descripcion"])); ?>
                                </option>
                                <?php } ?>
                            </select>

                    </div>
    
                </div>

    </div> <!-- <div class="col-md-6"> -->

    <div class="col-md-6">
        <div class="row mt-4">
            <div class="col-md-4">
                <a href="<?= $rutaCombustible ?>" class="btn btn-info btn-block">Carga de Combustible</a>
            </div>
            <div class="col-md-4">
                <a href="<?= $rutaServicio ?>" class="btn btn-info btn-block">Crear Servicio</a>
            </div>
            <!-- <div class="col-md-4">
                <button type="button" class="btn btn-info"></button>
            </div> -->
        </div> <!-- <div class="row"> -->
    </div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->