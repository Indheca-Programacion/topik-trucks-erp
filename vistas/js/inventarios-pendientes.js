$(function(){

	let tableList = document.getElementById('tablaInventariosPendientes');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/InventarioPendientesAjax.php', '#tablaInventariosPendientes');

	// Confirmar la eliminación de la Información Técnica
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Inventario Pendiente (Folio: '+folio+') ?',
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

	$('#tablaInventariosPendientes').on("click", "button.autorizar", function (e) {
		e.preventDefault();
		var id = $(this).data('id');
		var token = $(this).data('token');
		var folio = $(this).data('folio');
		Swal.fire({
			title: '¿Estás Seguro de querer autorizar este Inventario Pendiente (Folio: '+folio+') ?',
			text: "¡No podrás revertir esto!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#28a745',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, autorizar!',
			cancelButtonText:  'No!'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: rutaAjax+'app/Ajax/InventarioPendientesAjax.php',
					method: 'POST',
					data: { accion: 'autorizar', id: id, _token: token },
					dataType: 'json',
					success: function(respuesta){
						if(respuesta.error){
							Swal.fire('Error al autorizar', respuesta.mensaje, 'error');
						} else {
							Swal.fire('¡Inventario Autorizado!', respuesta.mensaje, 'success').then((result) => {
								if (result.isConfirmed) {
									location.reload();
								}
							});
						}
					},
					error: function(){
						Swal.fire('Tarea no realizada', 'Ha ocurrido un error en el proceso, favor de intentar nuevamente.', 'error');
					}
				});
			}
		});
	});

	

					
});