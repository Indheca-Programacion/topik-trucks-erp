if (document.getElementById('demo-upload') !== null) Dropzone.autoDiscover = false;

$(function(){
	
	let tableList = document.getElementById('tablaMaquinarias');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/MaquinariaAjax.php', '#tablaMaquinarias');

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
		let maquinariaTipoId = $('#filtroMaquinariaTipoId').val();
		let ubicacionId = $('#filtroUbicacionId').val();

		fAjaxDataTable(`${rutaAjax}app/Ajax/MaquinariaAjax.php?empresaId=${empresaId}&maquinariaTipoId=${maquinariaTipoId}&ubicacionId=${ubicacionId}`, '#tablaMaquinarias');
	});

	// Confirmar la eliminación de la Maquinaria
	$("#tablaMaquinarias").on("click",".eliminar", function (e) {

		e.preventDefault();
		var folio = $(this).attr("folio");
		var form = $(this).parents('form');

		Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Maquinaria (Descripción: '+folio+') ?',
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
	let btnEnviar2 = document.getElementById("btnSend2");
	if ( btnEnviar2 != null ) btnEnviar2.addEventListener("click", enviar);


	let arrImages = [];

	if (document.getElementById('demo-upload') !== null) {

		let myDropzone = new Dropzone('#demo-upload', {
			url: rutaAjax+'app/Ajax/MaquinariaAjax.php',
			acceptedFiles: "image/jpg,image/jpeg, image/png",
			addRemoveLinks: true,
			dictRemoveFile:'Eliminar Imagen',
			dictUpload: "Subiendo",
			dictCancelUpload: "Cancelar",
			parallelUploads: 2,
			thumbnailHeight: 100,
			thumbnailWidth: 100,
			maxFilesize: 5
		});
	
		myDropzone.on('addedfile', file => {
			arrImages.push(file);
		})
		
		myDropzone.on('removedfile', file => {
			let i = arrImages.indexOf(file);
			arrImages.splice(i, 1);
		})
	}

	$('#btnGuardarImagenes').on('click', function (e) {
		e.preventDefault();
		let maquinariaId = $('#maquinariaId').val();
		let token = $('input[name="_token"]').val();
		let fecha = $('#fecha').val();

		let detalle = $('#detalle').val();
		if (detalle == '0') {
			crearToast('bg-danger','Error','', 'Debe seleccionar un detalle');
			return;
		}
		
		if (arrImages.length == 0) {
			crearToast('bg-danger','Error','', 'Debe seleccionar al menos una imagen');
			return;
		}

		if (fecha == '') {
			crearToast('bg-danger','Error','', 'Debe seleccionar una fecha');
			return;
		}

		let datos = new FormData();
		arrImages.forEach(file => {
			datos.append('images[]', file);
		});
		datos.append("_token", token);
		datos.append("maquinariaId", maquinariaId);
		datos.append("detalle", detalle);
		datos.append("fecha", fecha);
		datos.append("accion", "guardarImagenes");
		$.ajax({
			url: rutaAjax+'app/Ajax/MaquinariaAjax.php',
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta) {
				if (respuesta.respuesta) {
					crearToast('bg-success','Guardado', '', respuesta.respuestaMessage);
				} else {
					$('#msgSend').html('<span class="list-group-item list-group-item-danger">'+respuesta.errorMessage+'</span>');
				}
				window.location.reload();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#msgSend').html('<span class="list-group-item list-group-item-danger">Hubo un error al intentar grabar el registro, intente de nuevo.</span>');
				setTimeout(function(){
					$(".alert").remove();
				}, 5000);
			}
		}).always(function() {
			$('#btnGuardarImagenes').prop('disabled', false);
		});
	});

	$('.btnEliminarImagen').on('click', function (e) {
		let btnEliminar = this;
	    // let archivoId = $(this).attr("archivoId");
	    let folio = $(this).attr("data-id");

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Imagen (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarlo!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if (result.isConfirmed) {
				eliminarArchivo(btnEliminar);
			}
	    })
	});

	// Envio del formulario para Cancelar el registro
	function eliminarArchivo(btnEliminar = null){

		if ( btnEliminar == null ) return;		

		let archivoId = $(btnEliminar).attr("data-id");

		// $(btnEliminar).prop('disabled', true);

		let token = $('input[name="_token"]').val();

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarFoto");
		datos.append("archivoId", archivoId);

		$.ajax({
		    url: rutaAjax+"app/Ajax/MaquinariaAjax.php",
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

		    		$(btnEliminar).parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

		    		$(btnEliminar).parent().remove();

		    	} else {

		    		$(btnEliminar).parent().after('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

		    		// $(btnEliminar).prop('disabled', false);

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	}

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});
	$('.select2Add').select2({
		tags: true
		// ,theme: 'bootstrap4'
	});
	//Date picker
    $('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    });
	let elementEmpresaId = $('#empresaId.select2.is-invalid');
	let elementMaquinariaTipoId = $('#maquinariaTipoId.select2Add.is-invalid');
	let elementModeloId = $('#modeloId.select2Add.is-invalid');
	let elementColorId = $('#colorId.select2Add.is-invalid');
	let elementEstatusId = $('#estatusId.select2Add.is-invalid');
	let elementUbicacionId = $('#ubicacionId.select2Add.is-invalid');
	let elementAlmacenId = $('#almacenId.select2Add.is-invalid');
	if ( elementEmpresaId.length == 1 ) {
		$('span[aria-labelledby="select2-empresaId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementMaquinariaTipoId.length == 1) {
		$('span[aria-labelledby="select2-maquinariaTipoId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-maquinariaTipoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-maquinariaTipoId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-maquinariaTipoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-maquinariaTipoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementModeloId.length == 1) {
		$('span[aria-labelledby="select2-modeloId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-modeloId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-modeloId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-modeloId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-modeloId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementColorId.length == 1) {
		$('span[aria-labelledby="select2-colorId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-colorId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-colorId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-colorId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-colorId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementEstatusId.length == 1) {
		$('span[aria-labelledby="select2-estatusId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-estatusId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-estatusId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-estatusId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-estatusId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementUbicacionId.length == 1) {
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementAlmacenId.length == 1) {
		$('span[aria-labelledby="select2-almacenId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-almacenId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-almacenId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-almacenId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-almacenId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}

	// let numMarcas = 0;
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
		$('#btnAddUbicacionId').attr('disabled','disabled');
		$('#btnAddAlmacenId').attr('disabled','disabled');

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

	$(campoUbicacionId).on('change', function (e) {

		if ( agregandoCatalogo ) return;
	
		let atributo = campoUbicacionId.querySelector('option[value="'+ this.value +'"]').getAttribute('data-select2-tag');

		if ( atributo ) $('#btnAddUbicacionId').removeAttr('disabled');
		else $('#btnAddUbicacionId').attr('disabled','disabled');

	});

	$('#btnAddUbicacionId').on('click', function (e) {
	
		$('#btnAddUbicacionId').attr('disabled','disabled');
		$(campoUbicacionId).attr('disabled','disabled');

		agregandoCatalogo = true;
		ajaxEnviar(campoUbicacionId, "nombreUbicacion", rutaAjax+'app/Ajax/UbicacionAjax.php');

	});

	$(campoAlmacenId).on('change', function (e) {

		if ( agregandoCatalogo ) return;
	
		let atributo = campoAlmacenId.querySelector('option[value="'+ this.value +'"]').getAttribute('data-select2-tag');

		if ( atributo ) $('#btnAddAlmacenId').removeAttr('disabled');
		else $('#btnAddAlmacenId').attr('disabled','disabled');

	});

	$('#btnAddAlmacenId').on('click', function (e) {
	
		$('#btnAddAlmacenId').attr('disabled','disabled');
		$(campoAlmacenId).attr('disabled','disabled');

		agregandoCatalogo = true;
		ajaxEnviar(campoAlmacenId, "nombreAlmacen", rutaAjax+'app/Ajax/AlmacenAjax.php');

	});

	$('input[type="checkbox"]').on('change', function (e) {
		let name = $(this).attr('name');
		if ( $(this).is(':checked') ) {
			$('#'+name).prop("checked", true);
		} else {
			$('#'+name).prop("checked", false);
		}
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

	function createCalendar(year, month) {
		const daysInMonth = new Date(year, month + 1, 0).getDate();
		const firstDay = new Date(year, month, 1).getDay();
		const today = new Date();
	  
		const calendar = document.getElementById('calendar');
		calendar.innerHTML = '';
		
		// Crear la cabecera del calendario
		const table = document.createElement('table');
		const header = table.createTHead();
		const headerRow = header.insertRow();
		const weekdays = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
		weekdays.forEach(day => {
			const th = document.createElement('th');
			th.textContent = day;
			th.classList.add('weekday');
			th.style.padding = '15px';
			th.style.border = '1px solid #ccc';
			th.style.backgroundColor = '#f2f2f2';
		  	headerRow.appendChild(th);


		});
		
		// Llenar el calendario con los días del mes
		let date = 1;
		for (let i = 0; i < 6; i++) {
		  const row = table.insertRow();
		  for (let j = 0; j < 7; j++) {
			if (i === 0 && j < firstDay) {
			  const cell = row.insertCell();
			} else if (date > daysInMonth) {
			  break;
			} else {
			  const cell = row.insertCell();
			  cell.style.padding = '15px';
			  cell.style.border = '1px solid #ccc';
			  cell.textContent = date;
			  if (incidencias !== undefined) {
				
				if (incidencias.laborados.includes(date)) {
					cell.style.background = '#00913f';
				}
				if (incidencias.fallas.includes(date)) {
					cell.style.background = '#FF0000';
				}
				if (incidencias.paros.includes(date)) {
					cell.style.background = '#FFD300';
				}
				if (incidencias.clima.includes(date)) {
					cell.style.background = '#572364';
				}
			}

			  date++;
			}
		  }
		}
		
		calendar.appendChild(table);
	}

	$('#mes').on('change',function (params) {
		const fecha = $('#mes').val()+'-01T00:00:00'
		let currentDate = new Date(fecha);
		let maquinariaId= $('#maquinariaId').val()
		fetch( rutaAjax+'app/Ajax/GeneradorAjax.php?mes='+$('#mes').val()+'-01&maquinariaId='+maquinariaId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
			'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			incidencias = data[0];
			if(data) createCalendar(currentDate.getFullYear(), currentDate.getMonth());

		}); // .then( data => {
	})
	
	let incidencias =''
	if ( tableList == null ){
		const fecha = $('#mes').val()+'-01T00:00:00'
		let currentDate = new Date(fecha);
		let maquinariaId= $('#maquinariaId').val()
		fetch( rutaAjax+'app/Ajax/GeneradorAjax.php?mes='+$('#mes').val()+'-01&maquinariaId='+maquinariaId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
			'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			incidencias = data[0];
			if(data) createCalendar(currentDate.getFullYear(), currentDate.getMonth());

		}); // .then( data => {
	}

	$('#agregarKitMantenimiento').on('click', function (e) {
		e.preventDefault();
		let maquinariaId = $('#maquinariaId').val();
		let token = $('input[name="_token"]').val();
		let kitMantenimientoId = $('#kits').val();

		if (kitMantenimientoId == '0') {
			crearToast('bg-danger','Error','', 'Debe seleccionar un Kit de Mantenimiento');
			return;
		}
		let datos = new FormData();
		datos.append("_token", token);
		datos.append("maquinariaId", maquinariaId);
		datos.append("kitMantenimientoId", kitMantenimientoId);
		datos.append("accion", "agregarKitMantenimiento");
		$.ajax({
			url: rutaAjax+'app/Ajax/KitMantenimientoAjax.php',
			method: "POST",
			data: datos,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function(respuesta) {
				if (respuesta.error ) {
					swal.fire({
						icon: 'error',
						title: 'Error',
						text: respuesta.errorMessage,
					});
				} else {
					swal.fire({
						icon: 'success',
						title: 'Éxito',
						text: 'Kit de Mantenimiento agregado correctamente',
					}).then(() => {
						location.reload();
					});
				}
			}
			,
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				crearToast('bg-danger', 'Error', '', 'Hubo un error al intentar agregar el Kit de Mantenimiento, intente de nuevo.');
			}
		}).always(function() {
			$('#agregarKitMantenimiento').prop('disabled', false);
		});
	});

	let elementModalCrearServicio = document.querySelector('#modalCrearServicio');

	// Guardar Seguimiento
	$('#modalCrearServicio button.btnGuardar').on('click', function (e) {

		let elementErrorValidacion = elementModalCrearServicio.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");

		let btnGuardar = this;

		// Petición Ajax para crear el servicio
		let token = $('input[name="_token"]').val();

		let datos = new FormData(document.getElementById("formCrearServicioSend"));
		datos.append("_token", token);
		datos.append("accion", "crearServicio");

		$.ajax({
			url: rutaAjax+'app/Ajax/ProgramacionAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnGuardar).prop('disabled', true);
				// loading();
			}
		})
		.done(function(respuesta) {

			if ( respuesta.error ) {

				if ( respuesta.errors ) {

					// console.log(Object.keys(respuesta.errors))
					let errors = Object.values(respuesta.errors);

					// respuesta.errors.forEach( (item, index) => {
					errors.forEach( (item) => {
						let elementList = document.createElement('li'); // prepare a new li DOM element
						let newContent = document.createTextNode(item);
						elementList.appendChild(newContent); //añade texto al div creado.
						elementErrorValidacion.querySelector('ul').appendChild(elementList);
					});

				} else {

					let elementList = document.createElement('li'); // prepare a new li DOM element
					let newContent = document.createTextNode(respuesta.errorMessage);
					elementList.appendChild(newContent); //añade texto al div creado.
					elementErrorValidacion.querySelector('ul').appendChild(elementList);

				}

				$(elementErrorValidacion).removeClass("d-none");

				$(btnGuardar).prop('disabled', false);

				location.reload();
				return;

			}

			// console.log(respuesta)
			$(elementModalCrearServicio).modal('hide');
			crearToast('bg-success', 'Crear Servicio', 'OK', respuesta.respuestaMessage);

			$("#modalCrearServicio_servicioCentroId").val("");
			$('#modalCrearServicio_servicioCentroId').trigger('change.select2'); // Notify only Select2 of changes
			$('#modalCrearServicio_servicioCentroId').change();

			$("#modalCrearServicio_empresa").val("");
			$('#modalCrearServicio_empresa').trigger('change.select2'); // Notify only Select2 of changes
			$('#modalCrearServicio_empresa').change();

			$("#modalCrearServicio_horasProyectadas").val("");

			$("#modalCrearServicio_fechaProgramacion").val("");

			$("#modalCrearServicio_descripcion").val("");

			document.getElementById("btnFiltrar").click();

		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);

			let elementList = document.createElement('li'); // prepare a new li DOM element
			// let newContent = document.createTextNode(error.errorMessage);
			let newContent = document.createTextNode("Ocurrió un error al intentar crear el servicio, de favor actualice o vuelva a cargar la página e intente de nuevo");
			elementList.appendChild(newContent); //añade texto al div creado.
			// elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar crear el servicio, de favor actualice o vuelva a cargar la página e intente de nuevo");
			elementErrorValidacion.querySelector('ul').appendChild(elementList);
			$(elementErrorValidacion).removeClass("d-none");

			$(btnGuardar).prop('disabled', false);
		})
		.always(function() {
			// stopLoading();
			// $(btnGuardar).prop('disabled', false);
		});

	});

});