// $(function(){

	/* Corrigiendo sidebar en móviles */
	// if(window.matchMedia("(max-width:767px)").matches){
	 
	//      $("body").removeClass('sidebar-collapse');
	 
	// }else{
	 
	//     $("body").addClass('sidebar-collapse');
	 
	// }

	/*=============================================
	SideBar Menu
	=============================================*/

	// $('.sidebar-menu').tree()

	// Si existe un elemento (div) 'msgToast' en el cuerpo del HTML se crea
	const rutaAjax = document.querySelector('base').getAttribute('href');
	const LENGUAJE_DT = {
		"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "Ningún dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
			"sFirst":    "Primero",
			"sLast":     "Último",
			"sNext":     "Siguiente",
			"sPrevious": "Anterior"
		},
		"oAria": {
			"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}
	};

	let elementMsgToast = document.getElementById("msgToast");
	if ( elementMsgToast != null ) {

		let clase = elementMsgToast.getAttribute('clase');
		let titulo = elementMsgToast.getAttribute('titulo');
		let subTitulo = elementMsgToast.getAttribute('subtitulo');
		let mensaje = elementMsgToast.getAttribute('mensaje');

		if ( clase == null ) clase = 'bg-info';
		if ( titulo == null ) titulo = 'Información';
		if ( subTitulo == null ) subTitulo = 'Info';
		if ( mensaje == null ) mensaje = '';

		$(document).Toasts('create', {
			class: clase,
			title: titulo,
			subtitle: subTitulo,
			body: mensaje
		})

	}

	/*=============================================
	Data Table
	=============================================*/

	// Realiza la petición para llenar el DataTable
	function fAjaxDataTable( rutaAjax, idTabla, parametros = {} ) {

	  	fetch( rutaAjax, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			//mode: 'cors', // no-cors, *cors, same-origin
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			//credentials: 'same-origin', // include, *same-origin, omit
			headers: {
			'Content-Type': 'application/json'
			},
			//redirect: 'follow', // manual, *follow, error
			//referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
			// body: JSON.stringify(data) // body data type must match "Content-Type" header
	  	} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

		    $(idTabla).DataTable({

		      autoWidth: false,
		      // "lengthChange": false,
		      // "responsive": true,
		      responsive: ( parametros.responsive === undefined ) ? true : parametros.responsive,
		      data: data.datos.registros,
		      columns: data.datos.columnas,
		      
		              // "render": function ( data, type, row, meta ) {
		              //         return "<a href='<?=Route::names(empresas.edit, "+data+")?>' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>"
		              //       } } ],
		          
		      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
		      buttons: [{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }],

		      language: LENGUAJE_DT,
		      aaSorting: [],

		    }).buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)'); // $(idTabla).DataTable({
		    // }).buttons().container().appendTo(idTabla+'_wrapper .col-md-6:eq(0)'); // $(idTabla).DataTable({

		}); // .then( data => {

	} // function fAjaxDataTable( rutaAjax, idTabla ) {

	// $("#example1").DataTable({
	//   "responsive": true, "lengthChange": false, "autoWidth": false,
	//   "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
	// }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

	$(".tablas1").DataTable({
		"autoWidth": false,
		// "lengthChange": false,
		"responsive": true,
             
		"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],

		"language": {

			"sProcessing":     "Procesando...",
			"sLengthMenu":     "Mostrar _MENU_ registros",
			"sZeroRecords":    "No se encontraron resultados",
			"sEmptyTable":     "Ningún dato disponible en esta tabla",
			"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
			"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
			"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
			"sInfoPostFix":    "",
			"sSearch":         "Buscar:",
			"sUrl":            "",
			"sInfoThousands":  ",",
			"sLoadingRecords": "Cargando...",
			"oPaginate": {
			"sFirst":    "Primero",
			"sLast":     "Último",
			"sNext":     "Siguiente",
			"sPrevious": "Anterior"
			},
			"oAria": {
				"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
				"sSortDescending": ": Activar para ordenar la columna de manera descendente"
			}

		},
		
		'aaSorting': [],

	}).buttons().container().appendTo('#DataTables_Table_0_wrapper .col-md-6:eq(0)');

	$(".tablaSimple").DataTable({

		"language": {

			"sProcessing":     "Procesando...",
			"sLengthMenu":     "Mostrar _MENU_ registros",
			"sEmptyTable":     "Ningún dato disponible en esta tabla",
			"sInfoPostFix":    "",
			"sUrl":            "",
			"sInfoThousands":  ",",
			"sLoadingRecords": "Cargando..."
		},

		'aaSorting': [],
	  	'paging': false,
	  	'ordering': false,
	  	'info': false,
	  	'searching': false

	});

	/*=============================================
	 //iCheck for checkbox and radio inputs
	=============================================*/

	// $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
	//   checkboxClass: 'icheckbox_minimal-blue',
	//   radioClass   : 'iradio_minimal-blue'
	// })

	// // $('input').iCheck({
	// $('input[type="checkbox"].square, input[type="radio"].square').iCheck({
	// 	checkboxClass: 'icheckbox_square-blue',
	// 	radioClass: 'iradio_square-blue'
	//	// increaseArea: '20%' // optional
	//});

	/*=============================================
	 //input Mask
	=============================================*/

	//Datemask dd/mm/yyyy
	// $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
	//Datemask2 mm/dd/yyyy
	// $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
	//Money Euro
	// $('[data-mask]').inputmask()

	$(".campoConDecimal").on("keypress keyup", function (event) {
		$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
	        event.preventDefault();
	    }
	});

	$(".tablaDetalle").on("keypress keyup", ".campoConDecimal", function(event) {
		$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
	        event.preventDefault();
	    }
	});

	$(".campoSinDecimal").on("keypress keyup", function (event) {
		$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
		    event.preventDefault();
		}
	});

	$(".tablaDetalle").on("keypress keyup", ".campoSinDecimal", function(event) {
		$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
		    event.preventDefault();
		}
	});

	$(".campoConDecimal").on("blur", function (event) {
		let decimales = this.getAttribute('decimales');
		if ( decimales === null ) decimales = 2;
		// $(this).val( number_format( $(this).val(), 2 ) );
		$(this).val( number_format( $(this).val(), decimales ) );
	});

	$(".tablaDetalle").on("change", ".campoConDecimal", function(event) {
		$(this).val( number_format( $(this).val(), 2 ) );
	});
	const TOASTS_DELAY = 9000;

	function crearToast(clase = null, titulo = null, subTitulo = null, mensaje = null) {
		if ( clase == null ) clase = 'bg-info';
  		if ( titulo == null ) titulo = 'Información';
  		if ( subTitulo == null ) subTitulo = 'Info';
  		if ( mensaje == null ) mensaje = '';

		$(document).Toasts('create', {
			autohide: true,
			delay: TOASTS_DELAY,
			class: clase,
			title: titulo,
			subtitle: subTitulo,
			body: mensaje
		});
	}
	function number_format(amount, decimals) {

	    amount += ''; // por si pasan un numero en vez de un string
	    amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

	    decimals = decimals || 0; // por si la variable no fue fue pasada

	    // si no es un numero o es igual a cero retorno el mismo cero
	    if (isNaN(amount) || amount === 0) 
	        return parseFloat(0).toFixed(decimals);

	    // si es mayor o menor que cero retorno el valor formateado como numero
	    amount = '' + amount.toFixed(decimals);

	    var amount_parts = amount.split('.'),
	        regexp = /(\d+)(\d{3})/;

	    while (regexp.test(amount_parts[0]))
	        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

	    return amount_parts.join('.');
	}

	function fNumeroMes(nombreMes) {
		mes = nombreMes.toLowerCase();

	    switch (mes) {
		    case "enero":
		        return 1;
		        break;
		    case "febrero":
		        return 2;
		        break;
		    case "marzo":
		        return 3;
		        break;
		    case "abril":
		        return 4;
		        break;
		    case "mayo":
		        return 5;
		        break;
		    case "junio":
		        return 6;
		        break;
		    case "julio":
		        return 7;
		        break;
		    case "agosto":
		        return 8;
		        break;
		    case "septiembre":
		        return 9;
		        break;
		    case "octubre":
		        return 10;
		        break;
		    case "noviembre":
		        return 11;
		        break;
		    case "diciembre":
		        return 12;
		        break;
	        default:
	            return null;
	    }
	}

	function fNombreMes(numeroMes)
	{		
	    switch (numeroMes) {
		    case 1:
		        return "Enero";
		        break;
		    case 2:	    
		        return "Febrero";
		        break;
		    case 3:
		        return "Marzo";
		        break;
		    case 4:
		        return "Abril";
		        break;
		    case 5:
		        return "Mayo";
		        break;
		    case 6:
		        return "Junio";
		        break;
		    case 7:
		        return "Julio";
		        break;
		    case 8:
		        return "Agosto";
		        break;
		    case 9:
		        return "Septiembre";
		        break;
		    case 10:
		        return "Octubre";
		        break;
		    case 11:
		        return "Noviembre";
		        break;
		    case 12:
		        return "Diciembre";
		        break;
	        default:
	            return null;
	    }
	}

	function fFechaLarga(fecha) // El formato se debe recibir confome a la case date() de JavaScript
	{
		var dia = fecha.getDate();
		if ( dia < 10 ) {
			dia = "0" + dia;
		}
		var mes = fecha.getMonth() + 1;
		mes = fNombreMes(mes);
		var year = fecha.getFullYear();

		var fechaLarga = dia + "/" + mes + "/" + year;

		return fechaLarga;
	}

	function fFecha(fecha){ // recibe una fecha en formato dia, mes y año
		// Separar la cadena en día, mes y año
		let partesFecha = fecha.split(/\/|-/);
		let dia = parseInt(partesFecha[0], 10);
		let mes = fNumeroMes(partesFecha[1])-1; // Restar 1 al mes porque en JavaScript los meses van de 0 a 11
		let anio = parseInt(partesFecha[2], 10);
	
		// Crear un nuevo objeto Date
		let nuevaFecha = new Date(anio, mes, dia);
	
		return nuevaFecha;
	}

	/* Desactivar autocompletado en formularios */
	$('form').attr('autocomplete','off');

// });

// Esto va en el HTML
// <button type="button" class="btn btn-warning swalDefaultWarning">
// 	Launch Warning Toast
// </button>
// // $(function() {
// var Toast = Swal.mixin({
// 	toast: true,
// 	position: 'top-end',
// 	showConfirmButton: false,
// 	timer: 3000
// });

// $('.swalDefaultWarning').click(function() {
// 	Toast.fire({
// 		icon: 'warning',
// 		title: 'La empresa fue actualizada correctamente.'
// 	})
// });
// // });
