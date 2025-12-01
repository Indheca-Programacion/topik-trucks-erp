
<div class="row">
    <input type="hidden" name="servicioId" id="servicioId">
    <input type="hidden" name="presupuestoId" id="presupuestoId" value="<?= isset($presupuesto) ? $presupuesto->id : '' ?>">
    <input type="hidden" name="_token" value="<?= token(); ?>">
    <input type="hidden" name="accion" id="accion" value="agregarPartida">
    
    <div class="form-group col-md-6">
        <label for="cantidad">Cantidad</label>
        <input type="number" name="cantidad" id="cantidad" class="form-control form-control-sm">
    </div>
    <div class="form-group col-md-6">
        <label for="unidad">Unidad</label>
        <input type="text" name="unidad" id="unidad" class="form-control form-control-sm text-uppercase">
    </div>
    <div class="form-group col-md-12">
        <label for="descripcion">Descripción</label>
        <input type="text" name="descripcion" id="descripcion" class="form-control form-control-sm text-uppercase">
    </div>
    <div class="form-group col-md-6">
        <label for="costo_base">Costo Base</label>
        <input type="number" step="0.01" name="costo_base" id="costo_base" class="form-control form-control-sm">
    </div>
    <div class="form-group col-md-6">
        <label for="logistica">Logística (%)</label>
        <input type="number" step="0.01" name="logistica" id="logistica" class="form-control form-control-sm">
    </div>
    <div class="form-group col-md-6">
        <label for="mantenimiento">Mantenimiento (%)</label>
        <input type="number" step="0.01" name="mantenimiento" id="mantenimiento" class="form-control form-control-sm">
    </div>
    <div class="form-group col-md-6">
        <label for="utilidad">Utilidad (%)</label>
        <input type="number" step="0.01" name="utilidad" id="utilidad" class="form-control form-control-sm">
    </div>
</div>