<?php
  use App\Controllers\Autorizacion;
  use App\Route;
?>

<div class="content-wrapper">

  <section class="content-header">
    
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <!-- font-weight-light -->
            <h1>Tablero <small class="font-weight-light">Panel de Control</small></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class="fas fa-tachometer-alt"></i> Inicio</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->

  </section>

  <section class="content">

    <div class="row">      
    <?php
      include "inicio/cajas-superiores.php";
    ?>
    </div> 

    <div class="row">
      
      <?php if ( count($horasTrabajadasCentro) ) : ?>
      <div class="col-lg-8">
        <?php
          include "reportes/grafico-horas-trabajadas.php";
        ?>
      </div>
      <?php endif; ?>

      <div class="col-md-4">
        <?php
          include "reportes/tareas.php";
        ?>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-comments"></i> 
              Asistente
            </h3>
          </div>
          <div class="card-body">
            <div id="chat-assistant">
              <p><strong>¿En qué puedo ayudarte?</strong></p>
              <?php if ( $usuarioAutenticado->checkPermiso("estimaciones-aut") || $usuarioAutenticado->checkAdmin()) : ?>
                <a class="btn btn-success btn-block mb-2" href="<?= Route::names('estimaciones.index') ?>">Mostrar Estimaciones sin autorizar</a>
              <?php endif; ?>
              <?php if ( $usuarioAutenticado->checkPerfil("pagos") || $usuarioAutenticado->checkAdmin()) : ?>
                <a class="btn btn-warning btn-block mb-2" href="<?= Route::names('pagos.index') ?>">Subir comprobantes de pago</a>
              <?php endif; ?>
              <?php if ( $usuarioAutenticado->checkPerfil("almacén") || $usuarioAutenticado->checkAdmin()) : ?>
                <a class="btn btn-danger btn-block mb-2" href="<?= Route::names('requisiciones.index')."?verPendientesAlmacen" ?>">Ver Requisiciones Pendientes</a>
              <?php endif; ?>
              <?php if ( $usuarioAutenticado->checkPermiso("inventarios-auth") || $usuarioAutenticado->checkAdmin()) : ?>
                <a class="btn btn-info btn-block mb-2" href="<?= Route::names('inventarios-pendientes.index') ?>">Ver Inventarios Pendientes de Autorizacion</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <?php
          include "reportes/maquinarias-sin-cargas-de-combustible.php";
        ?>
      </div>

      <div class="col-md-6">
      <?php
        include "reportes/maquinarias-con-servicio-proximo.php";
      ?>
      </div>

    </div>

  </section>
 
</div>
