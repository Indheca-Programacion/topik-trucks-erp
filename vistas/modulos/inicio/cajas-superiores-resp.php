<?php

use App\Route;

?>

<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-aqua">
    
    <div class="inner">
      
      <h3>$<?php echo number_format($costoProductos["costoTotal"], 2); ?></h3>

      <p>Productos</p>
    
    </div>
    
    <div class="icon">

      <i class="fa fa-usd"></i>
    
    </div>
    
    <a href="<?php echo Route::names('productos.index'); ?>" class="small-box-footer">
      
      Más info <i class="fa fa-arrow-circle-right"></i>
    
    </a>

  </div>

</div>

<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-green">
    
    <div class="inner">
    
      <h3><?php echo number_format($totalMuebles); ?></h3>

      <p>Muebles</p>
    
    </div>
    
    <div class="icon">
    
      <i class="fa fa-laptop"></i>
    
    </div>
    
    <a href="<?php echo Route::names('bienes.index'); ?>" class="small-box-footer">
      
      Más info <i class="fa fa-arrow-circle-right"></i>
    
    </a>

  </div>

</div>

<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-yellow">
    
    <div class="inner">
    
      <h3><?php echo number_format($totalInmuebles); ?></h3>

      <p>Inmuebles</p>
  
    </div>
    
    <div class="icon">
    
      <i class="fa fa-building-o"></i>
    
    </div>
    
    <a href="<?php echo Route::names('inmuebles.index'); ?>" class="small-box-footer">

      Más info <i class="fa fa-arrow-circle-right"></i>

    </a>

  </div>

</div>

<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-red">
  
    <div class="inner">
    
      <h3><?php echo number_format($totalTransportes); ?></h3>

      <p>Equipos de Transporte</p>
    
    </div>
    
    <div class="icon">
      
      <i class="fa fa-car"></i>
    
    </div>
    
    <a href="<?php echo Route::names('transportes.index'); ?>" class="small-box-footer">
      
      Más info <i class="fa fa-arrow-circle-right"></i>
    
    </a>

  </div>

</div>
