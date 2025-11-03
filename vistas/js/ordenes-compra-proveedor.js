$(function () {
  let tableList = document.getElementById("tablaOrdenes");
  let parametrosTableList = { responsive: true };

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

            createdRow: function (row, data, index) {
              if (data.colorTexto != "")
                $("td", row).eq(3).css("color", data.colorTexto);
              if (data.colorFondo != "")
                $("td", row).eq(3).css("background-color", data.colorFondo);
            },

            buttons: [
              { extend: "copy", text: "Copiar", className: "btn-info" },
              { extend: "csv", className: "btn-info" },
              { extend: "excel", className: "btn-info" },
              { extend: "pdf", className: "btn-info" },
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
  if (tableList != null)
    fActualizarListado(
      rutaAjax + "app/Ajax/OrdenCompraProveedorAjax.php",
      "#tablaOrdenes",
      parametrosTableList
    );

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

  $("#modalVerPagos").on("show.bs.modal", function (event) {
    const modal = $(this);
    const button = $(event.relatedTarget);
    const ordenId = button.data().id;
    const archivosContainer = modal.find("#archivosPagos");

    archivosContainer.html("<p>Cargando archivos...</p>");

    fetch(
      `${rutaAjax}app/Ajax/OrdenCompraProveedorAjax.php?accion=buscarArchivos&ordenId=${ordenId}`
    )
      .then((response) => response.json())
      .then((data) => {
        if (
          !data.error &&
          Array.isArray(data.archivos) &&
          data.archivos.length > 0
        ) {
          const grupos = {
            1: [],
            3: [],
          };
          data.archivos.forEach((archivo) => {
            if (archivo.tipo == 1) grupos["1"].push(archivo);
            else if (archivo.tipo == 3) grupos["3"].push(archivo);
          });

          let html = `
						<div id="archivosAccordion" role="tablist" aria-multiselectable="true">
							<div class="card">
								<div class="card-header" role="tab" id="headingComprobantes">
									<h5 class="mb-0">
										<a data-toggle="collapse" href="#collapseComprobantes" aria-expanded="true" aria-controls="collapseComprobantes">
											Comprobantes de Pago
										</a>
									</h5>
								</div>
								<div id="collapseComprobantes" class="collapse show" role="tabpanel" aria-labelledby="headingComprobantes" data-parent="#archivosAccordion">
									<div class="card-body">
										${
                      grupos["1"].length > 0
                        ? `
											<ul class="list-group">
												${grupos["1"]
                          .map(
                            (archivo) => `
													<li class="list-group-item d-flex justify-content-between align-items-center">
														<a href="${archivo.ruta}" target="_blank">${archivo.titulo}</a>
														<a href="${archivo.ruta}" download class="btn btn-sm btn-primary ms-2" title="Descargar">
															<i class="fa fa-download"></i> Descargar
														</a>
													</li>
												`
                          )
                          .join("")}
											</ul>
										`
                        : "<p>No se encontraron comprobantes de pago.</p>"
                    }
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" role="tab" id="headingFacturas">
									<h5 class="mb-0">
										<a class="collapsed" data-toggle="collapse" href="#collapseFacturas" aria-expanded="false" aria-controls="collapseFacturas">
											Facturas
										</a>
									</h5>
								</div>
								<div id="collapseFacturas" class="collapse" role="tabpanel" aria-labelledby="headingFacturas" data-parent="#archivosAccordion">
									<div class="card-body">
										${
                      grupos["3"].length > 0
                        ? `
											<ul class="list-group">
												${grupos["3"]
                          .map(
                            (archivo) => `
													<li class="list-group-item d-flex justify-content-between align-items-center">
														<a href="${archivo.ruta}" target="_blank">${archivo.titulo}</a>
														<a href="${archivo.ruta}" download class="btn btn-sm btn-primary ms-2" title="Descargar">
															<i class="fa fa-download"></i> Descargar
														</a>
													</li>
												`
                          )
                          .join("")}
											</ul>
										`
                        : "<p>No se encontraron facturas.</p>"
                    }
									</div>
								</div>
							</div>
						</div>
					`;
          archivosContainer.html(html);
        } else {
          archivosContainer.html("<p>No se encontraron archivos.</p>");
        }
      })
      .catch(() => {
        archivosContainer.html("<p>Error al cargar archivos.</p>");
      });
  });

  let ordenSelected = null;
  let codigosSelected = null;
  $("#modalAgregarFactura").on("show.bs.modal", function (event) {
    const button = $(event.relatedTarget);
    ordenSelected = button.data().id;
    const codigosStr = button.data().codigos || "";
    codigosSelected = codigosStr
      .toString()
      .split(",")
      .map((s) => s.trim())
      .filter((s) => s !== "")
      .map((s) => Number(s));
  });

  $("#modalAgregarFactura").on("hide.bs.modal", function () {
    ordenSelected = null;
    codigosSelected = null;
    $("#formAgregarFactura")[0].reset();
    $("#listaArchivos").empty();
  });

  $("#btnSubirFacturas").on("click", function () {
    const modal = $("#modalSubirFacturas");
    const form = $("#formAgregarFactura")[0];
    const formData = new FormData(form);
    formData.append("accion", "subirFacturas");
    formData.append("ordenId", ordenSelected);

    $.ajax({
      url: `${rutaAjax}app/Ajax/OrdenCompraProveedorAjax.php`,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        Swal.fire({
          title: "Subiendo archivos",
          text: "Por favor espere...",
          icon: "info",
          allowOutsideClick: false,
          showConfirmButton: false,
          willOpen: () => {
            Swal.showLoading();
          },
        });
      },
      success: function (response) {
        Swal.close();
        let data;
        try {
          data = typeof response === "string" ? JSON.parse(response) : response;
        } catch (e) {
          data = { error: true, mensaje: "Respuesta inválida del servidor." };
        }
        if (!data.error) {
          Swal.fire({
            icon: "success",
            title: "Éxito",
            text: "Archivos subidos correctamente.",
            showConfirmButton: true,
          }).then(() => {
            location.reload(); // Recargar la página para actualizar el listado
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: data.mensaje || "Error al subir archivos.",
          });
        }
      },
      error: function () {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error en la petición AJAX.",
        });
      },
    });
  });

  $('#archivoFactura').change(function () {

    const archivo = this.files[0];
    if (!archivo) return;

    // Solo procesar si es XML (por extensión o tipo)
    if (!/\.xml$/i.test(archivo.name) && !(archivo.type && archivo.type.includes("xml"))) {
      return;
    }

    // Si no hay códigos seleccionados, no hay con qué comparar
    if (!Array.isArray(codigosSelected) || codigosSelected.length === 0) {
      // opcional: limpiar el input si se desea cuando no hay códigos de referencia
      // this.value = "";
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      const text = e.target.result;
      let parser = new DOMParser();
      let xml;
      try {
      xml = parser.parseFromString(text, "application/xml");
      } catch (err) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudo leer el XML.",
      });
      $("#archivoFactura").val("");
      return;
      }

      if (xml.getElementsByTagName("parsererror").length > 0) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "XML inválido.",
      });
      $("#archivoFactura").val("");
      return;
      }

      // Buscar nodos <Concepto> independientemente del namespace
      const conceptos = [];
      const all = xml.getElementsByTagName("*");
      for (let i = 0; i < all.length; i++) {
      const node = all[i];
      if (node.localName && node.localName.toLowerCase() === "concepto") {
        // intentar obtener atributos comunes: ClaveProdServ, NoIdentificacion, ClaveProd
        let valor =
        node.getAttribute("ClaveProdServ") ||
        node.getAttribute("Clave_ProdServ") ||
        node.getAttribute("NoIdentificacion") ||
        node.getAttribute("noIdentificacion") ||
        node.getAttribute("ClaveProd") ||
        node.getAttribute("Clave");

        // también buscar dentro de tags hijos en caso de estructura diferente
        if (!valor) {
          const childClave =
            node.getElementsByTagName("ClaveProdServ")[0] ||
            node.getElementsByTagName("NoIdentificacion")[0] ||
            node.getElementsByTagName("ClaveProd")[0];
          if (childClave && childClave.textContent) valor = childClave.textContent;
        }

        if (valor) conceptos.push(valor.trim());
      }
      }

      // Normalizar a números (ya que codigosSelected son Numbers), filtrar NaN
      const conceptosNumericos = conceptos
      .map((c) => {
        // intentar convertir directamente; si contiene guiones u otros, extraer dígitos
        const n = Number(c);
        if (!isNaN(n)) return n;
        const digits = c.replace(/\D+/g, "");
        return digits === "" ? NaN : Number(digits);
      })
      .filter((n) => !isNaN(n));

      if (conceptosNumericos.length === 0) {
      Swal.fire({
        icon: "error",
        title: "Advertencia",
        text: "No se encontraron códigos de producto en el XML.",
      });
      $("#archivoFactura").val("");
      return;
      }

      // Comparar: buscar códigos que no estén en codigosSelected
      const unmatched = conceptosNumericos.filter(
      (c) => !codigosSelected.includes(c)
      );

      if (unmatched.length > 0) {
      Swal.fire({
        icon: "error",
        title: "Códigos no permitidos",
        html:
        "El XML contiene códigos de producto que no están en la lista:<br><b>" +
        codigosSelected.join(", ") +
        "</b>",
      });
      $("#archivoFactura").val("");
      // Vaciar la zona de archivos dentro del modal específico (no usar selector global si está en un modal)
      $("#listaArchivos").empty();
      return;
      }

      // Si llegó aquí, todos los códigos del XML están en codigosSelected
      // (no se requiere acción adicional; se puede mostrar confirmación opcional)
    };
    reader.onerror = function () {
      Swal.fire({
      icon: "error",
      title: "Error",
      text: "Error leyendo el archivo.",
      });
      $("#archivoFactura").val("");
    };
    reader.readAsText(archivo);

  });

  // gastos por comprobar
  const lista = document.getElementById('listaArchivos');

  $('#archivoFactura').on('change', function() {
    lista.innerHTML = '';
    Array.from(this.files).forEach((file, idx) => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.textContent = file.name;

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-danger btn-sm ml-2';
      btn.textContent = 'Eliminar';
      btn.onclick = function() {
        const dt = new DataTransfer();
        Array.from(this.files).forEach((f, i) => {
          if (i !== idx) dt.items.add(f);
        });
        this.files = dt.files;
        $(this).trigger('change');
      };

      li.appendChild(btn);
      lista.appendChild(li);
    });
  });
});
