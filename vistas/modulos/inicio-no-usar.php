<?php

  var_dump($module);
  die();

  include "vistas/modulos/plantilla/encabezado.php";

  die();
  
  use App\Controllers\Autorizacion;

?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Tablero
      
      <small>Panel de Control</small>
    
    </h1>

    <ol class="breadcrumb">
      
      <!-- <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li> -->
      <li><i class="fa fa-dashboard active"></i> Inicio</li>
      
      <!-- <li class="active">Tablero</li> -->
    
    </ol>

  </section>

  <section class="content">

    <div class="row">
      
    <?php

    // || Autorizacion::perfil($usuarioAutenticado, "vendedor")
    if ( $usuarioAutenticado->checkAdmin() || Autorizacion::perfil($usuarioAutenticado, "gerente") ) {

      include "inicio/cajas-superiores.php";

    }

    ?>

    </div> 

     <div class="row">
       
        <div class="col-lg-12">

          <?php

          if ( $usuarioAutenticado->checkAdmin() ) {
          
           // include "reportes/grafico-costos.php";

          }

          ?>

        </div>

        <div class="col-lg-6">

          <?php

          if ( $usuarioAutenticado->checkAdmin() ) {
          
           // include "reportes/productos-mas-vendidos.php";

          }

          ?>

        </div>

         <div class="col-lg-6">

          <?php

          if ( $usuarioAutenticado->checkAdmin() ) {
          
           // include "inicio/productos-recientes.php";

         }

          ?>

        </div>

         <div class="col-lg-12">
           
          <?php

          if ( !$usuarioAutenticado->checkAdmin() ) {

             echo '<div class="box box-success">

             <div class="box-header">

             <h1>Bienvenid@ ' .fString($usuarioAutenticado->nombreCompleto).'</h1>

             </div>

             </div>';

          }

          ?>

         </div>

     </div>

  </section>
 
</div>

<?php
	
  $archivoJS = "vistas/bower_components/select2/dist/js/select2.full.min.js";
  $comandoJS = "$('.select2').select2({
            tags: false
        });
        $('.select2Add').select2({
            tags: true
        });";
	include "vistas/modulos/plantilla/pie-de-pagina.php";

?>
