// $(function(){

	let tableList = document.getElementById('tablaActividadSemanal');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/ActividadAjax.php', '#tablaActividadSemanal');

	// $('#collapseFiltros').on('shown.bs.collapse', function (event) {
	$('#collapseFiltros').on('show.bs.collapse', function (event) {
		let btnVerFiltros = document.getElementById('btnVerFiltros');
		btnVerFiltros.querySelector('i').classList.remove("fa-eye");
		btnVerFiltros.querySelector('i').classList.add("fa-eye-slash");
	})
	
	// $('#collapseFiltros').on('hidden.bs.collapse', function (event) {
	$('#collapseFiltros').on('hide.bs.collapse', function (event) {
		let btnVerFiltros = document.getElementById('btnVerFiltros');
		btnVerFiltros.querySelector('i').classList.remove("fa-eye-slash");
		btnVerFiltros.querySelector('i').classList.add("fa-eye");
	})

	$('#btnFiltrar').on('click', function (e) {
		$(tableList).DataTable().destroy();
		tableList.querySelector('tbody').innerHTML = '';

		let empresaId = $('#filtroEmpresaId').val();
		let empleadoId = $('#filtroEmpleadoId').val();

		fAjaxDataTable(`${rutaAjax}app/Ajax/ActividadAjax.php?empresaId=${empresaId}&empleadoId=${empleadoId}`, '#tablaActividadSemanal');
	});

	// Confirmar la eliminación de la Actividad Semanal
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Actividad Semanal (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarla!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if (result.isConfirmed) {
				form.submit();
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

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// theme: 'bootstrap4'
	});
	$('.select2Add').select2({
		tags: true
		// ,theme: 'bootstrap4'
	});
	//Date picker
    $('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    });

	if ( $('#fechaInicial').length == 1 && $('#fechaInicial').val() != '' ) {
    	let fechaInicial = $('#fechaInicialDTP').datetimepicker('viewDate');
    	let fechaMinima = fechaInicial;
    	let fechaMaxima = moment(fechaMinima).add(6, 'd');

    	$('#fechaActividadDTP').datetimepicker('minDate', fechaMinima);
		$('#fechaActividadDTP').datetimepicker('maxDate', fechaMaxima);
		$('#fechaActividadDTP').datetimepicker('date', fechaMinima);
	}   

	let elementEmpresaId = $('#empresaId.select2.is-invalid');    
    let elementEmpleadoId = $('#empleadoId.select2.is-invalid');
    if ( elementEmpresaId.length == 1 ) {
		$('span[aria-labelledby="select2-empresaId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
    if ( elementEmpleadoId.length == 1 ) {
		$('span[aria-labelledby="select2-empleadoId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-empleadoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-empleadoId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-empleadoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-empleadoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}

	// Agregar Activdad
	function agregarAcividad(){
		let elementFechaInicial = document.getElementById("fechaInicial");
		let fechaInicial = elementFechaInicial.value;

		let elementPadre = null;
		let newDiv = null;
		let newContent = null;

		elementFechaInicial.classList.remove("is-invalid");
		elementPadre = elementFechaInicial.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		if ( fechaInicial == '' ) {
			elementFechaInicial.classList.add("is-invalid");
			elementPadre = elementFechaInicial.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("Debe ingresar la fecha inicial.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			$(elementFechaInicial).focus();

			return;
		}

		let elementFecha = document.getElementById("fechaActividad");
		let elementHoras = document.getElementById("horasActividad");
		let elementServicioId = document.getElementById("servicioId");
		let elementAvance = document.getElementById("avanceActividad");

		let fecha = elementFecha.value;
		let horas = elementHoras.value;
		let servicioId = elementServicioId.value;
		// let servicioText = elementServicioId.options[elementServicioId.selectedIndex].text;
		let servicioText = elementServicioId.options[elementServicioId.selectedIndex].getAttribute('folio');
		let avance = elementAvance.value.trim().toUpperCase();

		elementFecha.classList.remove("is-invalid");
		elementPadre = elementFecha.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementHoras.classList.remove("is-invalid");
		elementPadre = elementHoras.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementServicioId.classList.remove("is-invalid");
		elementPadre = elementServicioId.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);
		$('span[aria-labelledby="select2-servicioId-container"]').prop("style").removeProperty("border-color");
		$('span[aria-labelledby="select2-servicioId-container"]').prop("style").removeProperty("background-image");
		$('span[aria-labelledby="select2-servicioId-container"]').prop("style").removeProperty("background-repeat");
		$('span[aria-labelledby="select2-servicioId-container"]').prop("style").removeProperty("background-position");
		$('span[aria-labelledby="select2-servicioId-container"]').prop("style").removeProperty("background-size");

		elementAvance.classList.remove("is-invalid");
		elementPadre = elementAvance.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;

		if ( fecha == '' ) {
			elementFecha.classList.add("is-invalid");
			elementPadre = elementFecha.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("La fecha es obligatoria.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( parseFloat(horas) < 0.01 ) {
			elementHoras.classList.add("is-invalid");
			elementPadre = elementHoras.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El valor del campo Horas Trabajadas no puede ser menor a 0.01.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( horas.length > 10 ) {
			elementHoras.classList.add("is-invalid");
			elementPadre = elementHoras.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El campo Horas Trabajadas debe ser máximo de 8 dígitos.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( servicioId == '' ) {
			elementServicioId.classList.add("is-invalid");
			elementPadre = elementServicioId.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El servicio es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			$('span[aria-labelledby="select2-servicioId-container"]').css('border-color', '#dc3545');
			$('span[aria-labelledby="select2-servicioId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
			$('span[aria-labelledby="select2-servicioId-container"]').css('background-repeat', 'no-repeat');
			$('span[aria-labelledby="select2-servicioId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
			$('span[aria-labelledby="select2-servicioId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');

			errores = true;
		}

		if ( avance == '' ) {
			elementAvance.classList.add("is-invalid");
			elementPadre = elementAvance.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El avance de reparación es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( avance.length > 255 ) {
			elementAvance.classList.add("is-invalid");
			elementPadre = elementAvance.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El avance de reparación debe ser máximo de 255 caracteres.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( errores ) return;

		// elementFecha.value = '';
		elementHoras.value = '0.00';
		// elementServicioId.value = "";
		elementAvance.value = '';

		let tableActividadDetalles = document.querySelector('#tablaActividadDetalles tbody');
		let elementRow = `<tr class="font-italic" nuevo>
							<td class="text-center">
								${fecha}<input type="hidden" name="detalles[fecha][]" value="${fecha}">
								<button type='button' class='btn btn-xs btn-danger ml-1 eliminar'>
									<i class='far fa-times-circle'></i>
								</button>
							</td>
							<td>${servicioText}<input type="hidden" name="detalles[servicioId][]" value="${servicioId}"></td>
							<td>${avance}<input type="hidden" name="detalles[descripcion][]" value="${avance}"></td>
							<td class="text-right">${horas}<input type="hidden" name="detalles[horas][]" value="${horas}"></td>
						</tr>`;

		$(tableActividadDetalles).append(elementRow);

	}

	let btnAgregarActividad = document.getElementById("btnAgregarActividad");
	if ( btnAgregarActividad != null ) btnAgregarActividad.addEventListener("click", agregarAcividad);

	// Eliminar la actividad agregada (creando o editando)
	$('#tablaActividadDetalles').on("click", "button.eliminar", function (e) {
		this.parentElement.parentElement.remove();
	});

// });
