// $(function(){

	function fDataTable(idTabla){

		$(idTabla).DataTable({

	      // "autoWidth": false,
	      // "lengthChange": false,
	      // "responsive": false,
	      responsive: false,
	      paging: false,
		  searching: false,
	      // data: data.datos.registros,
	      // columns: data.datos.columnas,

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

	    // }).buttons().container().appendTo(idTabla+'_wrapper .col-md-6:eq(0)'); // $(idTabla).DataTable({
	    }).buttons().container().appendTo('#requisiciones .card-tools'); // $(idTabla).DataTable({

	    $(idTabla).parent().addClass( "table-responsive" );

  		let colButtons = document.querySelector(idTabla+'_wrapper').firstChild.firstChild;
  		$( colButtons ).removeClass('col-md-6');

	}

// });