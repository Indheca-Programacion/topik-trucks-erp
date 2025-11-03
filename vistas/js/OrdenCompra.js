$(function () {
  let tableList = document.getElementById("tablaOrdenes");
  let parametrosTableList = { responsive: true };

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tableList != null)
    fActualizarListado(
      rutaAjax + "app/Ajax/OrdenCompraAjax.php",
      "#tablaOrdenes",
      parametrosTableList
    );

  // Realiza la petición para actualizar el listado de obras
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
        $(idTabla)
          .DataTable({
            autoWidth: false,
            responsive:
              parametros.responsive === undefined
                ? true
                : parametros.responsive,
            data: data.datos.registros,
            columns: data.datos.columnas,

            createdRow: function (row, rowData, index) {
              // Posición 7 corresponde a la columna 7 (índice 6 si empieza en 0)
              // Pero en DataTables, eq(7) es la octava columna (índice 7)
              // Ajusta según tu estructura de columnas
              if (
                rowData.condicion_pago &&
                rowData.condicion_pago.toLowerCase() === "contado"
              ) {
                $("td", row).eq(7).css("background-color", "#rgb(40, 167, 69)"); // verde
                $("td", row).eq(7).css("color", "#fff");
              } else {
                $("td", row)
                  .eq(7)
                  .css("background-color", "#rgb(40, 137, 241)"); // azul
                $("td", row).eq(7).css("color", "#fff");
              }
              // Cambiar el color de la columna Estatus según los valores de colorTexto y colorFondo
              if (rowData.colorTexto != "")
                $("td", row).eq(3).css("color", rowData.colorTexto);
              if (rowData.colorFondo != "")
                $("td", row).eq(3).css("background-color", rowData.colorFondo);
            },
            buttons: [
              {
                extend: "copy",
                text: "Copiar",
                className: "btn-info",
                exportOptions: { columns: ":visible:not(:last-child)" },
              },
              {
                extend: "csv",
                className: "btn-info",
                exportOptions: { columns: ":visible:not(:last-child)" },
              },
              {
                extend: "excel",
                className: "btn-info",
                exportOptions: { columns: ":visible:not(:last-child)" },
              },
              {
                extend: "pdf",
                className: "btn-info",
                exportOptions: { columns: ":visible:not(:last-child)" },
              },
              {
                extend: "print",
                text: "Imprimir",
                className: "btn-info",
                exportOptions: { columns: ":visible:not(:last-child)" },
              },
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
          .appendTo(idTabla + "_wrapper .row:eq(0)");
      }); // .then( data => {
  } // function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

  // Confirmar la eliminación de la Obra
  $(tableList).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents("form");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar esta Obra (Descripción: " +
        folio +
        ") ?",
      text: "No podrá recuperar esta información!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, quiero eliminarlo!",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });

  // $('#collapseFiltros').on('shown.bs.collapse', function (event) {
  $("#collapseFiltros").on("show.bs.collapse", function (event) {
    let btnVerFiltros = document.getElementById("btnVerFiltros");
    btnVerFiltros.querySelector("i").classList.remove("fa-eye");
    btnVerFiltros.querySelector("i").classList.add("fa-eye-slash");
  });

  // $('#collapseFiltros').on('hidden.bs.collapse', function (event) {
  $("#collapseFiltros").on("hide.bs.collapse", function (event) {
    let btnVerFiltros = document.getElementById("btnVerFiltros");
    btnVerFiltros.querySelector("i").classList.remove("fa-eye-slash");
    btnVerFiltros.querySelector("i").classList.add("fa-eye");
  });

  $("#btnFiltrar").on("click", function (e) {
    $(tableList).DataTable().destroy();
    tableList.querySelector("tbody").innerHTML = "";

    let estatusId = $("#filtroEstatusId").val();
    let obraId = $("#filtroObraId").val();
    let fechaInicial = $("#filtroFechaInicial").val();
    let fechaFinal = $("#filtroFechaFinal").val();

    if (fechaInicial == "") fechaInicial = 0;
    if (fechaFinal == "") fechaFinal = 0;

    fActualizarListado(
      `${rutaAjax}app/Ajax/OrdenCompraAjax.php?estatusId=${estatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}&obraId=${obraId}`,
      "#tablaOrdenes",
      parametrosTableList
    );
  });

  // Envio del formulario para Crear o Editar registros
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit();
  }
  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  // Activar el elemento Select2
  $(".select2").select2({
    tags: false,
    width: "100%",
    // ,theme: 'bootstrap4'
  });

  //Date picker
  $(".input-group.date").datetimepicker({
    format: "DD/MMMM/YYYY",
  });

  /*=============================================
	ACTUALIZAR IMPORTE Y TOTAL
	=============================================*/
  var campoImporteControl = document.getElementById("total");

  $(".detalleOrden").on(
    "change",
    "input[name='detalles[cantidad][]']",
    function (e) {
      var cantidad = $(this).val();
      cantidad = parseFloat(cantidad);

      var valorUnitario = $(this)
        .parent()
        .parent()
        .parent()
        .find("input[name='detalles[importeUnitario][]']")
        .val();
      valorUnitario = valorUnitario.replace(/,/g, "");
      valorUnitario = parseFloat(valorUnitario);

      var importe = $(this).parent().parent().parent().find("span.importe");
      importe.html(number_format(cantidad * valorUnitario, 2));

      actualizarImporteControl();
    }
  );

  $(".detalleOrden").on(
    "change",
    "input[name='detalles[importeUnitario][]']",
    function (e) {
      var cantidad = $(this)
        .parent()
        .parent()
        .parent()
        .find("input[name='detalles[cantidad][]']")
        .val();
      cantidad = parseFloat(cantidad);

      var valorUnitario = $(this).val();
      valorUnitario = valorUnitario.replace(/,/g, "");
      valorUnitario = parseFloat(valorUnitario);

      var importe = $(this).parent().parent().parent().find("span.importe");
      importe.html(number_format(cantidad * valorUnitario, 2));

      actualizarImporteControl();
    }
  );

  function actualizarImporteControl() {
    var importeControl = 0;

    var importes = $(".detalleOrden tbody span.importe");

    importes.each(function (indice, elemento) {
      var importe = $(elemento).text();
      importe = importe.replace(/,/g, "");
      importe = parseFloat(importe);

      importeControl = importeControl + importe;
    });

    const subtotal = document.getElementById("subtotal");
    const iva = document.getElementById("iva");
    const descuento = document.getElementById("descuento");
    const retencionIva = document.getElementById("retencionIva");
    const retencionIsr = document.getElementById("retencionIsr");

    // Subtotal es la suma de los importes
    subtotal.value = importeControl;

    // Descuento (si existe)
    let descuentoValor = 0;
    if (descuento && descuento.value !== "") {
      descuentoValor = parseFloat(descuento.value.replace(/,/g, "")) || 0;
    }

    // IVA (16% sobre subtotal - descuento)
    let baseIva = importeControl - descuentoValor;

    // Retención IVA (10.6667% sobre base IVA)
    let retencionIvaValor =
      parseFloat(retencionIva.value.replace(/,/g, "")) || 0;

    let ivaValor = parseFloat(iva.value.replace(/,/g, "")) || 0;

    let renetencionIsrValor =
      parseFloat(retencionIsr.value.replace(/,/g, "")) || 0;

    // Total = subtotal - descuento + iva - retenciones
    let total = baseIva + ivaValor - retencionIvaValor - renetencionIsrValor;

    campoImporteControl.value = total;
  }

  /*=============================================
	AGREGAR DETALLE A LA ORDEN DE COMPRA
	=============================================*/
  $(".listaProductos").on("click", "button.btnAgregarDetalle", function (e) {
    $(this).prop("disabled", true);

    var productoId = $(this).attr("productoId");

    var existe = $(".detalleOrden td[productoId='" + productoId + "']");
    if (existe.length != 0) {
      return;
    }

    $(".listaProductos button[productoId='" + productoId + "']").prop(
      "disabled",
      true
    );

    var rowProductos = $(
      ".listaProductos td[productoId='" + productoId + "']"
    ).parents("tr");

    var precioCompra = parseFloat(
      $(rowProductos).find("td.costoUnitario").html().replace(/,/g, "")
    );
    var descripcion = $(rowProductos)
      .find("td.descripcion")
      .html()
      .toUpperCase();
    var cantidad = parseFloat(
      $(rowProductos).find("td.cantidad").html().replace(/,/g, "")
    );

    var table = $(".detalleOrden tbody");

    var rows = $(".detalleOrden tbody tr");

    var id = rows.length;

    var htmlCantidad =
      '<div class="input-group"><input type="text" name="detalles[cantidad][]" value="' +
      cantidad +
      '" class="form-control form-control-sm campoSinDecimal" placeholder="Ingresa la cantidad"></div>';

    var htmlUnidad = $(rowProductos).find("td.unidad").html();

    var htmlCodigo = $(rowProductos).find("td.codigo").html();

    var htmlPartidaId =
      '<input type="hidden" name="detalles[partidaId][]" value="' +
      productoId +
      '">';

    var htmlDescripcion =
      '<input type="hidden" name="detalles[descripcion][]" value="' +
      descripcion +
      '">' +
      descripcion;

    // var valorUnitario = 0;
    var valorUnitario = precioCompra;

    var htmlValorUnitario =
      '<div class="input-group"><div class="input-group-addon"><i class="fa fa-dollar"></i></div><input type="number" name="detalles[importeUnitario][]" value="' +
      valorUnitario +
      '" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el valor unitario"></div>';

    var htmlImporte =
      '$ <span class="importe">' + cantidad * valorUnitario + "</span>";

    var htmlEliminar =
      '<button type="button" class="btn btn-xs btn-danger eliminarDetalle" productoId="' +
      productoId +
      '"><i class="fa fa-times"></i></button>';

    $(table).append(
      "<tr><td>" +
        (id + 1) +
        htmlPartidaId +
        "</td><td>" +
        htmlCantidad +
        "</td><td>" +
        htmlUnidad +
        "</td><td>" +
        htmlCodigo +
        "</td><td>" +
        htmlDescripcion +
        "</td><td>" +
        htmlValorUnitario +
        "</td><td>" +
        htmlImporte +
        "</td><td>" +
        htmlEliminar +
        "</td></tr>"
    );

    var rows = $(".detalleOrden tbody tr");

    $(rows[id]).find("td:eq(0)").css("vertical-align", "middle");
    $(rows[id]).find("td:eq(0)").attr("productoId", productoId);

    $(rows[id]).find("td:eq(1)").css("vertical-align", "middle");

    $(rows[id]).find("td:eq(2)").css("vertical-align", "middle");

    $(rows[id]).find("td:eq(3)").css("vertical-align", "middle");

    $(rows[id]).find("td:eq(4)").css("vertical-align", "middle");

    $(rows[id]).find("td:eq(5)").css("vertical-align", "middle");

    $(rows[id]).find("td:eq(6)").css("vertical-align", "middle");

    $(rows[id]).find("td:eq(7)").css("vertical-align", "middle");

    actualizarImporteControl();
  });

  /*=============================================
	ELIMINAR DETALLE DE LA ORDEN DE COMPRA
	=============================================*/
  $(".detalleOrden").on("click", "button.eliminarDetalle", function (e) {
    // var hijo = $(this).parent().parent();
    // var table = $('.detalleOrden').DataTable();

    var productoId = $(this).attr("productoId");
    var hijo = $(
      ".detalleOrden tbody td[productoId='" + productoId + "']"
    ).parent();

    // var table = $('.detalleOrden').DataTable();

    // table.row(hijo).remove();
    // table.draw();

    hijo.remove();

    $(".listaProductos button[productoId='" + productoId + "']").prop(
      "disabled",
      false
    );

    actualizarImporteControl();

    renumerarDetalle();
  });

  /*=============================================
	RENUMERAR EL # DE DETALLE
	=============================================*/
  function renumerarDetalle() {
    // var rows = $(".detalleOrden").dataTable().fnGetNodes();
    var rows = $(".detalleOrden tbody tr");

    for (var i = 0; i < rows.length; i++) {
      var td = $(rows[i]).find("td:eq(0)");
      // Guarda el input oculto
      var hiddenInput = td.find("input[type='hidden']").prop('outerHTML') || '';
      // Renumera y vuelve a poner el input oculto
      td.html((i + 1) + hiddenInput);
    }
  }

  $("#ivaPorcentaje").on("input change", function () {
    // Actualizar el valor del IVA al cambiar el porcentaje
    calcularIVA();
  });

  function calcularIVA() {
    const subtotal = parseFloat($("#subtotal").val()) || 0;
    const porcentaje = parseFloat($("#ivaPorcentaje").val()) || 0;
    const iva = subtotal * (porcentaje / 100);
    $("#iva").val(iva);
    actualizarImporteControl();
  }

  $("#proveedorId").select2({
    width: "100%",
  });

  var proveedorId = $("#proveedorId").val();

  $("#proveedorId").on("change", function () {
    const proveedorId = $(this).val();
    cargarDatosBancarios(proveedorId);
  });

  // DATOS BANCARIOS INPUT
  var proveedorId = $("#proveedorId").val();
  var datoBancarioId = $("#datoBancarioInput").val();

  if (proveedorId) {
    $("#container-dato-bancario").removeClass("d-none");

    cargarDatosBancarios(proveedorId, datoBancarioId);
  } else {
    $("#container-dato-bancario").addClass("d-none");
  }

  function cargarDatosBancarios(proveedorId, datoBancarioId = null) {
    if (!proveedorId) {
      // Si no hay proveedorId, ocultamos el contenedor y limpiamos el select
      $("#container-dato-bancario").addClass("d-none");
      $("#datoBancarioId")
        .html('<option value="">Selecciona una opción</option>')
        .trigger("change");
      return;
    }

    fetch(
      rutaAjax +
        "app/Ajax/ProveedorAjax.php?accion=selectDatosBancarios&proveedorId=" +
        proveedorId,
      {
        method: "GET",
        cache: "no-cache",
        headers: {
          "Content-Type": "application/json",
        },
      }
    )
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        $("#container-dato-bancario").removeClass("d-none");

        if (datoBancarioId) {
          let selectHTML = `<option value="">Selecciona una opción</option>`;
          data.datos.forEach((item) => {
            selectHTML += `<option value="${item.id}"${
              item.id == datoBancarioId ? " selected" : ""
            }>[${item.nombreBanco} - ${item.cuentaClave} - ${item.divisaCorto}${
              item.referencia ? " - " + item.referencia : ""
            }]</option>`;
          });
          $("#datoBancarioId").html(selectHTML);

          // Reaplica Select2 si es necesario
          $("#datoBancarioId").select2({
            width: "100%",
          });
        } else {
          let selectHTML = `<option value="">Selecciona una opción</option>`;

          data.datos.forEach((item) => {
            selectHTML += `<option value="${item.id}">[${item.nombreBanco} - ${
              item.cuentaClave
            } - ${item.divisaCorto}${
              item.referencia ? " - " + item.referencia : ""
            }]</option>`;
          });

          // Cambiar solo el contenido del select
          $("#datoBancarioId").html(selectHTML);

          // Inicializar o refrescar Select2 en el select
          $("#datoBancarioId").select2({
            width: "100%",
          });
        }
      });
  }

  $(document).ready(function () {
    var rows = $(".detalleOrden tbody tr");
    for (var i = 0; i < rows.length; i++) {
      var productoId = $(rows[i]).find("td:eq(0)").attr("productoId");
      $(".listaProductos button[productoId='" + productoId + "']").prop(
        "disabled",
        true
      );
    }
  });

  //********* FUNCION PARA OBSERVACIONES EN LAS ORDENES DE COMPRA ********/

  // Habilitar observaciones al cambiar de estatus
  $("#estatusId").change(function () {
    let actualEstatusId = $("#actualServicioEstatusId").val();
    if (actualEstatusId === "") return;

    let observacion = document.getElementById("observacion");
    if (observacion === null) return;

    if (actualEstatusId == this.value) {
      // let observacion = document.getElementById('observacion');
      $(observacion).prop("disabled", true);
      observacion.parentElement.parentElement.parentElement.classList.add(
        "d-none"
      );
    } else {
      // let observacion = document.getElementById('observacion');
      if ($(observacion).prop("disabled")) {
        $(observacion).prop("disabled", false);
        observacion.parentElement.parentElement.parentElement.classList.remove(
          "d-none"
        );
      }
    }
  });

  //********* ACTUALIZACION DE IVA, DESCUENTO, RTENECION, RETENCIONISR ********/

  $("#iva, #descuento, #retencionIva, #retencionIsr").on(
    "change",
    function (e) {
      actualizarImporteControl();
    }
  );

  /*==============================================================
	BOTON PARA VER ARCHIVOS
	==============================================================*/
  $(".verArchivo").on("click", function () {
    var archivoRuta = $(this).attr("archivoRuta");
    $("#pdfViewer").attr("src", archivoRuta);
    // Mostrar el modal
    $("#pdfModal").modal("show");
  });

  //==============================================================
  // Ver todos los archivos de la requisición
  //==============================================================
  $("#modalVerPDF").on("show.bs.modal", function () {
    // Limpiar el iframe antes de mostrar el modal
    $("#iframePDF").attr("src", "");
    let requisicionId = $('input[name="requisicionId"]').val();
    let ordenCompraId = $('input[name="ordenCompraId"]').val();

    // Abrir el modal para ver los archivos
    $("#modalVerArchivos").modal("show");

    // Cargar los archivos en la tabla
    $.ajax({
      url: `${rutaAjax}app/Ajax/OrdenCompraAjax.php?accion=verArchivos&requisicionId=${requisicionId}&ordenCompraId=${ordenCompraId}`,
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#iframePDF").attr("src", data.ruta);
      },
      error: function (error) {
        console.error("Error al cargar los archivos:", error);
      },
    });
  });

  //********* FUNCION PARA SUBIR COMPROBANTES DE PAGO ********/

  //********* FUNCIÓN DE BOTON PARA SUBIR COMPROBANTES DE PAGO ********/
  $("#btnSubirComprobantes").click(function () {
    document.getElementById("comprobanteArchivos").click();
  });

  //********* VALIDACIONES DE COMPROBANTES DE PAGO ********/
  $("#comprobanteArchivos").change(function () {
    let archivos = this.files;
    if (archivos.length == 0) return;

    let error = false;

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      //** VALIDACION PDF*/
      if (archivo["type"] != "application/pdf") {
        error = true;

        Swal.fire({
          title: "Error en el tipo de archivo",
          text: '¡El archivo "' + archivo["name"] + '" debe ser PDF!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      } else if (archivo["size"] > 4000000) {
        error = true;

        Swal.fire({
          title: "Error en el tamaño del archivo",
          text:
            '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      }
    }
    if (error) {
      $("#comprobanteArchivos").val("");
      return;
    }

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];
      $("div.subir-comprobantes span.lista-archivos").append(
        '<p class="font-italic text-info mb-0">' + archivo["name"] + "</p>"
      );
    }

    let cloneElementArchivos = this.cloneNode(true);
    cloneElementArchivos.removeAttribute("id");
    cloneElementArchivos.name = "comprobanteArchivos[]";
    $("div.subir-comprobantes").append(cloneElementArchivos);
  });

  /*==============================================================
  Asignar documentos a la orden de compra
  ==============================================================*/
  $("#btnAsignarDocumentos").click(function () {
    let ordenCompraId = $('input[id="ordenCompraId"]').val();
    let documentosSeleccionados = [];
    $('input[name="documentos[]"]:checked').each(function () {
      documentosSeleccionados.push($(this).val());
    });

    if (documentosSeleccionados.length === 0) {
      Swal.fire({
        title: "Error",
        text: "¡Debes seleccionar al menos un documento!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
      return;
    }
    $.ajax({
      url: `${rutaAjax}app/Ajax/OrdenCompraAjax.php`,
      method: "POST",
      data: {
        accion: "asignarDocumentos",
        ordenCompraId: ordenCompraId,
        documentos: documentosSeleccionados,
      },
      dataType: "json",
      success: function (data) {
        if (!data.error) {
          Swal.fire({
            title: "Éxito",
            text: data.respuestaMessage,
            icon: "success",
            confirmButtonText: "¡Cerrar!",
          }).then(() => {
            formulario.submit();
          });
        } else {
          Swal.fire({
            title: "Error",
            text: data.errorMessage,
            icon: "error",
            confirmButtonText: "¡Cerrar!",
          });
        }
      },
      error: function (error) {
        console.error("Error al asignar documentos:", error);
      },
    });
  });

  $(".archivosAsignables").change(function () {
    // Verifica si el archivo es XML
    const fileInput = this;
    const inputId = fileInput.id;
    // Busca el label asociado usando el atributo for
    const label = document.querySelector(`label[for="${inputId}"]`);
    // Si el label existe, busca la ruta del archivo dentro del label
    let archivoRuta = "";
    if (label) {
      // Busca un elemento con atributo archivoRuta o similar dentro del label, o el texto
      // Ejemplo: <label for="inputId"><span class="ruta-archivo" archivoRuta="..."></span></label>
      const rutaElem = label.querySelector("[archivoRuta]");
      if (rutaElem) {
        archivoRuta = rutaElem.getAttribute("archivoRuta");
      } else {
        // Si no hay archivoRuta, intenta extraer texto
        archivoRuta = label.textContent.trim();
      }
    }

    // Verifica si la ruta termina en .xml (ignorando mayúsculas/minúsculas)
    if (archivoRuta && archivoRuta.toLowerCase().endsWith(".xml")) {
      // Leer el archivo XML por AJAX (asumiendo que archivoRuta es accesible por HTTP)
      fetch(archivoRuta)
        .then((response) => response.text())
        .then((xmlText) => {
          try {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(xmlText, "application/xml");
            // Buscar el atributo 'folio' en el nodo Comprobante
            let folio = "";
            const comprobante = xmlDoc.querySelector("Comprobante");
            if (comprobante) {
              folio = comprobante.getAttribute("Folio") || "";
            }
            // Puedes hacer algo con el folio aquí, por ejemplo mostrarlo en consola:
            $("#folioFactura").val(folio);
          } catch (err) {
            console.error("Error al leer el XML:", err);
          }
        })
        .catch((error) => {
          console.error("No se pudo cargar el archivo XML:", error);
        });
    }
  });

  $("#addPagos").click(function () {
    Swal.fire({
      title: "Agregar pago parcial",
      html: `<div class="form-group">
          <label for="cantidadPago">Cantidad a agregar:</label>
          <input type="number" id="cantidadPago" class="form-control" min="0" step="0.01" value="0">
        </div>`,
      showCancelButton: true,
      confirmButtonText: "Agregar",
      preConfirm: () => {
        const cantidad =
          parseFloat(document.getElementById("cantidadPago").value) || 0;
        if (cantidad <= 0) {
          Swal.showValidationMessage("Ingresa una cantidad válida mayor a 0");
          return false;
        }
        return cantidad;
      },
    }).then((result) => {
      if (result.isConfirmed) {
        // Suponiendo que tienes un input con id="pagoParcial" para el valor actual
        let pagoActual = parseFloat($("#pagoParcial").val()) || 0;
        let nuevoPago = pagoActual + result.value;
        $("#pagoParcial").val(nuevoPago.toFixed(2));
      }
    });
  });

    /*==============================================================
  Polizas contables
  ==============================================================*/
  $('#btnSubirPolizasContables').click(function(){
    document.getElementById('polizasContablesArchivos').click();
  });

  $('#polizasContablesArchivos').change(function(){
    let archivos = this.files;
    if ( archivos.length == 0) return;

    let error = false;

    for (let i = 0; i < archivos.length; i++) {

        let archivo = archivos[i];
        
      /*==========================================
      VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
      ==========================================*/
      
      if ( archivo["type"] != "application/pdf" ) {

        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: 'Error en el tipo de archivo',
          text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
          icon: 'error',
          confirmButtonText: '¡Cerrar!'
        })

      } else if ( archivo["size"] > 4000000 ) {

        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: 'Error en el tamaño del archivo',
          text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
          icon: 'error',
          confirmButtonText: '¡Cerrar!'
        })

      }
      // else {

        // $("div.subir-comprobantes span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

      // }

    }

    if ( error ) {
      $("#polizasContablesArchivos").val("");

      return;
    }

    for (let i = 0; i < archivos.length; i++) {

      let archivo = archivos[i];

      $("div.subir-polizas span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

    }

		let cloneElementArchivos = this.cloneNode(true);
		cloneElementArchivos.removeAttribute('id');
		cloneElementArchivos.name = 'polizasContablesArchivos[]';
		$("div.subir-polizas").append(cloneElementArchivos);

    Swal.fire({
      title: 'Valor de Póliza',
      input: 'number',
      inputLabel: 'Ingresa el valor de la póliza',
      inputAttributes: {
      min: 0,
      step: '0.01'
      },
      inputValidator: (value) => {
      if (value === '' || value === null || isNaN(value) || parseFloat(value) <= 0) {
        return 'Ingresa un valor válido mayor a 0';
      }
      // Total disponible
      const totalEl = document.getElementById('total');
      const total = totalEl ? parseFloat((totalEl.value || '0').toString().replace(/,/g, '')) || 0 : 0;
      // Valor ya asignado a pólizas (si existe)
      const existenteEl = document.getElementById('polizasContableValor');
      const existente = existenteEl ? parseFloat((existenteEl.value || '0').toString().replace(/,/g, '')) || 0 : 0;
      const val = parseFloat(value.toString().replace(/,/g, '')) || 0;
      if (val + existente > total) {
        const disponible = (total - existente).toFixed(2);
        return `La suma (${(existente + val).toFixed(2)}) no puede ser mayor al total (${total.toFixed(2)}). Disponible: ${disponible}`;
      }
      },
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
      allowOutsideClick: false,
      allowEscapeKey: false
    }).then((result) => {
      const totalEl = document.getElementById('total');
      const total = totalEl ? parseFloat((totalEl.value || '0').toString().replace(/,/g, '')) || 0 : 0;
      const existenteEl = document.getElementById('polizasContableValor');
      const existente = existenteEl ? parseFloat((existenteEl.value || '0').toString().replace(/,/g, '')) || 0 : 0;

      if (result.isConfirmed) {
      const val = parseFloat((result.value || '0').toString().replace(/,/g, '')) || 0;
      // Revalidar por seguridad
      if (existente + val > total) {
        Swal.fire({
        title: 'Error',
        text: `La suma de valores no puede ser mayor al total (${total.toFixed(2)}). Disponible: ${(total - existente).toFixed(2)}`,
        icon: 'error',
        confirmButtonText: 'Cerrar'
        });
        // limpiar lo agregado
        $('#polizasContablesArchivos').val('');
        $('div.subir-polizas span.lista-archivos').html('');
        if (typeof cloneElementArchivos !== 'undefined' && cloneElementArchivos && cloneElementArchivos.parentNode) {
        cloneElementArchivos.remove();
        }
        return;
      }
      // Asignar la suma (existente + nuevo)
      if (existenteEl) existenteEl.value = (existente + val).toFixed(2);
      } else {
      // Si se cancela o se cierra, limpiar el input de archivos y la lista visual y remover el clone agregado
      $('#polizasContablesArchivos').val('');
      $('div.subir-polizas span.lista-archivos').html('');
      if (typeof cloneElementArchivos !== 'undefined' && cloneElementArchivos && cloneElementArchivos.parentNode) {
        cloneElementArchivos.remove();
      }
      }
    });
  });
});
