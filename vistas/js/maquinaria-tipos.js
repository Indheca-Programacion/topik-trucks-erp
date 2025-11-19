$(function(){

	let tableList = document.getElementById('tablaMaquinariaTipos');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/MaquinariaTipoAjax.php', '#tablaMaquinariaTipos');

	// Confirmar la eliminación del Tipo de Maquinaria
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Tipo de Maquinaria (Descripción: '+folio+') ?',
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
	
	$('.select2').select2({
		tags: false,
		width: '93%'
		// ,theme: 'bootstrap4'
	});

	$('.select2Add').select2({
		tags: true,
		width: '93%'
		// ,theme: 'bootstrap4'
	});

	// Agregar Tarea
	$('#btnAddChecklist').on('click', function(e){
		
		let form = $('#formAddChecklist');
		let formData = new FormData(form[0]);
		formData.append('accion', 'addChecklist');

		$.ajax({
			url: rutaAjax+"app/Ajax/MaquinariaTipoAjax.php",
			data: formData,
			method: 'POST',
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
			success: function(response){
				
				if (!response.error) {
					Swal.fire({
						icon: 'success',
						title: 'Éxito',
						text: response.message
					}).then(() => {
						location.reload();
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: Object.values(response.errors).join('\n')
					});
				}
			},
			error: function(xhr, status, error){
				console.error(xhr);
				alert('Error en la solicitud AJAX');
			}
		});
	});
	// Eliminar Tarea
	$('.btnDeleteChecklist').on('click', function(e){
		let dataId = $(this).attr('data-id');
		
		let formData = new FormData();
		formData.append('accion', 'deleteChecklist');
		formData.append('id', dataId);

		$.ajax({
			url: rutaAjax+"app/Ajax/MaquinariaTipoAjax.php",
			data: formData,
			method: 'POST',
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
			success: function(response){
				if (!response.error) {
					Swal.fire({
						icon: 'success',
						title: 'Éxito',
						text: response.message
					});
					// Borrar la fila que activó el botón
					$(e.target).closest('tr').remove();
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: response.message
					});
				}
			},
			error: function(xhr, status, error){
				console.error(xhr);
				alert('Error en la solicitud AJAX');
			}
		});
	});

	let agregandoCatalogo = false;

	let campoSeccion = document.getElementById('seccion');

	$(campoSeccion).on('change', function (e) {

		if ( agregandoCatalogo ) return;
	
		let atributo = campoSeccion.querySelector('option[value="'+ this.value +'"]').getAttribute('data-select2-tag');

		// if ( atributo ) $('#btnAddModeloId').removeAttr('disabled');
		if ( atributo && campoSeccion.value != '' ) $('#btnAddSectionId').removeAttr('disabled');
		else $('#btnAddSectionId').attr('disabled','disabled');

	});

	$('#btnAddSectionId').on('click', function (e) {
	
		$('#btnAddSectionId').attr('disabled','disabled');
		$(campoSeccion).attr('disabled','disabled');

		agregandoCatalogo = true;
		ajaxEnviar(campoSeccion, rutaAjax+'app/Ajax/ChecklistMaquinariaAjax.php');

	});

	function ajaxEnviar(objetoCampo, rutaUrl){

		let token = $('input[name="_token"]').val();
		var nombreValor = objetoCampo.value;

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("descripcion", nombreValor);
		datos.append("accion", "agregarSeccion");

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