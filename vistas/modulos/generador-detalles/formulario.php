<?php
    if (isset($generadorDetalles->id)) {
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : $generadorDetalles->fecha_inicio;
    } else {
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : "";
    }
?>
<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<div class="row form-group">
    <div class="col-md-6">
        <label for="maquinariaId">Numero:</label>
        <select name="fk_maquinaria" id="maquinariaId" class="form-control select2">
            <option value="<?= $generadorDetalles->maquinaria ?>"><?php echo $maquinaSelected["numeroEconomico"] ?></option>
            <?php foreach($maquinarias as $maquinaria) { ?>
                <option value="<?php echo $maquinaria["id"]; ?>"
                    ><?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
                </option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="fecha">Fecha de Inicio:</label>
        <div class="input-group fecha" id="fechaInicio" data-target-input="nearest">
            <input type="date" name="fechaInicio" id="fecha" value="<?= $fecha_inicio ?>" class="form-control form-control-sm" placeholder="Ingresa la fecha">
        </div>
    </div>
</div>