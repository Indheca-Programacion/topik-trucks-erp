$(function(){

  // Consultar los registros de Empresas
  $.ajax({
    url: rutaAjax+"app/Ajax/EmpresaAjax.php",
    method: "GET", // "POST"
    // data: formData,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json"
  })
  .done(function(respuesta) {
    console.log(respuesta);

    $(".tablas1").DataTable({

        "autoWidth": false,
        // "lengthChange": false,
        "responsive": true,
        data: respuesta,
        columns: [ { "data": "consecutivo" },
              { "data": "razonSocial" },
              { "data": "nombreCorto" },
              { "data": "rfc" },
              { "data": "municipio" },
              { "data": "estado" },
              { "data": "pais" },
              { "data": "acciones" } ],
                // "render": function ( data, type, row, meta ) {
                //         return "<a href='<?=Route::names(empresas.edit, "+data+")?>' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>"
                //       } } ],
            
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

  })
  .fail(function(error) {
    console.log(error);
  })
  .always(function() {
  });

  // Confirmar la eliminación de la Empresa
  $("table tbody").on("click", "button.eliminar", function (e) {

    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents('form');

    Swal.fire({
      title: '¿Estás Seguro de querer eliminar esta Empresa (Razón Social: '+folio+') ?',
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

});