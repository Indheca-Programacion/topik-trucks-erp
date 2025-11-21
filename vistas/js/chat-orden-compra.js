$(function () {
  /*==============================================================
	LISTADO DE MENSAJES

	FUNCIÓN ENCARGADA DE MOSTRAR LOS MENSAJES POR MEDIO
	DE AJAX SE MUESTRAN EN FORMA DE LISTA EN EL CHAT.
	==============================================================*/
  let bodyChat = document.getElementById("direct-chat-messages");

  if (bodyChat != null) {
    // EJECUTA CUANDO CARGA LA PAGINA
    obtenerMensajes();

    setInterval(function () {
      // EJECUTAR CADA 4 SEGUNDOS
      obtenerMensajes();
    }, 4000);
  }

  function obtenerMensajes() {
    const ordenCompraId = $("#ordenCompraId").val().trim();
    $.ajax({
      url:
        rutaAjax +
        `app/Ajax/MensajeOrdenCompraAjax.php?ordenCompraId=${ordenCompraId}`,
      method: "GET",
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        // Limpiar el contenedor primero (opcional)
        $("#direct-chat-messages").html("");

        if (!respuesta.mensajes || respuesta.mensajes.length === 0) {
          // Mostrar "SIN MENSAJES"
          const sinMensajesHtml = `
    <li class="list-group-item list-group-item-warning py-2 px-3">
      <span>SIN MENSAJES</span>
    </li>
  `;
          $("#direct-chat-messages").append(sinMensajesHtml);
        } else {
          respuesta.mensajes.forEach(function (mensaje) {
            let leyenda = `[${mensaje.fechaCreacion}]`;
            leyenda +=
              " " +
              (mensaje["usuarios.nombre"]
                ? mensaje["usuarios.nombre"].toUpperCase()
                : "");
            leyenda +=
              " " +
              (mensaje["usuarios.apellidoPaterno"]
                ? mensaje["usuarios.apellidoPaterno"].toUpperCase()
                : "");
            if (
              mensaje["usuarios.apellidoMaterno"] !== null &&
              mensaje["usuarios.apellidoMaterno"] !== undefined
            ) {
              leyenda +=
                " " + mensaje["usuarios.apellidoMaterno"].toUpperCase();
            }

            // Construir el mensaje HTML
            const mensajeHtml = `
      <li class="list-group-item list-group-item-success py-2 px-3">
        <span>${leyenda}</span><br>
        <span>(${mensaje.observacion})</span>
      </li>
    `;
            // Agregar el mensaje al contenedor
            $("#direct-chat-messages").append(mensajeHtml);
          });
        }
      })
      .fail(function (error) {
        $("#error-message-container").html(`
            <div class="alert alert-danger mt-2 mx-2" role="alert">
                ${
                  error.errorMessage ||
                  "Error al obtener los mensajes, llama a un administrador"
                }
            </div>
        `);
      })
      .always(function () {});
  }

  /*==============================================================
	CREACIÓN DE MENSAJES

	FUNCIÓN ENCARGADA DE LA CREACION DE LOS MENSAJES 
	MANDANDO LOS DATOS A TRAVEZ DE AJAX POR MEDIO DE 
	UNA PETICIÓN POST.
	==============================================================*/
  $("#btnCrearMensaje").click(function (e) {
    e.preventDefault(); // Evita que se recargue la página

    const mensaje = $("#mensaje").val().trim();
    const ordenCompraId = $("#ordenCompraId").val().trim();
    const _token = $("#token_id").val().trim();

    if (mensaje === "") {
      $("#mensaje-peticion").html(`
                <div class="alert alert-danger mt-2" role="alert">
                    No se ha escrito un mensaje.
                </div>
            `);
      // Limpiar después de 1 segundo (1000 ms)
      setTimeout(function () {
        $("#mensaje-peticion").html("");
      }, 1000);
    } else {
      // Si hay mensaje, limpiamos cualquier alerta anterior
      $("#mensaje-peticion").html("");

      let dataSend = new FormData();
      dataSend.append("accion", "crearMensaje");
      dataSend.append("_token", _token);
      dataSend.append("observacion", mensaje);
      dataSend.append("ordenCompraId", ordenCompraId);

      // Bloqueamos el botón y mostramos cargando
      $("#btnCrearMensaje").prop("disabled", true);
      $("#btnCrearMensaje").html(`
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...
    `);

      $.ajax({
        url: rutaAjax + "app/Ajax/MensajeOrdenCompraAjax.php",
        method: "POST",
        data: dataSend,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
      })
        .done(function (respuesta) {
          if (!respuesta.error) {
            $("#mensaje").val("");

            $("#mensaje-peticion").html(`
                            <div class="alert alert-success mt-2" role="alert">
                                ${respuesta.mensaje}
                            </div>
                        `);
            // Limpiar después de 1 segundo (1000 ms)
            setTimeout(function () {
              $("#mensaje-peticion").html("");
            }, 1000);
          }
        })
        .fail(function (error) {
          $("#mensaje-peticion").html(`
                        <div class="alert alert-danger mt-4" role="alert">
                            Error en el sistema hable con el administrador.
                        </div>
                    `);

          // Limpiar después de 1 segundo (1000 ms)
          setTimeout(function () {
            $("#mensaje-peticion").html("");
          }, 2000);
        })
        .always(function () {
          // Siempre se ejecuta: desbloquear el botón y restaurar texto
          $("#btnCrearMensaje").prop("disabled", false);
          $("#btnCrearMensaje").html(`Enviar`);
        });
    }
  });
});
