$(function(){

	let elementmodalAsignarPuestoMantenimiento = document.querySelector('#modalAsignarPuestoMantenimiento');


	let tableList = document.getElementById('tablaPuestoTipo');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/ConfiguracionPuestoMantenimientoAjax.php', '#tablaPuestoTipo');

	// Confirmar la eliminación del Puesto Mantenimiento
	$(tableList).on("click", "button.eliminar", function (e) {

		e.preventDefault();
		var id_puesto_mantenimiento = $(this).attr("puestoMan");
			console.log()

		Swal.fire({
		title: '¿Estás Seguro de querer eliminar este Puesto Mantenimiento Asignado '+id_puesto_mantenimiento+'?',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: '#3085d6',
		confirmButtonText: 'Sí, quiero eliminarlo!',
		cancelButtonText:  'No!'
		}).then((result) => {
		if (result.isConfirmed) {
	
		  $.ajax({
			url:  rutaAjax+'app/Ajax/ConfiguracionPuestoMantenimientoAjax.php' ,
			type: 'POST',
			data: {
					  accion:'eliminarPuestoMantenimiento',
					  id_puesto_mantenimiento:id_puesto_mantenimiento,
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

	// Envio del formulario para Crear o Editar registros
	function enviar(){
		btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviar.parentNode;
		padre.removeChild(btnEnviar);

		formulario.submit();
	}
	let formulario = document.getElementById("formSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	  // BOTTON ASIGNAR PUESTO
	  $('#modalAsignarPuestoMantenimiento button.btnAsignarPuestoMantenimiento').on('click',function(e){

		let elementId_MantenimientoTipo =  document.getElementById('id_mantenimiento_tipo')
		let elementId_Puesto =  document.getElementById('id_puesto')

		let id_MantenimientoTipo = $('#id_mantenimiento_tipo').val()
		let id_puesto = $('#id_puesto').val()


		let elementErrorValidacion = elementmodalAsignarPuestoMantenimiento.querySelector('.error-validacion')
		let btnAgregar = $(this)

		let elementPadre = null;
		let newDiv = null;
		let newContent = null;

	
		elementId_MantenimientoTipo.classList.remove("is-invalid");
		elementPadre = elementId_MantenimientoTipo.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

    elementId_Puesto.classList.remove("is-invalid");
		elementPadre = elementId_Puesto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;
		if (id_MantenimientoTipo == "") {
			elementId_MantenimientoTipo.classList.add("is-invalid");
			elementPadre = elementId_MantenimientoTipo.parentElement;
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

		let token = $('#token').val()


    $.ajax({
      url:  rutaAjax+'app/Ajax/ConfiguracionPuestoMantenimientoAjax.php' ,
      type: 'POST',
      beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
			},
      data: {
                accion:'asignarPuestoMantenimiento',
                _token: token,
                id_MantenimientoTipo:id_MantenimientoTipo,
                id_puesto:id_puesto,
            },
      success: function(response ) {

        const respuesta = JSON.parse(response);

        if(respuesta.respuestaMessage == "La asignacion ya existe"){

          crearToast('bg-warning', 'Ya existe la asignacion', 'OK', response.respuestaMessage);
  				$(btnAgregar).prop('disabled', false);
          return
        }

        // Muestra la respuesta del servidor (mensaje o error)
         crearToast('bg-success', 'Puesto tipo mantenimiento asignado', 'OK', response.respuestaMessage);
         $('#modalAsignarPuestoMantenimiento').modal('hide');

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
});