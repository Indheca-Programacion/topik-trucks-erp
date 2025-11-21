$(function(){

	let tableList = document.getElementById('tablaGeneradores');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/EstimacionesAjax.php', '#tablaGeneradores');

	// Confirmar la eliminación del Generador
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Estatus (Descripción: '+folio+') ?',
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

	$(".select2").select2({
		width: '100%'
	});

	$("#btnMandarCorregir").on("click", function(){
		let token = $("#token").val();

		Swal.fire({
			title: '¿Estás seguro de querer mandar a corregir esta Estimación?',
			text: "No podrás revertir esta acción",
			icon: 'warning',
			input: 'textarea',
			inputLabel: 'Observación',
			inputPlaceholder: 'Escribe la observación aquí...',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Sí, mandar a corregir',
			cancelButtonText: 'No, cancelar',
			inputValidator: (value) => {
				if (!value) {
					return 'Debes ingresar una observación';
				}
			}
		}).then((result) => {
			if (result.isConfirmed) {
				let generadorId = $("#generadorId").val();
				let observacion = result.value;
				let datos = new FormData();
				datos.append("generadorId", generadorId);
				datos.append("_token", token);
				datos.append("accion", "mandarCorregir");
				datos.append("observacion", observacion);

				$.ajax({
					url: rutaAjax + 'app/Ajax/EstimacionesAjax.php',
					method: "POST",
					data: datos,
					cache: false,
					contentType: false,
					processData: false,
					dataType: "json",
					success: function (respuesta) {
						if ( !respuesta.error ) {
							Swal.fire({
								title: "¡CORRECTO!",
								text: "La Estimación ha sido mandada a corregir",
								icon: "success",
								confirmButtonText: "Cerrar",
								closeOnConfirm: false
							}).then((result) => {
								if (result.isConfirmed) {
									location.reload();
								}
							});
						} else {
							Swal.fire({
								title: "¡ERROR!",
								text: respuesta.mensaje,
								icon: "error",
								confirmButtonText: "Cerrar",
								closeOnConfirm: false
							});
						}
					},
					error: function (error) {
						Swal.fire({
							title: "¡ERROR!",
							text: "Ocurrió un error al procesar la solicitud",
							icon: "error",
							confirmButtonText: "Cerrar",
							closeOnConfirm: false
						});
					}
				});
			}
		});

	});

});