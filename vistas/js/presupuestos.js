$(function(){

	let tableList = document.getElementById('tablaPresupuestos');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/PresupuestoAjax.php', '#tablaPresupuestos');

	// Confirmar la eliminación del Color
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var puesto = $(this).attr("puesto");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Puesto (Descripción: '+puesto+') ?',
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

	$('.select2').select2();

	$('.select2Add').select2({
	tags: true
	// ,theme: 'bootstrap4'
});

	$('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    });

	let minDate = moment().subtract(12, 'months')
	let maxDate = moment().add(1, 'hours');

	$('#fechaSolicitudDTP').datetimepicker('minDate', minDate);
	$('#fechaSolicitudDTP').datetimepicker('maxDate', maxDate);

	minDate = $('#fechaSolicitudDTP').datetimepicker('viewDate');


	$('#btnSiguiente').on('click', function () {
        
        if ($('#maquinariaId').val() == "") {
            Swal.fire({
                title: 'Error',
                text: 'Debe seleccionar una maquinaria antes de continuar.',
                icon: 'error',
                confirmButtonText: 'Cerrar'
            });
            return;
        }

		if ($('#clienteId').val() == "") {
			Swal.fire({
				title: 'Error',
				text: 'Debe seleccionar un cliente antes de continuar.',
				icon: 'error',
				confirmButtonText: 'Cerrar'
			});
			return;
		}

        let step1 = document.getElementById("formulario-step-1");
        let step2 = document.getElementById("formulario-step-2");
        let step3 = document.getElementById("formulario-step-3");

        let step2Trigger = document.getElementById("stepper1trigger2");
        let step3Trigger = document.getElementById("stepper1trigger3");

        let btnAnterior = document.getElementById("btnAnterior");
        let btnSiguiente = document.getElementById("btnSiguiente");
		let btnAgregarServicio = document.getElementById("btnAgregarServicio");

        if (step2.classList.contains("d-none")) {
            step1.classList.add("d-none");
            step2.classList.remove("d-none");
            step3.classList.add("d-none");

			btnAgregarServicio.classList.remove("d-none");
            btnAnterior.classList.remove("d-none");

            step2Trigger.classList.add("active");
        } else if (step3.classList.contains("d-none")) {
            step1.classList.add("d-none");
            step2.classList.add("d-none");
            step3.classList.remove("d-none");

            btnSiguiente.classList.add("d-none");
			btnAnterior.classList.remove("d-none");
			btnAgregarServicio.classList.add("d-none");

            step3Trigger.classList.add("active");

			let tablaServiciosPresupuesto = document.getElementById("tablaServiciosPresupuesto");
			let tbody = tablaServiciosPresupuesto.querySelector("tbody");
			tbody.innerHTML = "";

			let descripcionInputs = $('#accordionLevantamientos textarea[name="descripcion[]"]');
			descripcionInputs.each(function(index) {
				let descripcion = $(this).val();
				let row = `
					<tr>
						<td>${index + 1}</td>
						<td>${descripcion}</td>
					</tr>
				`;
				$(tbody).append(row);
			});

			$('#btnSend').removeClass("d-none");
        }

    });

    $('#btnAnterior').on('click', function () {
        
        let step1 = document.getElementById("formulario-step-1");
        let step2 = document.getElementById("formulario-step-2");
        let step3 = document.getElementById("formulario-step-3");

        let step2Trigger = document.getElementById("stepper1trigger2");
        let step3Trigger = document.getElementById("stepper1trigger3");

        let btnSiguiente = document.getElementById("btnSiguiente");
        let btnAnterior = document.getElementById("btnAnterior");
		let btnAgregarServicio = document.getElementById("btnAgregarServicio");

        if (step2.classList.contains("d-none") ) {
            step2.classList.remove("d-none");
            step3.classList.add("d-none");

            btnSiguiente.classList.remove("d-none");
			btnAgregarServicio.classList.add("d-none");

            step3Trigger.classList.remove("active");
			
        } else {
            step1.classList.remove("d-none");
            step2.classList.add("d-none");
            btnSiguiente.removeAttribute("disabled");
			btnAnterior.classList.add("d-none");

            step2Trigger.classList.remove("active");
            step3Trigger.classList.remove("active");
        }
		$('#btnSend').addClass("d-none");

        // Ocultar el botón anterior si estamos en el step 1
        if (!step1.classList.contains("d-none")) {
            btnAnterior.classList.add("d-none");
			btnAgregarServicio.classList.add("d-none");
        }
    });

	/*==============================================
	Abrir el input al presionar el botón Subir Fotos
	==============================================*/
	$(document).on('click', '.btnSubirFotos', function() {
		let dataId = $(this).data('id');
		document.getElementById('imagenes_' + dataId).click();
	})

	/*===========================================================
	Validar tipo y tamaño de las imágenes (Evidencia fotográfica)
	===========================================================*/
	$(document).on('change', 'input[type="file"][id^="imagenes"]', function() {

		let inputFile = $(this);
		let servicioContainer = inputFile.closest('.servicios-levantamiento');
		let previewContainer = servicioContainer.find('span.previsualizar');
		
		previewContainer.html('');
		let archivos = this.files;
		let error = false;

		for (let i = 0; i < archivos.length; i++) {

			let archivo = archivos[i];

			/*================================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA JPG O PNG
			================================================*/
			
			if ( archivo["type"] != "image/jpeg" && archivo["type"] != "image/png" ) {

				error = true;

				Swal.fire({
					title: 'Error en el tipo de archivo',
					text: '¡El archivo "'+archivo["name"]+'" debe ser JPG o PNG!',
					icon: 'error',
					confirmButtonText: '¡Cerrar!'
				})

			} else {

				let datosImagen = new FileReader;
				datosImagen.readAsDataURL(archivo);

				$(datosImagen).on("load", function(event) {
					let rutaImagen = event.target.result;

					let elementPicture = `<picture>
											<img src="${rutaImagen}" class="img-fluid img-thumbnail" style="width: 100%">
										</picture>
										<p class="font-italic text-info mb-0">${archivo["name"]}</p>`;
					previewContainer.append(elementPicture);
				})

			}

		}

		if ( error ) {
			inputFile.val("");

			setTimeout(() => {
				previewContainer.html('');
			}, 1000);
		}

	})

	let modalVerImagenes = document.querySelector('#modalVerImagenes');
	/*==============================================================
	Visualizar las imágenes	
	==============================================================*/
	$("#verImagenes").click(function(e) {

		let verBotonEliminar = this.getAttribute('verBotonEliminar');
		let folio = this.getAttribute('folio').toUpperCase();
		$("#modalVerImagenesLabel span").html(folio);
		$("#modalVerImagenes div.imagenes").html('');

		let token = $('input[name="_token"]').val();
		let servicioId = $(this).attr("servicioId");

		let datos = new FormData();
		datos.append("accion", "verImagenes");
		datos.append("_token", token);
		datos.append("servicioId", servicioId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/ServicioAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

				if ( respuesta.error ) {
					let elementErrorValidacion = modalVerImagenes.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				respuesta.imagenes.forEach( (imagen, index) => {
					let elementImagen = ( verBotonEliminar ) === 'true'
						? `<div class="col mb-4">
								<div class="card">
									<i class="mt-1 mr-1 p-1 fas fa-trash-alt fa-lg text-danger eliminarImagen" style="cursor: pointer; position: absolute; top: 0; right: 0; background-color: rgba(255, 255, 255, 0.1);" servicioId="${servicioId}" imagenId="${imagen.id}" folio="${imagen.titulo}"></i>
									<img src="${imagen.ruta}" class="card-img-top" alt="${imagen.titulo}">
								</div>
							</div>`
						: `<div class="col mb-4">
								<div class="card">
									<img src="${imagen.ruta}" class="card-img-top" alt="${imagen.titulo}">
								</div>
							</div>`;

					$("#modalVerImagenes div.imagenes").append(elementImagen);
				});

		    }
		}) // $.ajax({

	})

	// Confirmar la eliminación de las Imágenes
	$("#modalVerImagenes div.imagenes").on("click", "i.eliminarImagen", function (e) {

		let btnEliminar = this;
	    let folio = $(this).attr("folio");

	    Swal.fire({
			title: '¿Estás seguro de querer eliminar esta Foto (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarla!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if ( result.isConfirmed ) eliminarImagen(btnEliminar);
	    })

	});

	// Envio del formulario para Eliminar la imágen
	function eliminarImagen(btnEliminar = null){

		if ( btnEliminar == null ) return;

		let token = $('input[name="_token"]').val();
		let imagenId = $(btnEliminar).attr("imagenId");
		let servicioId = $(btnEliminar).attr("servicioId");

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarImagen");
		datos.append("imagenId", imagenId);
		datos.append("servicioId", servicioId);

		$.ajax({
		    url: rutaAjax+"app/Ajax/ServicioAjax.php",
		    method: "POST",
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta){

		    	// console.log(respuesta)
		    	// Si la respuesta es positiva pudo eliminar el archivo
		    	if (respuesta.respuesta) {

		    		$(btnEliminar).parent().parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

		    		$(btnEliminar).parent().parent().remove();

		    		let btnVerImagenes = document.getElementById("verImagenes");
		    		let elementSpan = btnVerImagenes.querySelector('span');
		    		let cantidadImagenes = elementSpan.innerHTML;
		    		btnVerImagenes.querySelector('span').innerHTML = --cantidadImagenes;

		    		if ( cantidadImagenes == 0 ) {
		    			btnVerImagenes.removeChild(elementSpan);
		    			$(btnVerImagenes).prop('disabled', true);
		    		}

		    	} else {

		    		$(btnEliminar).parent().after('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	}

	$('#btnAgregarServicio').on('click', function () {
		let accordionLevantamientos = document.getElementById('accordionLevantamientos');
		let primerServicio = accordionLevantamientos.querySelector('.servicios-levantamiento').cloneNode(true);

		// Actualizar índices y IDs del nuevo servicio
		let servicios = accordionLevantamientos.querySelectorAll('.servicios-levantamiento');
		
		let nuevoIndice = servicios.length+1;

		// Colapsar todos los servicios anteriores
		servicios.forEach(servicio => {
			let collapse = servicio.querySelector('.collapse');
			$(collapse).collapse('hide');
		});

		// Actualizar atributos del collapse
		let collapseDiv = primerServicio.querySelector('.collapse');
		let headingDiv = primerServicio.querySelector('[id^="heading"]');
		let botonCollapse = primerServicio.querySelector('button[data-target]');
		let botonEliminar = document.createElement('button');
		botonEliminar.type = 'button';
		botonEliminar.className = 'btn btn-danger btn-sm ml-2 eliminarServicio';
		botonEliminar.innerHTML = '<i class="fas fa-trash"></i>';

		headingDiv.id = `heading${nuevoIndice}`;
		collapseDiv.id = `collapse${nuevoIndice}`;
		botonCollapse.setAttribute('data-target', `#collapse${nuevoIndice}`);
		botonCollapse.setAttribute('aria-controls', `collapse${nuevoIndice}`);
		botonCollapse.innerHTML = `Levantamiento Reparacion / Servicio ${nuevoIndice}`;
		
		headingDiv.querySelector('.mb-0').appendChild(botonEliminar);

		// Actualizar data-id del botón btnSubirFotos
		let btnSubirFotos = primerServicio.querySelector('.btnSubirFotos');
		if (btnSubirFotos) {
			btnSubirFotos.setAttribute('data-id', nuevoIndice);
		}

		// Limpiar valores de los inputs y actualizar name de imagenes[]
		primerServicio.querySelectorAll('input, textarea, select').forEach(input => {
			if (input.type !== 'hidden') {
				input.value = '';
				// Actualizar el name del input file para separar por servicio
				if (input.type === 'file') {
					input.name = `imagenes_${nuevoIndice}[]`;
					input.id = `imagenes_${nuevoIndice}`;
				}
			}
		});

		// Limpiar previsualización de imágenes si existe
		let previsualizar = primerServicio.querySelector('.previsualizar');
		if (previsualizar) {
			previsualizar.innerHTML = '';
		}

		// Agregar el nuevo servicio al accordion
		accordionLevantamientos.appendChild(primerServicio);

		// Mostrar el nuevo collapse
		$(collapseDiv).collapse('show');
	});

	$('.eliminarServicio').on('click', function () {
		let servicioToDelete = this.closest('.servicios-levantamiento');
		$(servicioToDelete).remove();
	});

	// Delegar el evento para eliminar servicios dinámicamente
	$('#accordionLevantamientos').on('click', '.eliminarServicio', function () {
		let servicioToDelete = this.closest('.servicios-levantamiento');
		$(servicioToDelete).remove();
	});

	/*==============================================================
	Agregar nuevo cliente desde el modal	
	==============================================================*/
	$('#btnGuardarCliente').on('click', function (e) {
		e.preventDefault();
		guardarClienteModal();
	});

	function guardarClienteModal() {
		let formularioCliente = $('#formSendCliente');
		let msgSendCliente = $('#msgSendCliente');
		let btnGuardarCliente = $('#btnGuardarCliente');

		// btnGuardarCliente.prop('disabled', true);
		msgSendCliente.html("<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>");
		$.ajax({
			url: rutaAjax+'app/Ajax/ClienteAjax.php',
			method: 'POST',
			data: formularioCliente.serialize(),
			dataType: "json",
			success:function(respuesta) {
				if ( respuesta.error ) {
					let elementErrorValidacion = msgSendCliente;
					elementErrorValidacion.html('<div class="alert alert-danger alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button><ul><li>'+respuesta.errorMessage+'</li></ul></div>');
					// btnGuardarCliente.prop('disabled', false);
					return;
				}
				// Agregar el nuevo cliente al select
				let clienteSelect = $('#clienteId');
				clienteSelect.append(new Option(respuesta.cliente.nombre, respuesta.cliente.id, true, true));
				clienteSelect.trigger('change');
				// Cerrar el modal
				$('#modalAgregarCliente').modal('hide');
				// Resetear el formulario
				formularioCliente[0].reset();
				btnGuardarCliente.prop('disabled', false);
				msgSendCliente.html('');

				crearToast('bg-success', 'Cliente agregado correctamente.', '', 'Se ha agregado un nuevo cliente al sistema.');
			}
		})
	}

	/*==============================================================
	Agregar nueva maquinaria desde el modal	
	==============================================================*/
	$('#btnGuardarMaquinaria').on('click', function (e) {
		e.preventDefault();
		guardarMaquinariaModal();
	});

	function guardarMaquinariaModal() {
		let formularioMaquinaria = $('#formSendMaquinaria');
		let msgSendMaquinaria = $('#msgSendMaquinaria');
		let btnGuardarMaquinaria = $('#btnGuardarMaquinaria');
		// btnGuardarMaquinaria.prop('disabled', true);
		msgSendMaquinaria.html("<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>");
		$.ajax({
			url: rutaAjax+'app/Ajax/MaquinariaAjax.php',
			method: 'POST',
			data: formularioMaquinaria.serialize(),
			dataType: "json",
			success:function(respuesta) {
				if ( respuesta.error ) {
					let listaErrores = '';
					if ( respuesta.errors){
						Object.values(respuesta.errors).forEach( function(valor) {
							listaErrores += '<li>'+valor+'</li>';
						});
					}

					let elementErrorValidacion = msgSendMaquinaria;
					elementErrorValidacion.html('<div class="alert alert-danger alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button><ul>'+listaErrores+'</ul></div>');
					// btnGuardarMaquinaria.prop('disabled', false);
					return;
				}
				// Agregar la nueva maquinaria al select
				let maquinariaSelect = $('#maquinariaId');
				maquinariaSelect.append(new Option(respuesta.maquinaria.descripcion, respuesta.maquinaria.id, true, true));
				maquinariaSelect.trigger('change');
				// Cerrar el modal
				$('#modalAgregarMaquinaria').modal('hide');
				// Resetear el formulario
				formularioMaquinaria[0].reset();
				btnGuardarMaquinaria.prop('disabled', false);
				msgSendMaquinaria.html('');
				crearToast('bg-success', 'Maquinaria agregada correctamente.', '', 'Se ha agregado una nueva maquinaria al sistema.');
			}
		})
	}

	let campoMaquinariaTipoId = document.getElementById('maquinariaTipoId');
	let campoMarcaId = document.getElementById('marcaId');
	let campoModeloId = document.getElementById('modeloId');
	let campoColorId = document.getElementById('colorId');
	let campoEstatusId = document.getElementById('estatusId');
	let campoUbicacionId = document.getElementById('ubicacionId');
	let campoAlmacenId = document.getElementById('almacenId');
	
	let agregandoCatalogo = false;

	$(document).ready(function(){

		// Deshabilitar todos los botones de Agregar Catalogos
		$('#btnAddMaquinariaTipoId').attr('disabled','disabled');
		$('#btnAddMarcaId').attr('disabled','disabled');
		// $(campoModeloId).attr('disabled','disabled'); // Se habilita cuando está seleccionada una Marca
		$('#btnAddModeloId').attr('disabled','disabled');
		$('#btnAddColorId').attr('disabled','disabled');
		$('#btnAddEstatusId').attr('disabled','disabled');

	});

	$(campoMaquinariaTipoId).on('change', function (e) {

		if ( agregandoCatalogo ) return;
	
		let atributo = campoMaquinariaTipoId.querySelector('option[value="'+ this.value +'"]').getAttribute('data-select2-tag');

		if ( atributo ) $('#btnAddMaquinariaTipoId').removeAttr('disabled');
		else $('#btnAddMaquinariaTipoId').attr('disabled','disabled');

	});

	$('#btnAddMaquinariaTipoId').on('click', function (e) {
	
		$('#btnAddMaquinariaTipoId').attr('disabled','disabled');
		$(campoMaquinariaTipoId).attr('disabled','disabled');

		agregandoCatalogo = true;
		ajaxEnviar(campoMaquinariaTipoId, "nombreMaquinariaTipo", rutaAjax+'app/Ajax/MaquinariaTipoAjax.php');

	});

	$(campoMarcaId).on('change', function (e) {

		if ( agregandoCatalogo ) return;

		// Eliminar los tags nuevos que no se hayan intentado grabar
		// newTags.forEach(function callback(currentValue, index, array) {
		// 	if ( valorActual != currentValue.value ) valueTagEliminar = currentValue.value;
		// });
		// if ( valueTagEliminar != null ) campoMarcaId.querySelector('option[value="'+ valueTagEliminar +'"]').remove();
	
		let atributo = campoMarcaId.querySelector('option[value="'+ this.value +'"]').getAttribute('data-select2-tag');

		if ( atributo ) $('#btnAddMarcaId').removeAttr('disabled');
		else {
			$('#btnAddMarcaId').attr('disabled','disabled');

			// Consultar los modelos de la marca seleccionada
			campoModeloId.innerHTML = '<option value="">Selecciona un Modelo</option>';
			if ( campoMarcaId.value != '' ) {

			  	fetch( rutaAjax+'app/Ajax/ModeloAjax.php?marcaId='+campoMarcaId.value, {
					method: 'GET', // *GET, POST, PUT, DELETE, etc.
					cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
					headers: {
					'Content-Type': 'application/json'
					}
			  	} )
				.then( response => response.json() )
				.catch( error => console.log('Error:', error) )
				.then( data => {
					data.datos.modelos.forEach(function callback(currentValue, index, array) {
						$(campoModeloId).append('<option value="'+currentValue.id+'">'+currentValue.descripcion+'</option>');
					});
				}); // .then( data => {

			} // if ( campoMarcaId.value != '' )

		};

	});

	$('#btnAddMarcaId').on('click', function (e) {
	
		$('#btnAddMarcaId').attr('disabled','disabled');
		$(campoMarcaId).attr('disabled','disabled');

		agregandoCatalogo = true;
		ajaxEnviar(campoMarcaId, "nombreMarca", rutaAjax+'app/Ajax/MarcaAjax.php');

	});

	$(campoModeloId).on('change', function (e) {

		if ( agregandoCatalogo ) return;
	
		let atributo = campoModeloId.querySelector('option[value="'+ this.value +'"]').getAttribute('data-select2-tag');

		// if ( atributo ) $('#btnAddModeloId').removeAttr('disabled');
		if ( atributo && campoMarcaId.value != '' ) $('#btnAddModeloId').removeAttr('disabled');
		else $('#btnAddModeloId').attr('disabled','disabled');

	});

	$('#btnAddModeloId').on('click', function (e) {
	
		$('#btnAddModeloId').attr('disabled','disabled');
		$(campoModeloId).attr('disabled','disabled');

		agregandoCatalogo = true;
		ajaxEnviar(campoModeloId, "nombreModelo", rutaAjax+'app/Ajax/ModeloAjax.php');

	});

	$(campoColorId).on('change', function (e) {

		if ( agregandoCatalogo ) return;
	
		let atributo = campoColorId.querySelector('option[value="'+ this.value +'"]').getAttribute('data-select2-tag');

		if ( atributo ) $('#btnAddColorId').removeAttr('disabled');
		else $('#btnAddColorId').attr('disabled','disabled');

	});

	$('#btnAddColorId').on('click', function (e) {
	
		$('#btnAddColorId').attr('disabled','disabled');
		$(campoColorId).attr('disabled','disabled');

		agregandoCatalogo = true;
		ajaxEnviar(campoColorId, "nombreColor", rutaAjax+'app/Ajax/ColorAjax.php');

	});

	$(campoEstatusId).on('change', function (e) {

		if ( agregandoCatalogo ) return;
	
		let atributo = campoEstatusId.querySelector('option[value="'+ this.value +'"]').getAttribute('data-select2-tag');

		if ( atributo ) $('#btnAddEstatusId').removeAttr('disabled');
		else $('#btnAddEstatusId').attr('disabled','disabled');

	});

	$('#btnAddEstatusId').on('click', function (e) {
	
		$('#btnAddEstatusId').attr('disabled','disabled');
		$(campoEstatusId).attr('disabled','disabled');

		agregandoCatalogo = true;
		ajaxEnviar(campoEstatusId, "nombreEstatus", rutaAjax+'app/Ajax/EstatusAjax.php');

	});

	
	function ajaxEnviar(objetoCampo, nombreCampoPost, rutaUrl){

		let token = $('input[name="_token"]').val();
		var nombreValor = objetoCampo.value;

		let datos = new FormData();
		datos.append("_token", token);
		// Agregar el valor de la MarcaId
		if ( objetoCampo.getAttribute('name') == 'modeloId' ) datos.append('marcaId', campoMarcaId.value);
		datos.append(nombreCampoPost, nombreValor);

		$.ajax({
		    url: rutaUrl,
		    method: "POST",
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success: function(respuesta) {

		    	// Si la respuesta es positiva pudo grabar el nuevo registro
		    	if (respuesta.respuesta) {

		    		let respuestaId = respuesta.respuesta["id"];

		    		$(objetoCampo).parent().after('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

					let lastOption = objetoCampo.lastChild;
					$(lastOption).after('<option value="'+respuestaId+'" selected>'+nombreValor+'</option>');

		    	} else {

		    		$(objetoCampo).parent().after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

		    		$(objetoCampo).val(null).trigger('change');

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    	$(objetoCampo).removeAttr('disabled');

		    	agregandoCatalogo = false;

		    },
			error: function(XMLHttpRequest, textStatus, errorThrown) {

				$(objetoCampo).parent().after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>Hubo un error al intentar grabar el registro, intente de nuevo.</div>');

		    	$(objetoCampo).val(null).trigger('change');

		    	setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    	$(objetoCampo).removeAttr('disabled');

		    	agregandoCatalogo = false;

			}
		})

	}


});