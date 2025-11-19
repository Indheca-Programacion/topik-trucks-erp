<?php

	include "vistas/modulos/plantilla/encabezado.php";

  use App\Route;

?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Maquinarias

      <small>Listado</small>
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="<?php echo Route::routes('inicio'); ?>"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Maquinarias</li>
    
    </ol>

  </section>

  <section class="content">
    
    <?php if ( !is_null(flash()) ) : ?>
      <div class="alert <?php echo flashAlertClass(); ?>"><?php echo flash(); ?></div>
    <?php endif; ?>

    <div class="box">

      <div class="box-header with-border">

        <h3 class="box-title">Listado de Maquinarias</h3>
  
        <a href="<?php echo Route::names('maquinarias.create'); ?>" class="btn btn-primary pull-right">
          
          <i class="fa fa-plus"></i> Crear maquinaria

        </a>

      </div>

      <div class="box-body">
        
       <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
         
        <thead>
         
         <tr>
           
           <th style="width:10px">#</th>
           <th>Num. Económico</th>
           <th>Num. Factura</th>
           <th>Descripcion</th>
           <th>Marca</th>
           <th>Modelo</th>
           <th>Serie</th>
           <th>Color</th>
           <th>Estatus</th>
           <th>Ubicación</th>
           <th>Almacén</th>
           <th>Acciones</th>

         </tr> 

        </thead>

        <tbody>

        <?php

        $item = null;
        $valor = null;

        foreach ($maquinarias as $key => $value){
  
          echo ' <tr>
                  <td>'.($key+1).'</td>
                  <td class="text-uppercase">'.fString($value["numeroEconomico"]).'</td>
                  <td class="text-uppercase">'.fString($value["numeroFactura"]).'</td>
                  <td class="text-uppercase">'.fString($value["descripcion"]).'</td>
                  <td class="text-uppercase">'.fString($value["marcas.descripcion"]).'</td>
                  <td class="text-uppercase">'.fString($value["modelos.descripcion"]).'</td>
                  <td class="text-uppercase">'.fString($value["serie"]).'</td>
                  <td class="text-uppercase">'.fString($value["colores.descripcion"]).'</td>
                  <td class="text-uppercase">'.fString($value["estatus.descripcion"]).'</td>
                  <td class="text-uppercase">'.fString($value["ubicaciones.descripcion"]).'</td>
                  <td class="text-uppercase">'.fString($value["almacenes.descripcion"]).'</td>

                  <td>
                        
                    <a href="' . Route::names('maquinarias.edit', $value["id"]) .'" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>

                    <form method="POST" action="' . Route::names("maquinarias.destroy", $value["id"]) .'" style="display: inline;">
                      <input type="hidden" name="_method" value="DELETE">
                      <input type="hidden" name="_token" value="' . createToken() . '">
                      <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(' . chr(39) .'¿Estás seguro de querer eliminar esta Maquinaria?'. chr(39) . ')">
                        <i class="fa fa-times"></i>
                      </button>
                    </form>

                  </td>

                </tr>';
        }

        ?> 

        </tbody>

       </table>

      </div>

    </div>

  </section>

</div>

<?php
	
	// $archivoJS = "vistas/js/fileName.js";
	include "vistas/modulos/plantilla/pie-de-pagina.php";

?>