<?php
use App\Route;
?>

<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-info">
        <div class="inner">
            <h3><?php echo $cantidadMaquinarias; ?></h3>
            <p>Maquinarias</p>
        </div>    
        <div class="icon">
            <i class="fas fa-truck"></i>
        </div>
        <a href="<?php echo Route::names('maquinarias.index'); ?>" class="small-box-footer">
            Más info <i class="fa fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <!-- small box -->
    <div class="small-box bg-green">
        <div class="inner">
            <h3><?php echo $cantidadServicios; ?></h3>
            <p>Servicios</p>
        </div>
        <div class="icon">
            <i class="fas fa-tools"></i>
        </div>
        <a href="<?php echo Route::names('servicios.index'); ?>" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <!-- small box -->
    <div class="small-box bg-warning">
        <div class="inner">
            <h3><?php echo 0;//SE quieto la cantidad de requisiciones ?></h3>
            <p>Presupuestos</p>
        </div>
        <div class="icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <a href="#" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <!-- small box -->
    <div class="small-box bg-danger">
        <div class="inner">
            <h3><?php echo $cantidadGastos; ?></h3>
            <p>Gastos</p>
        </div>
        <div class="icon">
            <i class="fas fa-comment-dollar"></i>
        </div>
        <a href="<?php echo Route::names('gastos.index'); ?>" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>
