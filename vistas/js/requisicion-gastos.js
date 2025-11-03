$(function(){
	const TIEMPO_DESCARGA = 350;
	let parametrosTableList = { responsive: true };
    let tableList = document.getElementById('tablaRequisiciones');

	function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

		fetch( rutaAjax, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			$(idTabla).DataTable({

				autoWidth: false,
				responsive: ( parametros.responsive === undefined ) ? true : parametros.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,

		        createdRow: function (row, data, index) {
		        	if ( data.colorTexto != '' ) $('td', row).eq(3).css("color", data.colorTexto);
		        	if ( data.colorFondo != '' ) $('td', row).eq(3).css("background-color", data.colorFondo);
		        },

				buttons: [{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }],

				language: LENGUAJE_DT,
				aaSorting: [],

			}).buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)'); // $(idTabla).DataTable({
		}); // .then( data => {

	} // function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {
  
    // LLamar a la funcion fAjaxDataTable() para llenar el Listado 
	if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/RequisicionGastoAjax.php', '#tablaRequisiciones', parametrosTableList);
  
    // Confirmar la eliminación del Proveedor
    $(tableList).on("click", "button.eliminar", function (e) {
  
      e.preventDefault();
      var folio = $(this).attr("folio");
      var form = $(this).parents('form');
  
      Swal.fire({
        title: '¿Estás Seguro de querer eliminar esta Requisicion (Nombre: '+folio+') ?',
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

    // Habilitar observaciones al cambiar de estatus
	$("#servicioEstatusId").change(function(){
		let actualServicioEstatusId = $('#actualServicioEstatusId').val();
		if ( actualServicioEstatusId === '' ) return;

		let observacion = document.getElementById('observacion');
		if ( observacion === null ) return;

		if ( actualServicioEstatusId == this.value ) {
			// let observacion = document.getElementById('observacion');
			$(observacion).prop('disabled', true);
			observacion.parentElement.parentElement.parentElement.classList.add("d-none");
		} else {
			// let observacion = document.getElementById('observacion');
			if ( $(observacion).prop('disabled') ) {
				$(observacion).prop('disabled', false);
				observacion.parentElement.parentElement.parentElement.classList.remove("d-none");
			}
		}
	})
  
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

    // Confirmar la eliminación de los Archivos
	$("div.subir-ordenes, div.subir-comprobantes, div.subir-facturas, div.subir-cotizaciones, div.subir-vales").on("click", "i.eliminarArchivo", function (e) {

		let btnEliminar = this;
	    // let archivoId = $(this).attr("archivoId");
	    let folio = $(this).attr("folio");

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Archivo (Folio: '+folio+') ?',
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

		let archivoId = $(btnEliminar).attr("archivoId");

		// $(btnEliminar).prop('disabled', true);

		let token = $('input[name="_token"]').val();
		let requisicionId = $('input#requisicionId').val();

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarArchivo");
		datos.append("archivoId", archivoId);
		datos.append("requisicionId", requisicionId);

		$.ajax({
		    url: rutaAjax+"app/Ajax/RequisicionGastoAjax.php",
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

    /*==============================================================
	Abrir el input al presionar el botón Cargar Comprobantes de Pago
	==============================================================*/
	$("#btnSubirComprobantes").click(function(){
		document.getElementById('comprobanteArchivos').click();
	})

    /*========================================================
 	Validar tipo y tamaño de los archivos Comprobantes de Pago
 	========================================================*/
 	$("#comprobanteArchivos").change(function() {

        // $("div.subir-comprobantes span.lista-archivos").html('');
        let archivos = this.files;
        if ( archivos.length == 0) return;

        let error = false;

        for (let i = 0; i < archivos.length; i++) {

           let archivo = archivos[i];
           
           /*==========================================
           VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
           ==========================================*/
           
           if ( archivo["type"] != "application/pdf" ) {

               error = true;

               // $("#comprobanteArchivos").val("");
               // $("div.subir-comprobantes span.lista-archivos").html('');

               Swal.fire({
                 title: 'Error en el tipo de archivo',
                 text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
                 icon: 'error',
                 confirmButtonText: '¡Cerrar!'
               })

           } else if ( archivo["size"] > 4000000 ) {

               error = true;

               // $("#comprobanteArchivos").val("");
               // $("div.subir-comprobantes span.lista-archivos").html('');

               Swal.fire({
                 title: 'Error en el tamaño del archivo',
                 text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
                 icon: 'error',
                 confirmButtonText: '¡Cerrar!'
               })

           }
           // else {

               // $("div.subir-comprobantes span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

           // }

        }

        if ( error ) {
            $("#comprobanteArchivos").val("");

            return;
        }

        for (let i = 0; i < archivos.length; i++) {

            let archivo = archivos[i];

            $("div.subir-comprobantes span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

        }

       let cloneElementArchivos = this.cloneNode(true);
       cloneElementArchivos.removeAttribute('id');
       cloneElementArchivos.name = 'comprobanteArchivos[]';
       $("div.subir-comprobantes").append(cloneElementArchivos);

   }) // $("#comprobanteArchivos").change(function(){

   // Descargar Comprobantes de Pago
	$("#btnDescargarComprobantes").click(function(event) {

		event.preventDefault();

		let btnDescargarComprobantes = this;
		let requisicionId = $('#requisicionId').val();
		
		$.ajax({
			url: `${rutaAjax}requisiciones/${requisicionId}/download/comprobantes`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarComprobantes.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				// btnDescargarComprobantes.parentElement.appendChild(link);
				// link.click();
				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarComprobantes.disabled = false;
		});

	})

    $('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});
	$('.select2Add').select2({
		tags: true
		// ,theme: 'bootstrap4'
	});
});
