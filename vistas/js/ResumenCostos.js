let tableList = document.getElementById("tablaCostosResumen");
let parametrosTableList = { responsive: true };

function cargarTablaConFiltros() {
  $(".card.card-primary.card-outline.card-outline-tabs").removeClass("d-none");

  $("#yearFilterWrapper").removeClass("d-none");
  $("#monthFilterWrapper").removeClass("d-none");

  let obraId = $("#filtroObraId").val();
  let empresaId = $("#filtroEmpresaId").val();
  let year = $("#filterYear").val();
  let month = $("#filterMonth").val();

  if (tableList != null) {
    if ($.fn.DataTable.isDataTable("#tablaCostosResumen")) {
      $("#tablaCostosResumen").DataTable().clear().destroy();
      $("#tablaCostosResumen").empty(); // <--- agrega esto
    }
    fActualizarListado(
      rutaAjax +
        `app/Ajax/ResumenCostosAjax.php?obraId=${encodeURIComponent(
          obraId
        )}&empresaId=${encodeURIComponent(
          empresaId
        )}&month=${encodeURIComponent(month)}&year=${encodeURIComponent(year)}`,
      "#tablaCostosResumen",
      parametrosTableList
    );

    // Realiza la petición para actualizar el listado de requisiciones
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
          const table = $("#tablaCostosResumen").DataTable({
            autoWidth: true,
            data: data.datos.registros,
            columns: data.datos.columnas,
            paging: false,
            info: false,
            ordering: false,
            searching: true,
            // 👉 Traducción al español
            language: {
              // Carga todas las cadenas directamente desde el CDN oficial
              url: "//cdn.datatables.net/plug-ins/2.3.2/i18n/es-ES.json",

              // (Opcional) sobrescribe alguna cadena concreta si la quieres distinta
              // emptyTable: "No hay datos para mostrar"
            },
            scrollX: true,
            initComplete: function () {
              $(".dataTables_scrollHead thead").addClass("thead-dark");
            },
          });

          $("#spanTotal").text(`${data.datos.totalGeneral}`);

          // Agrega el evento de clic a las filas
          $("#tablaCostosResumen tbody").on("click", "td", function () {
            const celda = $(this); // La celda en sí
            const indiceColumna = celda.index(); // Índice de la columna

            if (indiceColumna >= 3) {
              const fila = celda.closest("tr"); // Obtener la fila
              const valorColumna1 = fila.find("td").eq(1).text().trim(); // Índice 0 = columna 1

              // Obtener el texto del encabezado de la columna
              const encabezado = $("#tablaCostosResumen thead th")
                .eq(indiceColumna)
                .text()
                .trim();

              // Guardar los datos temporalmente si los necesitas en la consulta
              $("#miModal").data("numeroEconomico", valorColumna1);
              $("#miModal").data("fechaSeleccionada", encabezado);

              $("#miModal").modal("show");
            }
          });
        });
    }
  }
}

$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});

$(document).ready(function () {
  $("#filtroObraId, #filtroEmpresaId, #filterYear, #filterMonth").change(
    function () {
      cargarTablaConFiltros();
    }
  );

  // Evento: cuando el modal termina de abrirse
  $("#miModal").on("shown.bs.modal", function () {
    const numeroEconomico = $(this).data("numeroEconomico");
    const fechaSeleccionada = $(this).data("fechaSeleccionada");

    // Aquí haces la consulta (puede ser AJAX, fetch, etc.)
    console.log("Haciendo consulta con número económico:", numeroEconomico);
    console.log("Haciendo consulta con fechaSeleccionada:", fechaSeleccionada);

    $.ajax({
      url:
        rutaAjax +
        `app/Ajax/ResumenCostosAjax.php?noEconomico=${numeroEconomico}&fechaSeleccionada=${fechaSeleccionada}`,
      method: "GET",
      dataType: "json",
      success: function (respuesta) {
        const lista = $("#lista-resumen-costos");
        lista.empty(); // Limpiar lista previa

        if (respuesta.ordenesCompra.length === 0) {
          lista.append("<li>No se encontraron datos.</li>");
        } else {
          respuesta.ordenesCompra.forEach((item) => {
            console.log(item);
            lista.append(`
          <li>
              ${item.verOrden} - ${item.justificacion} - ${
              item.fechaCreacion
            } - Total: $${item.total ?? "N/A"}
          </li>
        `);
          });
        }
      },
      error: function () {
        $("#lista-resumen-costos").html("<li>Error al cargar datos.</li>");
      },
    });
  });
});
