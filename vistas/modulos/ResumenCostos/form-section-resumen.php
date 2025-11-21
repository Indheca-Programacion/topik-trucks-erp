
<div style="width: 100%;">
  <table class="table table-sm mb-0 nowrap" 
         id="tablaCostosResumen" 
         style="width:100%;">
    <thead class="thead-dark" id="dynamicTableHead"></thead>  
    <tbody></tbody>
  </table>
  <div class="d-flex flex-column align-items-end mt-2">
    <label>Total General:</label>
    <span id="spanTotal" class=""></span>
  </div>
</div>

<div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Detalle del Registro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul id="lista-resumen-costos"></ul>
      </div>
    </div>
  </div>
</div>
