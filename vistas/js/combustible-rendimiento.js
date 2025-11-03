let tableList = document.getElementById("tablaCombustibleRendimiento");
let parametrosTableList = { responsive: false };

// Realiza la petición para actualizar el rendimiento de combustible
function fActualizarListado(rutaAjax, idTabla, parametros = {}) {
  fetch(rutaAjax, {
    method: "GET", // *GET, POST, PUT, DELETE, etc.
    cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .catch((error) => console.log("Error:", error))
    .then((data) => {
      // console.log(data)
      tableList.classList.remove("d-none");

      let columnasFecha = tableList.querySelectorAll("thead tr th[col-fecha]");
      columnasFecha.forEach((item, index) => {
        let fecha = data.datos.arrayFechas[index].fecha;
        let nombreDia = data.datos.arrayFechas[index].nombreDia;
        item.innerHTML = `${fecha}<br> ${nombreDia}`;
      });

      $(idTabla)
        .DataTable({
          autoWidth: false,
          responsive:
            parametros.responsive === undefined ? true : parametros.responsive,
          pageLength: 25,
          scrollX: true,
          data: data.datos.registros,
          columns: data.datos.columnas,

          // createdRow: function (row, data, index) {
          // 	if ( data.colorTexto != '' ) $('td', row).eq(4).css("color", data.colorTexto);
          // 	if ( data.colorFondo != '' ) $('td', row).eq(4).css("background-color", data.colorFondo);
          // },

          buttons: [
            { extend: "copy", text: "Copiar", className: "btn-info" },
            { extend: "csv", className: "btn-info" },
            { extend: "excel", className: "btn-info" },
            {
              extend: "pdf",
              className: "btn-info",
              orientation: "landscape", // Horizontal
              pageSize: "A4", // Tamaño opcional
              text: "PDF",
              customize: function (doc) {
                doc.styles.tableHeader.alignment = "center";
                doc.styles.tableBodyOdd.alignment = "center";
                doc.styles.tableBodyEven.alignment = "center";
              },
            },
            { extend: "print", text: "Imprimir", className: "btn-info" },
            {
              extend: "colvis",
              text: "Columnas visibles",
              className: "btn-info",
            },
          ],

          language: LENGUAJE_DT,
          aaSorting: [],
        })
        .buttons()
        .container()
        .appendTo(idTabla + "_wrapper .row:eq(0)"); // $(idTabla).DataTable({
    }); // .then( data => {
} // function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

// LLamar a la funcion fAjaxDataTable() para llenar el Listado
// if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/CombustibleRendimientoAjax.php', '#tablaCombustibleRendimiento');

$("#collapseFiltros").on("show.bs.collapse", function (event) {
  let btnVerFiltros = document.getElementById("btnVerFiltros");
  btnVerFiltros.querySelector("i").classList.remove("fa-eye");
  btnVerFiltros.querySelector("i").classList.add("fa-eye-slash");
});

$("#collapseFiltros").on("hide.bs.collapse", function (event) {
  let btnVerFiltros = document.getElementById("btnVerFiltros");
  btnVerFiltros.querySelector("i").classList.remove("fa-eye-slash");
  btnVerFiltros.querySelector("i").classList.add("fa-eye");
});

$("#btnFiltrar").on("click", function (e) {
  document.getElementById("filtroFechaInicial").classList.remove("is-invalid");
  // document.getElementById('filtroFechaFinal').classList.remove('is-invalid');

  tableList.classList.add("d-none");

  $(tableList).DataTable().destroy();
  tableList.querySelector("tbody").innerHTML = "";

  let empresaId = $("#filtroEmpresaId").val();
  let ubicacionId = $("#filtroUbicacionId").val();
  let fechaInicial = $("#filtroFechaInicial").val();
  // let fechaFinal = $('#filtroFechaFinal').val();

  // if ( fechaInicial == '' || fechaFinal == '' ) {
  if (fechaInicial == "") {
    if (fechaInicial == "")
      document.getElementById("filtroFechaInicial").classList.add("is-invalid");
    // if ( fechaFinal == '' ) document.getElementById('filtroFechaFinal').classList.add('is-invalid');

    return;
  }

  // fActualizarListado(`${rutaAjax}app/Ajax/CombustibleRendimientoAjax.php?empresaId=${empresaId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}`, '#tablaCombustibleRendimiento', parametrosTableList);
  fActualizarListado(
    `${rutaAjax}app/Ajax/CombustibleRendimientoAjax.php?empresaId=${empresaId}&ubicacionId=${ubicacionId}&fechaInicial=${fechaInicial}`,
    "#tablaCombustibleRendimiento",
    parametrosTableList
  );
});

// Activar el elemento Select2
$(".select2").select2({
  tags: false,
  width: "100%",
  // theme: 'bootstrap4'
});
$(".select2Add").select2({
  tags: true,
  // ,theme: 'bootstrap4'
});
//Date picker
$(".input-group.date").datetimepicker({
  format: "DD/MMMM/YYYY",
});
