$(function () {
  let tableList = document.getElementById("tablaEmpresas");

  let rutaApiTopickTrucks;

  if (location.hostname === "localhost" || location.hostname === "127.0.0.1") {
    rutaApiTopickTrucks = "http://127.0.0.1:8000/api";
  } else {
    rutaApiTopickTrucks = "https://topiktrucks.com/api";
  }

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  // if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/EmpresaAjax.php', '#tablaEmpresas');
  if (tableList != null)
    fAjaxDataTable(rutaAjax + "app/Ajax/EmpresaAjax.php", "#tablaEmpresas");

  // Confirmar la eliminación de la Empresa
  // $("table tbody").on("click", "button.eliminar", function (e) {
  $(tableList).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents("form");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar esta Empresa (Razón Social: " +
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
  // btnEnviar.addEventListener("click", enviar);
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  /*=============================================
	Abrir el input al presionar el logo (figure)
	=============================================*/
  $("#imgLogo").click(function () {
    document.getElementById("logo").click();
  });

  /*=============================================
	Actualizar el previsual del logo
	=============================================*/
  $("#logo").change(function () {
    var imagen = this.files[0];

    /*=============================================
		VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
		=============================================*/
    if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {
      $("#logo").val("");

      Swal.fire({
        title: "Error en el tipo de archivo",
        text: "¡La imagen debe estar en formato JPG o PNG!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else if (imagen["size"] > 2000000) {
      $("#logo").val("");

      Swal.fire({
        title: "Error en el tamaño del archivo",
        text: "¡La imagen no debe pesar más de 2MB!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else {
      var datosImagen = new FileReader();
      datosImagen.readAsDataURL(imagen);

      $(datosImagen).on("load", function (event) {
        var rutaImagen = event.target.result;
        $("#imgLogo.previsualizar").attr("src", rutaImagen);
      });
    }
  });

  /*=============================================
	Abrir el input al presionar la imágen (figure)
	=============================================*/
  $("#imgImagen").click(function () {
    document.getElementById("imagen").click();
  });

  /*=============================================
	Actualizar el previsual de la imágen
	=============================================*/
  $("#imagen").change(function () {
    var imagen = this.files[0];

    /*=============================================
		VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
		=============================================*/
    if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {
      $("#imagen").val("");

      Swal.fire({
        title: "Error en el tipo de archivo",
        text: "¡La imagen debe estar en formato JPG o PNG!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else if (imagen["size"] > 2000000) {
      $("#imagen").val("");

      Swal.fire({
        title: "Error en el tamaño del archivo",
        text: "¡La imagen no debe pesar más de 2MB!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else {
      var datosImagen = new FileReader();
      datosImagen.readAsDataURL(imagen);

      $(datosImagen).on("load", function (event) {
        var rutaImagen = event.target.result;
        $("#imgImagen.previsualizar").attr("src", rutaImagen);
      });
    }
  });

  function mostrarErroresSwal(apiErrorResponse) {
    const validationErrors = apiErrorResponse.errors;

    if (!validationErrors) {
      Swal.fire({
        icon: "error",
        title: "¡Error!",
        text:
          apiErrorResponse.message ||
          "Ocurrió un error desconocido al procesar la solicitud.",
        confirmButtonText: "Aceptar",
      });
      return;
    }

    let errorListHtml = "<ul>";

    for (const field in validationErrors) {
      if (validationErrors.hasOwnProperty(field)) {
        validationErrors[field].forEach((errorMessage) => {
          errorListHtml += `<li><strong>${field}:</strong> ${errorMessage}</li>`;
        });
      }
    }
    errorListHtml += "</ul>";

    Swal.fire({
      icon: "error",
      title: "¡Error de Validación!",
      text:
        apiErrorResponse.message ||
        "Por favor, corrige los siguientes errores:",
      html: errorListHtml,
      confirmButtonText: "Entendido",
    });
  }

  function mostrarExitoSwal(apiResponse) {
    const message =
      apiResponse.message || "La operación se completó exitosamente.";

    Swal.fire({
      icon: "success",
      title: "¡Éxito!",
      text: message,
      confirmButtonText: "Aceptar",
      timer: 3000,
    });
  }

  // LISTA DE ERRORES TIPO VALIDACIONES
  function mostrarListadoErrores(apiErrorResponse) {
    const validationErrors = apiErrorResponse.errors;

    let errorListHtml = "<ul>";

    for (const field in validationErrors) {
      if (validationErrors.hasOwnProperty(field)) {
        // Convertir string a array si no lo es
        const errors = Array.isArray(validationErrors[field])
          ? validationErrors[field]
          : [validationErrors[field]];

        errors.forEach((mensaje) => {
          errorListHtml += `<li><strong>${field}:</strong> ${mensaje}</li>`;
        });
      }
    }

    errorListHtml += "</ul>";

    Swal.fire({
      icon: "error",
      title: "Se encontraron errores",
      html: errorListHtml,
      confirmButtonText: "Entendido",
    });
  }

  // *************************************************
  // ACTUALIZAR DATOS
  // *************************************************

  $("#btnActualizarSesionPagina").on("click", function () {
    // SESIÓN
    const sesionId = parseInt($("#sesionId").val(), 10) || 0;

    if (sesionId > 0) {
      actualizarDatosSesion();
    } else {
      crearSesion();
    }
  });

  function actualizarDatosSesion() {
    let dataSend = {
      id: $("#sesionId").val(),
      password: $("#passwordSesionPagina").val(),
      password_confirmation: $("#passwordSesionPagina").val(),
    };
    $.ajax({
      url: rutaApiTopickTrucks + "/update-password-user",
      method: "POST",
      data: JSON.stringify(dataSend),
      contentType: "application/json",
      dataType: "json",
      success: function (respuesta) {
        if (respuesta) {
          const sesionId = parseInt($("#sesionId").val(), 10) || 0;
          // ACTUALIZAR AJAX
          let dataSend = new FormData();

          dataSend.append("accion", "actualizarSesion");
          dataSend.append("_token", $("#_token").val());
          dataSend.append("sesionId", sesionId);
          dataSend.append("password", $("#passwordSesionPagina").val());

          $.ajax({
            url: rutaAjax + "app/Ajax/EmpresaAjax.php",
            method: "POST",
            data: dataSend,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
          })
            .done(function (respuesta) {
              if (respuesta.error) {
                mostrarListadoErrores(respuesta);
                return;
              }

              mostrarExitoSwal(respuesta.message);
            })
            .fail(function (error) {})
            .always(function () {});
        }

        mostrarExitoSwal(respuesta);
      },
      error: function (error) {
        mostrarErroresSwal(error.responseJSON);
      },
    });
  }

  function crearSesion() {
    let dataSend = {
      name: $("#nombreCorto").val(),
      email: $("#correoSesionPagina").val(),
      password: $("#passwordSesionPagina").val(),
      password_confirmation: $("#passwordSesionPagina").val(),
    };
    $.ajax({
      url: rutaApiTopickTrucks + "/register",
      method: "POST",
      data: JSON.stringify(dataSend),
      contentType: "application/json",
      dataType: "json",
      success: function (respuesta) {
        if (respuesta) {
          const sesionId = parseInt($("#sesionId").val(), 10) || 0;
          // CREAR AJAX
          let dataSend = new FormData();

          dataSend.append("accion", "guardarSesion");
          dataSend.append("_token", $("#_token").val());
          dataSend.append("sesionId", respuesta.user.id);
          dataSend.append("empresaId", $("#empresaId").val());
          dataSend.append("name", $("#nombreCorto").val());
          dataSend.append("email", $("#correoSesionPagina").val());
          dataSend.append("password", $("#passwordSesionPagina").val());

          $.ajax({
            url: rutaAjax + "app/Ajax/EmpresaAjax.php",
            method: "POST",
            data: dataSend,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
          })
            .done(function (respuesta) {
              if (respuesta.error) {
                mostrarListadoErrores(respuesta);
                return;
              }

              mostrarExitoSwal(respuesta.message);
            })
            .fail(function (error) {})
            .always(function () {});
        }

        mostrarExitoSwal(respuesta);
      },
      error: function (error) {
        mostrarErroresSwal(error.responseJSON);
      },
    });
  }
});
