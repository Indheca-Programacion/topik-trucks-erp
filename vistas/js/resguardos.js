$(function () {
  // *************************************************
  // * VARIABLES GLOBALES
  // *************************************************
  const TIEMPO_DESCARGA = 350;
  let parametrosTableList = { responsive: false };

  let elementModalBuscarInventario = document.querySelector(
    "#modalBuscarInventario"
  );

  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  let datatTablePartidaSalida = null;
  let datatTablePartidaResguardoTransferencia = null;

  let dataTableSeleccionarInventarios = $(
    "#tablaSeleccionarInventario"
  ).DataTable();

  // *************************************************
  // * TABLAS
  // *************************************************

  // TABLA INDEX
  let tableList = document.getElementById("tablaResguardos");
  // TABLA DE LAS PARTIDAS DE LAS SALIDAS
  let tableListPartidaResguardo = document.getElementById(
    "tablaResguardoPartida"
  );
  let tableListPartidaResguardoTransferencia = document.getElementById(
    "tablaPartidasResguardosTransferencias"
  );

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tableList != null)
    fAjaxDataTable(rutaAjax + "app/Ajax/ResguardoAjax.php", "#tablaResguardos");

  // FUNCION PARA OBTENER LAS PARTIDAS DE LOS RESGUARDOS
  if (tableListPartidaResguardo != null) {
    let resguardoId = $("#resguardoId").val();

    fetch(rutaAjax + "app/Ajax/ResguardoAjax.php?resguardoId=" + resguardoId, {
      method: "GET", // *GET, POST, PUT, DELETE, etc.
      cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        datatTablePartidaSalida = $("#tablaResguardoPartida").DataTable({
          info: false,
          paging: false,
          pageLength: 100,
          searching: false,
          autoWidth: false,
          data: data.datos.registros,
          columns: data.datos.columnas,
          language: LENGUAJE_DT,
          aaSorting: [],
        });
      });
  }

  // TABLA CON CHECKS E INPUTS PARA MODAL TRANSFERENCIA
  if (tableListPartidaResguardoTransferencia != null) {
    let resguardoId = $("#resguardoId").val();

    fetch(rutaAjax + "app/Ajax/ResguardoAjax.php?resguardoId=" + resguardoId, {
      method: "GET", // *GET, POST, PUT, DELETE, etc.
      cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        data.datos.columnas.pop();

        datatTablePartidaResguardoTransferencia = $(
          "#tablaPartidasResguardosTransferencias"
        ).DataTable({
          info: false,
          paging: false,
          pageLength: 100,
          searching: false,
          select: true, // Habilita la selección de filas
          autoWidth: false,
          autoWidth: false,
          data: data.datos.registros,
          columns: [
            { data: "checkbox" },
            { data: "id" },
            { data: "concepto" },
            { data: "cantidad" },
            { data: "unidad" },
            { data: "numeroParte" },
            { data: "partida" },
          ],
          columnDefs: [
            {
              targets: 0, // Columna checkbox
              orderable: false,
              className: "dt-body-center",
              render: function (data, type, row) {
                // Si la cantidad es 0, solo muestro el ícono fijo
                if (row.cantidad == 0) {
                  return '<i class="fa fa-check text-success"></i>';
                }
                // Si tiene cantidad, muestro el checkbox
                return '<input type="checkbox" class="row-checkbox">';
              },
            },
            {
              targets: 3, // Columna cantidad (input)
              render: function (data, type, row) {
                // Si cantidad = 0 → input deshabilitado
                let disabled = row.cantidad == 0 ? "disabled" : "";
                return (
                  '<input class="form-control form-control-sm cantidad" ' +
                  disabled +
                  ' min="1" max="' +
                  row.cantidad +
                  '" type="number" value="' +
                  row.cantidad +
                  '">'
                );
              },
            },
            { targets: "_all", orderable: false },
          ],
          select: {
            style: "multi",
            selector: "td:first-child",
            selectable: function (rowData) {
              return rowData.cantidadDisponible !== 0;
            },
          },
          language: LENGUAJE_DT,
          aaSorting: [],
        });
      });
  }

  // Confirmar la eliminación del resguardo
  $(tableList).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents("form");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar este Resguardo (Descripción: " +
        folio +
        ") ?",
      text: "No podrá recuperar esta información!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, quiero eliminarla!",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });

  // TABLA TRANSFERENCIAS RESGUARDO
  let tableTransferenciaResguardo = document.getElementById(
    "tablaTransferencias"
  );

  // FUNCION PARA OBTENER LAS PARTIDAS DE LOS RESGUARDOS
  if (tableTransferenciaResguardo != null) {
    let resguardoId = $("#resguardoId").val();

    fetch(
      rutaAjax +
        "app/Ajax/ResguardoAjax.php?accion=obtenerTransferenciaResguardo&resguardoId=" +
        resguardoId,
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
        tableTransferenciaResguardo = $("#tablaTransferencias").DataTable({
          info: false,
          paging: false,
          pageLength: 100,
          searching: false,
          autoWidth: false,
          data: data.datos.registros,
          columns: data.datos.columnas,
          language: LENGUAJE_DT,
          aaSorting: [],
        });
      });
  }

  // *************************************************
  // * FUNCIONES
  // *************************************************

  // Función para mostrar SweetAlert de éxito
  function mostrarExito(mensaje, callback = null) {
    Swal.fire({
      icon: "success",
      title: "¡Éxito!",
      text: mensaje || "La operación se realizó correctamente.",
      confirmButtonText: "Aceptar",
    }).then(() => {
      if (typeof callback === "function") callback();
    });
  }

  // Función para mostrar SweetAlert de error
  function mostrarError(mensaje) {
    Swal.fire({
      icon: "error",
      title: "Hubo un problema",
      text: mensaje || "Ocurrió un error en el sistema",
      confirmButtonText: "Aceptar",
    });
  }

  // Envio Pdel formulario para Crear o Editar registros
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);
    formulario.submit();
  }
  // Activar el elemento Select2
  $(".select2").select2({
    tags: false,
  });
  // input date
  $(".input-group.date").datetimepicker({
    format: "DD/MMMM/YYYY",
  }); // $('.input-group.date').datetimepicker({

  // *************************************************
  // * REFERENTE A LOS ARCHIVOS
  // *************************************************

  // Envio del formulario para Cancelar el registro
  function eliminarArchivo(btnEliminar = null) {
    if (btnEliminar == null) return;

    let archivoId = $(btnEliminar).attr("archivoId");
    // $(btnEliminar).prop('disabled', true);

    let token = $('input[name="_token"]').val();
    let resguardoId = $("#resguardoId").val();

    let datos = new FormData();
    datos.append("_token", token);
    datos.append("accion", "eliminarArchivo");
    datos.append("archivoId", archivoId);
    datos.append("resguardoId", resguardoId);

    $.ajax({
      url: rutaAjax + "app/Ajax/ResguardoAjax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        // console.log(respuesta)
        // Si la respuesta es positiva pudo eliminar el archivo
        if (respuesta.respuesta) {
          $(btnEliminar)
            .parent()
            .after(
              '<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>' +
                respuesta.respuestaMessage +
                "</div>"
            );

          $(btnEliminar).parent().parent().parent().remove();
        } else {
          $(btnEliminar)
            .parent()
            .after(
              '<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>' +
                respuesta.errorMessage +
                "</div>"
            );

          // $(btnEliminar).prop('disabled', false);
        }

        setTimeout(function () {
          $(".alert").remove();
        }, 5000);
      },
    });
  }

  $("#btnSubirArchivos").click(function () {
    document.getElementById("archivos").click();
  });

  $("#archivos").change(function () {
    let archivos = this.files;
    if (archivos.length == 0) return;

    let error = false;

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      /*==========================================
        VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
        ==========================================*/

      if (archivo["type"] != "application/pdf") {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tipo de archivo",
          text: '¡El archivo "' + archivo["name"] + '" debe ser PDF!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      } else if (archivo["size"] > 4000000) {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

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
      $("#archivos").val("");

      return;
    }

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      $("div.subir-archivos span.lista-archivos").append(
        '<p class="font-italic text-info mb-0">' + archivo["name"] + "</p>"
      );
    }

    let cloneElementArchivos = this.cloneNode(true);
    cloneElementArchivos.removeAttribute("id");
    cloneElementArchivos.name = "archivos[]";
    $("div.subir-archivos").append(cloneElementArchivos);
  });

  $("div.subir-archivos").on("click", "i.eliminarArchivo", function (e) {
    let btnEliminar = this;
    // let archivoId = $(this).attr("archivoId");
    let folio = $(this).attr("folio");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar este Archivo (Folio: " +
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
        eliminarArchivo(btnEliminar);
      }
    });
  });

  $("#btnDescargarArchivos").click(function (event) {
    event.preventDefault();

    let btnDescargarArchivos = this;
    let resguardoId = $("#resguardoId").val();

    $.ajax({
      url: `${rutaAjax}resguardos/${resguardoId}/download`,
      method: "GET",
      dataType: "json",
      beforeSend: () => {
        btnDescargarArchivos.disabled = true;
      },
    })
      .done(function (data) {
        // console.log(data);
        data.archivos.forEach((archivo, index) => {
          let link = document.createElement("a");
          // link.innerHTML = 'download file';

          link.addEventListener(
            "click",
            function (event) {
              link.href = rutaAjax + archivo.ruta;
              link.download = archivo.archivo;
            },
            false
          );

          setTimeout(() => {
            link.click();
          }, TIEMPO_DESCARGA * (index + 1));
        });
      })
      .fail(function (error) {
        console.log(error);
        console.log(error.responseJSON);
      })
      .always(function () {
        btnDescargarArchivos.disabled = false;
      });
  });

  // *************************************************
  // * REFERENTE A TRANSFERENCIA
  // *************************************************

  $("#btnTransferirResguardo").on("click", function () {
    // Mostrar el modal
    $("#transferenciaModal").modal("show");
  });

  // Al cerrar el modal, limpia el src del iframe
  $("#transferenciaModal").on("hidden.bs.modal", function () {});

  $("#enviarTransferencia").on("click", function () {
    let usuarioRecibioTransferencia = $('#usuarioRecibioTransferencia').val();
		let fechaTransferencia = $('#fechaTransferencia').val();

		if (usuarioRecibioTransferencia == '' || usuarioRecibioTransferencia == 0) {
			crearToast("bg-danger","error",'',"Se debe ingresar el nombre de la persona que recibe")
			return;
		}
    if (fechaTransferencia == '' || fechaTransferencia == 0) {
			crearToast("bg-danger","error",'',"Se debe ingresar la fecha de transferencia")
			return;
		}

    // VALIDACION PAD
    if (signaturePad.isEmpty()) {
      crearToast("bg-danger", "error", "", "Se debe ingresar la firma");
      return;
    }

    let data = [];

    $("#tablaPartidasResguardosTransferencias tbody .row-checkbox:checked").each(function () {
        let $tr = $(this).closest("tr");
        let rowData = datatTablePartidaResguardoTransferencia.row($tr).data();

        // Tomar el valor actual del input de cantidad
        let cantidadInput = $tr.find(".cantidad").val();
        rowData.cantidad = cantidadInput ? parseInt(cantidadInput, 10) : rowData.cantidad;

        data.push(rowData);
    });

    if (data.length === 0) {
      crearToast(
        "bg-danger",
        "error",
        "",
        "Se debe seleccionar al menos un registro"
      );
      return;
    }

    // obtiene la imagen de la firma
    let dataURL = signaturePad.toDataURL();
    let elementFirma = document.getElementById("firma");

    elementFirma.value = dataURL;

    let dataSend = new FormData(formulario);

    let resguardoId = $("#resguardoId").val();
    let salidaId = $("#salidaId").val();

    dataSend.append("salidaId", salidaId);
    dataSend.append("resguardoId", resguardoId);
    dataSend.append("fechaTransferencia", fechaTransferencia);
    dataSend.append("usuarioRecibioTransferencia", usuarioRecibioTransferencia);

    dataSend.append("accion", "transferirResguardo");
    dataSend.append("detalles", JSON.stringify(data));

    $.ajax({
      url: rutaAjax + "app/Ajax/ResguardoAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        if (respuesta.error === true) {
          mostrarError(respuesta.respuestaMessage);
        } else {
          mostrarExito(respuesta.respuestaMessage, () => location.reload());
        }
      })
      .fail(function (error) {
        mostrarError("Ocurrió un error al procesar la solicitud.");
      })
      .always(function () {});
  });

  // *************************************************
  // * CANVAS PARA FIRMAS
  // *************************************************

  const canvas = document.querySelector("#canvas");
  const signaturePad = new SignaturePad(canvas);

  $("#btnLimpiar").on("click", function () {
    signaturePad.clear();
  });
});
