  $(function(){

	let parametrosTableList = { responsive: true };
	let elementmodalAsignarPuesto = document.querySelector('#modalAsignarPuesto');
	let elementmodalAsignarAlmacen = document.querySelector('#modalAsignarAlmacen');

  /*=============================================
  MOSTRAR TABLAS
  =============================================*/

  // TABLA USUARIOS

  let tableList = document.getElementById('tablaUsuarios');
  // LLamar a la funcion fAjaxDataTable() para llenar el Listado  
  if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/UsuarioAjax.php', '#tablaUsuarios');

  // Confirmar la eliminación del Usuario
  // $("table tbody").on("click", "button.eliminar", function (e) {
  $(tableList).on("click", "button.eliminar", function (e) {

    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents('form');

    Swal.fire({
      title: '¿Estás Seguro de querer eliminar este Usuario (Usuario: '+folio+') ?',
      text: "No podrá recuperar esta información!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, quiero eliminarlo!',
      cancelButtonText:  'No!'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    })

  });

  /*=============================================
  MOSTRAR PUESTOS ASIGNADOS
  =============================================*/


  let tableListPuesto = document.getElementById('tablaPuestos');
  
  function fActualizarListado(rutaAjax, idTabla, parametros = {}) {
    fetch(rutaAjax, {
        method: 'GET',
        cache: 'no-cache',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .catch(error => console.log('Error:', error))
    .then(data => {
        $(idTabla).DataTable({
            autoWidth: false,
            responsive: parametros.responsive === undefined ? true : parametros.responsive,
            data: data.datos.registros,
            columns: data.datos.columnas,
            paging: false, // Deshabilita la paginación
            searching: false, // Deshabilita la barra de búsqueda
            info:false,
            createdRow: function (row, data, index) {
                if (data.colorTexto != '') $('td', row).eq(3).css("color", data.colorTexto);
                if (data.colorFondo != '') $('td', row).eq(3).css("background-color", data.colorFondo);
            },
            language: LENGUAJE_DT,
            aaSorting: [],
        }).buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)'); 
    });
}

  let usuarioId = $('#usuarioId').val();

	if ( tableListPuesto != null ) fActualizarListado(rutaAjax+`app/Ajax/PuestoAjax.php?accion=puestoUsuario&id_usuario=${usuarioId}`, '#tablaPuestos', parametrosTableList);

  // BOTTON ELIMINAR PUESTO ASIGNADO
  $(tableListPuesto).on("click", "button.eliminar", function (e) {

    e.preventDefault();
    var id_puesto_usuario = $(this).attr("puesto");

    Swal.fire({
    title: '¿Estás Seguro de querer eliminar este Puesto Asignado?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, quiero eliminarlo!',
    cancelButtonText:  'No!'
    }).then((result) => {
    if (result.isConfirmed) {


      $.ajax({
        url:  rutaAjax+'app/Ajax/PuestoAjax.php' ,
        type: 'POST',
        data: {
                  accion:'eliminarPuestoAsignado',
                  id_puesto_usuario:id_puesto_usuario,
              },
        success: function(response) {
  
          // Muestra la respuesta del servidor (mensaje o error)
           crearToast('bg-success', 'Puesto eliminado con exito ', 'OK');
  
          location.reload();
          console.log(response)
          },
          error: function(xhr, status, error) {
  
            console.log(error)
  
  
          },
      });

      
    }
    })

});


  /*=============================================
  MOSTRAR ALMACENES POR ASGINADOS
  =============================================*/


  let tableListAlmacen = document.getElementById('tablaAlmacenes');
  
  function fActualizarListadoAlmacenes(rutaAjax, idTabla, parametros = {}) {
    fetch(rutaAjax, {
        method: 'GET',
        cache: 'no-cache',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .catch(error => console.log('Error:', error))
    .then(data => {
        $(idTabla).DataTable({
            autoWidth: false,
            responsive: parametros.responsive === undefined ? true : parametros.responsive,
            data: data.datos.registros,
            columns: data.datos.columnas,
            paging: false, // Deshabilita la paginación
            searching: false, // Deshabilita la barra de búsqueda
            info:false,
            createdRow: function (row, data, index) {
                if (data.colorTexto != '') $('td', row).eq(3).css("color", data.colorTexto);
                if (data.colorFondo != '') $('td', row).eq(3).css("background-color", data.colorFondo);
            },
            language: LENGUAJE_DT,
            aaSorting: [],
        }).buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)'); 
    });
}

	if ( tableListAlmacen != null ) fActualizarListadoAlmacenes(rutaAjax+`app/Ajax/AlmacenAjax.php?accion=almacenUsuario&id_usuario=${usuarioId}`, '#tablaAlmacenes', parametrosTableList);

  // BOTTON ELIMINAR ALMACEN ASIGNADO
  $(tableListAlmacen).on("click", "button.eliminarAlmacen", function (e) {

    e.preventDefault();
    var id_almacen_usuario = $(this).attr("almacen");

    Swal.fire({
    title: '¿Estás Seguro de querer eliminar este Puesto Asignado?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, quiero eliminarlo!',
    cancelButtonText:  'No!'
    }).then((result) => {
    if (result.isConfirmed) {


      $.ajax({
        url:  rutaAjax+'app/Ajax/AlmacenAjax.php' ,
        type: 'POST',
        data: {
                  accion:'eliminarAlmacenAsignado',
                  id_almacen_usuario:id_almacen_usuario,
              },
        success: function(response) {
  
          // Muestra la respuesta del servidor (mensaje o error)
           crearToast('bg-success', 'Almacen eliminado con exito ', 'OK');
  
          location.reload();
          console.log(response)
          },
          error: function(xhr, status, error) {
  
            console.log(error)
  
  
          },
      });

      
    }
    })

});

  // Envio del formulario para Crear o Editar registros
  function enviar(){
    btnEnviar.disabled = true;
    mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit(); // Enviar los datos
  }
  
  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

  // Activar el elemento Select2
  $('.select2').select2({
    tags: false
  });
  let elementEmpresaId = $('#empresaId.select2.is-invalid');
  if ( elementEmpresaId.length == 1 ) { 
    $('.select2-selection.select2-selection--single').css('border-color', '#dc3545');
    $('.select2-selection.select2-selection--single').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
    $('.select2-selection.select2-selection--single').css('background-repeat', 'no-repeat');
    $('.select2-selection.select2-selection--single').css('background-position', 'right calc(0.375em + 1.0875rem) center');
    $('.select2-selection.select2-selection--single').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
  }

  $("#foto").hide();

  /*=============================================
  Abrir el input al presionar la imágen (figure)
  =============================================*/
  $("#imgFoto").click(function(){
    document.getElementById('foto').click();
  })

  /*=============================================
  Actualizar el previsual de la imágen
  =============================================*/
  $("#foto").change(function(){

    var imagen = this.files[0];
    
    /*=============================================
      VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
      =============================================*/

      if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {

        $("#foto").val("");

        Swal.fire({
          title: 'Error en el tipo de archivo',
          text: '¡La imagen debe estar en formato JPG o PNG!',
          icon: 'error',
          confirmButtonText: '¡Cerrar!'
        })

      } else if (imagen["size"] > 2000000) {

        $("#foto").val("");

        Swal.fire({
          title: 'Error en el tamaño del archivo',
          text: '¡La imagen no debe pesar más de 2MB!',
          icon: 'error',
          confirmButtonText: '¡Cerrar!'
        })

      } else {

        var datosImagen = new FileReader;
        datosImagen.readAsDataURL(imagen);

        $(datosImagen).on("load", function(event){

        var rutaImagen = event.target.result;

        $(".previsualizar").attr("src", rutaImagen);

        })

      }

  })

  $("#firma").hide();

  /*=============================================
  Abrir el input al presionar la firma (picture)
  =============================================*/
  $("#imgFirma").click(function(){
    document.getElementById('firma').click();
  })

  /*=============================================
  Actualizar el previsual de la firma
  =============================================*/
  $("#firma").change(function(){

    let imagen = this.files[0];
    
    /*============================================
    VALIDAMOS EL FORMATO DE LA FIRMA SEA JPG O PNG
    ============================================*/

    if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {

      $("#firma").val("");

      Swal.fire({
        title: 'Error en el tipo de archivo',
        text: '¡La imagen debe estar en formato JPG o PNG!',
        icon: 'error',
        confirmButtonText: '¡Cerrar!'
      })

    } else if (imagen["size"] > 2000000) {

      $("#firma").val("");

      Swal.fire({
        title: 'Error en el tamaño del archivo',
        text: '¡La imagen no debe pesar más de 2MB!',
        icon: 'error',
        confirmButtonText: '¡Cerrar!'
      })

    } else {

      let datosImagen = new FileReader;
      datosImagen.readAsDataURL(imagen);

      $(datosImagen).on("load", function(event){

        let rutaImagen = event.target.result;

        $(".previsualizarFirma").attr("src", rutaImagen);

      })

    }

  })


  document.getElementById("formSendAsignarPuesto").addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        event.preventDefault(); // Evita que el formulario se envíe
    }
  });
  
  // BOTTON ASIGNAR PUESTO
  $('#modalAsignarPuesto button.btnAsignarPuesto').on('click',function(e){

		let elementId_Zona =  document.getElementById('id_zona')
		let elementId_Puesto =  document.getElementById('id_puesto')

		let id_zona = $('#id_zona').val()
		let id_puesto = $('#id_puesto').val()


		let elementErrorValidacion = elementmodalAsignarPuesto.querySelector('.error-validacion')
		let btnAgregar = $(this)

		let elementPadre = null;
		let newDiv = null;
		let newContent = null;

	
		elementId_Zona.classList.remove("is-invalid");
		elementPadre = elementId_Zona.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

    elementId_Puesto.classList.remove("is-invalid");
		elementPadre = elementId_Puesto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;
		if (id_zona == "") {
			elementId_Zona.classList.add("is-invalid");
			elementPadre = elementId_Zona.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			newContent = document.createTextNode("Debe escribir una opcion:");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}

    if (id_puesto == "") {
			elementId_Puesto.classList.add("is-invalid");
			elementPadre = elementId_Puesto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			newContent = document.createTextNode("Debe elegir una opcion:");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}
		if ( errores ) return;

    let token = $('input[name="_token"]').val();


    $.ajax({
      url:  rutaAjax+'app/Ajax/PuestoAjax.php' ,
      type: 'POST',
      beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
			},
      data: {
                accion:'asignarPuesto',
                _token: token,
                id_zona:id_zona,
                id_puesto:id_puesto,
                id_usuario:usuarioId
            },
      success: function(response ) {

        const respuesta = JSON.parse(response);

        if(respuesta.respuestaMessage == "Puesto asignado ya existe"){

          crearToast('bg-warning', 'Puesto ya asignado', 'OK', response.respuestaMessage);
  				$(btnAgregar).prop('disabled', false);
          return
 
        }

        // Muestra la respuesta del servidor (mensaje o error)
         crearToast('bg-success', 'Puesto asignado', 'OK', response.respuestaMessage);
         $('#modalAsignarPuesto').modal('hide');

  			location.reload();
        },
        error: function(xhr, status, error) {

        let elementList = document.createElement('p'); // prepare a new li DOM element
				let newContent = document.createTextNode("Error del sistema");
				elementList.appendChild(newContent); //añade texto al div creado.
				elementErrorValidacion.querySelector('ul').appendChild(elementList);

        console.log(error)
				$(elementErrorValidacion).removeClass("d-none");


        },
    });


	});

  /*=============================================
  ACTIVAR USUARIO
  =============================================*/
  // $(".tablas").on("click", ".btnActivarUsuario", function(){

  //  var idUsuario = $(this).attr("idUsuario");
  //  var estadoUsuario = $(this).attr("estadoUsuario");

  //  var datos = new FormData();
  //    datos.append("activarId", idUsuario);
  //    datos.append("activarUsuario", estadoUsuario);

  //    $.ajax({

  //    url:"ajax/usuarios.ajax.php",
  //    method: "POST",
  //    data: datos,
  //    cache: false,
  //       contentType: false,
  //       processData: false,
  //       success: function(respuesta){

  //       }

  //    })

  //    if(estadoUsuario == 0){

  //      $(this).removeClass('btn-success');
  //      $(this).addClass('btn-danger');
  //      $(this).html('Desactivado');
  //      $(this).attr('estadoUsuario',1);

  //    }else{

  //      $(this).addClass('btn-success');
  //      $(this).removeClass('btn-danger');
  //      $(this).html('Activado');
  //      $(this).attr('estadoUsuario',0);

  //    }

  // })

});
